<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Remesh Babu S  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Encounter\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Encounter\Model\Encounter;
use Encounter\Form\EncounterForm;
use Zend\View\Model\JsonModel;
use HTML2PDF;
//use DOMPDFModule\View\Model\PdfModel;

class EncounterController extends AbstractActionController
{
		protected $encounterTable;
    
		// Index page
    public function indexAction()
    {
				global $phpgacl_location;
				global $viewmode;
				global $pid;
				global $encounter;
				$view 	= '';
				$data 	= array();
				$row 	= array();
				$authUser = $_SESSION['authUser'];
				require_once ($phpgacl_location.'/../library/lists.inc');

				$form = new EncounterForm();
				
				// if global viewmode is set
				if ($viewmode) {
						$request = $this->getRequest();
						if($request->isGet()){
								$id 		= $request->getQuery('id');
								$issue 		= $request->getQuery('issue');
								if (empty($issue)) $issue = '';
								$thistype 	= $request->getQuery('thistype');
								if (empty($thistype)) $thistype = '';
								$helper 	= $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
								$result 	= $helper->getEncounter($id);
								$data = array(
												'viewmode'			=> $viewmode,
												'pcId'  			=> $result['pc_catid'],
												'encounter'			=> $result['encounter'],
												'defaultFacility'	=> $result['facility_id'],
												'billingFacility'	=> $result['billing_facility'],
												'sensitivity'		=> $result['sensitivity'],
												'requestIssue'		=> $request->getQuery('issue'),
										);
								if ($result['sensitivity'] && !acl_check('sensitivities', $result['sensitivity'])) {
										$view = 'N';
								}
						} else if ($request->isPost()) {
								$issue 		= $request->getPost('issue');
								if (empty($issue)) $issue = '';
								$thistype 	= $request->getPost('thistype');
								if (empty($thistype)) $thistype = '';
						}
						if ($issue) {
								//$row = $this->getEncounterTable()->listIssue($issue);
						} else  if ($thistype) {
								$row['type'] = $thistype;
						}
				}
				
				// Collect data from data base by help of helper
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$visitCategory = $helper->getVisitCategory($data);
				$form->get('visitCategory')->setValueOptions($visitCategory);
				
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$providers = $helper->getProviders();
				$form->get('provider')->setValueOptions($providers);
					
				$defaultFacility = $helper->getDefaultFacility($authUser);
				$data ['defaultFacility'] = $defaultFacility;
				$facility = $helper->getFacility($data);
				$form->get('facility')->setValueOptions($facility);
				
				$billingFacility = $helper->getBillingFacility($data);
				$form->get('billingFacility')->setValueOptions($billingFacility);
		
				$data ['opt'] = 'sensitivities';
				$sensitivity = $helper->getSensitivities($data);
				$form->get('sensitivity')->setValueOptions($sensitivity);
					
				$data ['pid'] =  $pid;
				$issues = $helper->getIssues($data,$ISSUE_TYPES);
				$form->get('issues[]')->setValueOptions($issues);
				// Set value to inxed page
				if (!$pid) {
						$view = 'F';
				}
				$index = new ViewModel(array(
							'form' 				=> $form, 
							'view' 				=> $view, 
							'ISSUE_TYPES' => $ISSUE_TYPES,
							'issue' 			=> $issue,
							'row'					=> $row,
						));
				return $index;
    }
    
		// Save the Encounter data
    public function saveDataAction()
    {
				$request = $this->getRequest();
				if ($request->isPost()) {
						$this->getEncounterTable()->saveEncounter($request->getPost());
				}
				return $this->redirect()->toRoute('show');
    }
    
		// View and edit encounter page
    public function showAction()
    {
				global $phpgacl_location;
				global $viewmode;
				global $pid;
				global $encounter;
				$view 		= '';
				$data 		= array();
				$row 			= array();
				$authUser = $_SESSION['authUser'];
				require_once ($phpgacl_location.'/../library/lists.inc');
				require_once ($phpgacl_location.'/../library/encounter.inc');
			
				$form 		= new EncounterForm();
				$request 	= $this->getRequest();
				if($request->isGet()){
					if ($request->getQuery()->enc) {
							$encounter		= $request->getQuery('enc');
					}
				}
				
				// If Global viewmode is set
				if ($viewmode) {
					if($request->isGet()){
						$id 		= $request->getQuery('id');
						$issue 		= $request->getQuery('issue');
						if (empty($issue)) $issue = '';
						$thistype 	= $request->getQuery('thistype');
						if (empty($thistype)) $thistype = '';
						$helper 	= $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
						$result 	= $helper->getEncounter($id);
						$data = array(
								'viewmode'			=> $viewmode,
								'pcId'  			=> $result['pc_catid'],
								'encounter'			=> $result['encounter'],
								'defaultFacility'	=> $result['facility_id'],
								'billingFacility'	=> $result['billing_facility'],
								'sensitivity'		=> $result['sensitivity'],
								'requestIssue'		=> $request->getQuery('issue'),
						);
						if ($result['sensitivity'] && !acl_check('sensitivities', $result['sensitivity'])) {
							$view = 'N';
						}
					} else if ($request->isPost()) {
						$issue 		= $request->getPost('issue');
						if (empty($issue)) $issue = '';
						$thistype 	= $request->getPost('thistype');
						if (empty($thistype)) $thistype = '';
					}
					if ($issue) {
						//$row = $this->getEncounterTable()->listIssue($issue);
					} else  if ($thistype) {
						$row['type'] = $thistype;
					}
				}
				
				$result = $this->getEncounterTable()->listEncounter($encounter);

				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$visitCategory = $helper->getVisitCategory($data);
				$form->get('visitCategory')->setValueOptions($visitCategory);
				
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$providers = $helper->getProviders();
				$form->get('provider')->setValueOptions($providers);
				
				$defaultFacility = $helper->getDefaultFacility($authUser);
				$data ['defaultFacility'] = $defaultFacility;
				$facility = $helper->getFacility($data);
				$form->get('facility')->setValueOptions($facility);
				
				$billingFacility = $helper->getBillingFacility($data);
				$form->get('billingFacility')->setValueOptions($billingFacility);
				
				$data ['opt'] = 'sensitivities';
				$sensitivity = $helper->getSensitivities($data);
				$form->get('sensitivity')->setValueOptions($sensitivity);
				 
				$data ['pid'] =  $pid;
				$issues = $helper->getIssues($data,$ISSUE_TYPES);
				$form->get('issues[]')->setValueOptions($issues);
				
				$data['patient'] = $result[0]['pid'];
				$patient = $helper->getPatientDetails($data);
				
				$vitals	= $helper->getVitals($pid);
				
				// Get Patient Image
				$data['photoCatName'] = $GLOBALS['patient_photo_category_name'];
				$images	= $helper->getPatientImage($data);
				
				// Preview Tab
				$preview = $helper->getPreviewForms($data);
				
				// Facility Details
				$data['facilityID'] = $_SESSION['pc_facility'];
				$facility = $helper->getFacilityDetails($data);
				
				// Employer Details
				$employer = $helper->getEmployerDetails($data);
				
				// Lay Out Data
				$data[0] = 'DEM';
				$data[1] = 'HIS';
				$layOut = $helper->getLayOut($data);
				// Set Encounter ID 
				setencounter($encounter);
				
				//ob_start();

				$index = new ViewModel(array(
						'form' 					=> $form,
						'view' 					=> $view,
						'ISSUE_TYPES' 	=> $ISSUE_TYPES,
						'issue' 				=> $issue,
						'row'						=> $row,
						'mode'					=> 'Edit',
						'visitCategory'	=> $visitCategory,
						'result'				=> $result,
						'patient'				=> $patient,
						'vitals'				=> $vitals,
						'images'				=> $images,
						'includes'			=> $includes,
						'preview' 			=> $preview,
						'facility'			=> $facility,
						'employer'			=> $employer,
						'layOut'				=> $layOut,
				));
				return $index;
    }
		
		// Report Page
		public function reportAction()
		{
				global $pid;

				$request 	= $this->getRequest();
				$tmp = explode(',', $request->getQuery()->data);

				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				
				// Patient Details
				$data['patient'] = $pid;
				$patient = $helper->getPatientDetails($data);
				
				// Get Patient Image
				$data['pid'] = $pid;
				$data['photoCatName'] = $GLOBALS['patient_photo_category_name'];
				$images	= $helper->getPatientImage($data);
				
				// Preview Tab
				// param. @pid
				$data['pid'] = $pid;
				$preview = $helper->getPreviewForms($data);
				
				// Facility Details
				$data['facilityID'] = $_SESSION['pc_facility'];
				$facility = $helper->getFacilityDetails($data);
				
				// Employer Details
				// param. @pid
				$employer = $helper->getEmployerDetails($data);
				
				// Lay Out Data
				//$layOut = $helper->getReportLayout($tmp, $patient, $employer);
				$data[0] = 'DEM';
				$data[1] = 'HIS';
				$layOut = $helper->getLayOut($data);
				$report = new ViewModel(array(
						'patient'				=> $patient,
						'images'				=> $images,
						'preview' 			=> $preview,
						'facility'			=> $facility,
						'employer'			=> $employer,
						'layOut'				=> $layOut,
						'options'				=> $tmp,
				));
				return $report;

		}
		
		// Preview Print
		public function previewPrintAction()
		{
				global $pid;
				$data['pid'] = $pid;
				$request 	= $this->getRequest();
				//$fh = fopen("D:/test.txt","a");
				//fwrite($fh,"data ".print_r($request->getQuery(), 1));
				$preview = $helper->getPreviewForms($data);
				
				// For Ajax Success
				$arr = array("Success");
				$data = new JsonModel($arr);
				return $data;
		}
		
		// Preview Download to PDF
		public function pdfAction()
		{
				global $srcdir;
				global $pid;
				global $encounter;
				global $webserver_root;

				$request 	= $this->getRequest();
				//$formID = $request->getQuery()->formID;
				//$arr  = array();
				$arrFormID  = explode(',', $request->getQuery()->formID);
				//$fh = fopen("D:/test.txt","a");
				//fwrite($fh,"data ".print_r($request->getQuery(), 1));
				//global $PDF_OUTPUT;
				//global $printable;
				//if (function_exists('writeHTML')) { fwrite($fh,"PDF YES"); }else{ fwrite($fh,"PDF NO"); }
				

				
				//$PDF_OUTPUT =  1;
				//$printable =  true;
				
				// Set values for PDF Generate
				$_POST['pdf'] 									= 1;
				$_GET['printable'] 							= 1;
				$_GET['pdf'] 										= 1;
				// Set Values for Report Options
				$_GET['include_demographics'] 	= 'demographics';
				$_GET['include_history'] 				= 'history';
				$_GET['include_insurance'] 			= 'insurance';
				$_GET['include_billing'] 				= 'billing';
				$_GET['include_immunizations'] 	= 'immunizations';
				$_GET['include_notes'] 					= 'notes';
				$_GET['include_transactions'] 	= 'transactions';
				$_GET['include_batchcom'] 			= 'batchcom';
				$_GET['newpatient_' . $formID] 	= $encounter;	
				foreach ($arrFormID as $key => $value) {
					$_GET[$value] 	= $encounter;	
				}
				//$fh = fopen("D:/test.txt","a");
				//fwrite($fh,"data ".print_r($_GET, 1));
				
				include_once($GLOBALS['incdir'] . "/patient_file/report/custom_report.php");
				
				// For Ajax Success
				$arr = array("Success");
				$data = new JsonModel($arr);
				return $data;
		}
		
		// Save Vitals data to the table
    public function saveNoteDataAction()
    {
				$request = $this->getRequest();
				if ($request->isGet()) {
						$this->getEncounterTable()->saveEncounterNote($request->getQuery());
				}
				return array('form' => $form);
    }
		
		// Delete Notes
		public function deleteNoteDataAction()
		{
				$request = $this->getRequest();
				if ($request->isGet()) {
						$this->getEncounterTable()->deleteEncounterNote($request->getQuery());
				}
				return array('form' => $form);
		}
    
		// Check the check box options right side of the encounte edit form
    public function checkAMCAction()
    {
				global $phpgacl_location;
				global $pid;
				global $encounter;
				require_once ($phpgacl_location.'/../library/amc.php');
				$request = $this->getRequest();
    	
				if($request->isPost()){
						$id = $request->getPost('id');
						if ($id == 'PER') {
								$itemAMC = amcCollect("patient_edu_amc", $pid, 'form_encounter', $encounter);
								if (empty($itemAMC)) $itemAMC['empty'] = 'Y';
								$itemAMC['AMCTYPE'] = 'PER';
								$data = new JsonModel($itemAMC);
						}
						if ($id == 'PCS') {
								$itemAMC = amcCollect("provide_sum_pat_amc", $pid, 'form_encounter', $encounter);
								if (empty($itemAMC)) $itemAMC['empty'] = 'Y';
								$itemAMC['AMCTYPE'] = 'PCS';
								$data = new JsonModel($itemAMC);
						}
						if ($id == 'TTC') {
								$itemAMC = amcCollect("med_reconc_amc", $pid, 'form_encounter', $encounter);
								if (empty($itemAMC)) $itemAMC['empty'] = 'Y';
								if (empty($itemAMC['date_completed'])) $itemAMC['date_completed'] = 'empty';
								$itemAMC['AMCTYPE'] = 'TTC';
								$data = new JsonModel($itemAMC);
						}
						return $data;
				}
    }
    
		// Get Providers from the data base by the help of helper
    public function getProvidersAction()
    {
    	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
    	$providers = $helper->getProviders();
    	$data = new JsonModel($providers);
    	return $data;
    }
    
		// Get the Charting Shortcut Menu data from the data base by the help of helper
    public function getChartingMenuAction()
    {
				$request = $this->getRequest();
				if($request->isPost()){
						if ($request->getPost('type') == 1) {
								$data = array(
										'type'		=> 1,
										'state'		=> 1,
										'limit' 	=> 'unlimited',
										'offset'	=> 0,
								);
						}
						if ($request->getPost('type') == 2) {
								$data = array(
										'type'		=> 2,
								);
						}
						if ($request->getPost('type') == 3) {
								$data = array(
										'type'		=> 3,
								);
						}
						$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
						$menu = $helper->getChartingMenuList($data);
						$data = new JsonModel($menu);
						return $data;
				}
    }
		
		// ZEND PDF Test
		/*public function monthlyReportPdfAction()
    {
				$pdf = new PdfModel();
				if (function_exists('setOption')) echo 'ok'; else echo 'no';
        $pdf->setOption('filename', 'monthly-report'); // Triggers PDF download, automatically appends ".pdf"
        $pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
        $pdf->setOption('paperOrientation', 'landscape'); // Defaults to "portrait"
        
        // To set view variables
        $pdf->setVariables(array(
          'message' => 'Hello'
        ));
        
        return $pdf;
    }*/
		
    public function getEncounterTable()
    {
        if (!$this->encounterTable) {
            $sm = $this->getServiceLocator();
            $this->encounterTable = $sm->get('Encounter\Model\EncounterTable');
        }
        return $this->encounterTable;
    } 
}