<?php

namespace Encounter\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Encounter\Model\Encounter;
use Encounter\Form\EncounterForm;
use Zend\View\Model\JsonModel;


class EncounterController extends AbstractActionController
{
    protected $encounterTable;
    
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
    	//if (function_exists('amcCollect')) echo 'ok'; else echo 'no';

    	$form = new EncounterForm();

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
		
		
		$index = new ViewModel(array(
					'form' 			=> $form, 
					'view' 			=> $view, 
					'ISSUE_TYPES' 	=> $ISSUE_TYPES,
					'issue' 		=> $issue,
					'row'			=> $row,
				));
    	return $index;
    	
    }
    
    public function saveDataAction()
    {
    	$request = $this->getRequest();
    	
    	if ($request->isPost()) {
    		$this->getEncounterTable()->saveEncounter($request->getPost());
    	}
    	return $this->redirect()->toRoute('show');
    }
    
    public function showAction()
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
    	require_once ($phpgacl_location.'/../library/encounter.inc');
    	//if (function_exists('amcCollect')) echo 'ok'; else echo 'no';
    	
    	$form = new EncounterForm();
    	$request = $this->getRequest();
    	if($request->isGet()){
    		$encounter		= $request->getQuery('enc');
    	}
    	
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
    	setencounter($encounter);
    	$index = new ViewModel(array(
    			'form' 			=> $form,
    			'view' 			=> $view,
    			'ISSUE_TYPES' 	=> $ISSUE_TYPES,
    			'issue' 		=> $issue,
    			'row'			=> $row,
    			'mode'			=> 'Edit',
    			'visitCategory'	=> $visitCategory,
    			'result'		=> $result,
    			'patient'		=> $patient,
    			'vitals'		=> $vitals,
    	));
    	return $index;
    }
    
    public function saveNoteDataAction()
    {
    	$request = $this->getRequest();
    	if ($request->isGet()) {
    		$this->getEncounterTable()->saveEncounterNote($request->getQuery());
     	}
    	return array('form' => $form);
    	//return $this->redirect()->toRoute('show');
    }
    
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
    
    public function getProvidersAction()
    {
    	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
    	$providers = $helper->getProviders();
    	$data = new JsonModel($providers);
    	return $data;
    }
    
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

        
    public function getEncounterTable()
    {
        if (!$this->encounterTable) {
            $sm = $this->getServiceLocator();
            $this->encounterTable = $sm->get('Encounter\Model\EncounterTable');
        }
        return $this->encounterTable;
    } 
}