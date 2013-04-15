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
// Author:   Jacob T.Paul <jacob@zhservices.com> 
//
// +------------------------------------------------------------------------------+
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Lab;
use Lab\Form\LabForm;
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

use Zend\ZendPdf;

class LabController extends AbstractActionController
{
    protected $labTable;
    
    /**
     * Lab Order Row wise
     */
    
    public function orderAction()
    {
	global $pid;
	$msg = '';
	if ($pid == '' || $_SESSION['encounter'] == '') {
	    $msg = 'N';  
	}
	$form 	= new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider[0][]')->setValueOptions($providers);
	
	$labs = $helper->getLabs('y');
	$form->get('lab_id[0][]')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority[0][]')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status",'pending');
	$form->get('status[0][]')->setValueOptions($status);
	$result = new ViewModel(array('form' => $form, 'message' => $msg,));
	return $result;
    }
    
    public function ordereditAction()
    {
	global $pid;
	$msg = '';
	if ($pid == '') {
	    $msg = 'N';  
	}
	$form = new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider[0][]')->setValueOptions($providers);
	
	//$form->get('specimencollected[0][]')->setValue('onsite');
	
	$labs = $helper->getLabs('y');
	$form->get('lab_id[0][]')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority[0][]')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status",'pending');
	$form->get('status[0][]')->setValueOptions($status);
	
	return array('form' => $form, 'message' => $msg,);
    }
    
    public function getPatientLabOrdersAction()
    {
	$labOrders = $this->getLabTable()->listPatientLabOrders();
        $data 	= new JsonModel($labOrders);
        return $data;
    }
    
    public function getOrderListAction()
    {
        $request = $this->getRequest();
	 if($request->isPost()){
            $data = array(
		    'ordId'  => $request->getPost('id'),
		);
        }
	$labOrders = $this->getLabTable()->listLabOrders($data);
        $data = new JsonModel($labOrders);
        return $data;
    }
    
    public function getLabOrderAOEAction()
    {
	$request = $this->getRequest();
	$response = $this->getResponse();
        $data = array(
		    'ordId' 	=> $request->getPost('inputValue'),
		    'seq'	=> $request->getPost('seq'),
		);
	$aoe = $this->getLabTable()->listLabOrderAOE($data);
	$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'aoeArray' => $aoe)));
	return $response;
    }
    
    /*public function updateDataAction()
    {
	$request = $this->getRequest();
	if($request->isPost()){
	    //$fh = fopen("D:/test.txt","a");
	    //fwrite($fh,print_r($request->getPost(),1));
        }
	$Lab = new Lab();
	$aoeArr = array();
	foreach($request->getPost() as $key=>$val){
	    if(substr($key,0,4)==='AOE_'){
		$NewArr = explode("_",$key);
		$aoeArr[$NewArr[1]-1][$NewArr[2]][$NewArr[3]] = $val;
	    }
	}
	
	$clientorder_id = $this->getLabTable()->saveLab123($request->getPost(),$aoeArr);
    }*/
    
   public function removeLabOrderAction()
    {
	$request = $this->getRequest();
	if($request->isPost()){
	    $data = array(
		    'ordId'  => $request->getPost('orderID'),
		);
        }
	$result = $this->getLabTable()->removeLabOrders($data);
	return true;
    }
    
    public function saveDataAction()
    {
	$request = $this->getRequest();
	//$fh = fopen("d:/tttt.txt","a");
	//fwrite($fh,print_r($request,1));
	$data =array();
	$form = new LabForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
	    $Lab = new Lab();
	    $aoeArr = array();
	    foreach($request->getPost() as $key=>$val){
		if(substr($key,0,4)==='AOE_'){
		    $NewArr = explode("_",$key);
		    $aoeArr[$NewArr[1]-1][$NewArr[2]][$NewArr[3]] = $val;
		}
	    }
            
	    	$clientorder_id = $this->getLabTable()->saveLab($request->getPost(),$aoeArr);
                
		//------------- STARTING PROCEDURE ORDER XML IMPORT -------------
		for($i=0;$i<sizeof($clientorder_id);$i++){
                //GET CLIENT CREDENTIALS OF INITIATING ORDER
                $cred           = $this->getLabTable()->getClientCredentials($clientorder_id[$i]);                
                $username       = $cred['login'];
                $password       = $cred['password'];
                $remote_host    = trim($cred['remote_host']);
                $site_dir       = $GLOBALS['OE_SITE_DIR'];
                
                if(($username <> "")&&($password <> "")&&($remote_host <> "")) {//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB
		    $post = $request->getPost();
		    $labPost = $post['lab_id'][$i][0];
                    $labArr = explode("|",$labPost[$i]);
		    //RETURNS AN ARRAY OF ALL PENDING ORDERS OF THE PATIENT
                    $xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$labArr[0],"");
                    ini_set("soap.wsdl_cache_enabled","0");            
                    ini_set('memory_limit', '-1');
                    
                    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                    $client     = new Client(null,$options);                    
                    
                    $lab_id     = $labArr[0]; 
                    foreach($xmlresult_arr as $xmlresult){
                        $order_id   = $xmlresult['order_id'];
                        $xmlstring  = $xmlresult['xmlstring'];
			
			//GET CLIENT CREDENTIALS OF EACH PENDING ORDER OF A PARTICULAR PATIENT   
                        $cred           = $this->getLabTable()->getClientCredentials($order_id);                    
                        $username       = $cred['login'];
                        $password       = $cred['password'];
                        $remote_host    = trim($cred['remote_host']);
                        $site_dir       = $GLOBALS['OE_SITE_DIR'];
                        
                        if(($username <> "")&&($password <> "")&&($remote_host <> "")){//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB
                            $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
                            if(is_numeric($result))// CHECKS IF ORDER IS SUCCESSFULLY IMPORTED
                            {
                                $this->getLabTable()->setOrderStatus($order_id,"routed");
                            }
                        }                        
                    }                    
                }
		//------------- END PROCEDURE ORDER XML IMPORT -------------
		}
                return $this->redirect()->toRoute('result');
        }
        return array('form' => $form);
    }
    
    public function indexAction()
    {
	return $this->redirect()->toRoute('lab',array('action'=>'order'));
    }
    
    public function labLocationAction()
    {
	$request 		= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	if ($request->isPost()) {
		$location = $this->getLabLocation($inputString);
		$data = new JsonModel($location);
		return $data;
	}
    }
    
    public function getLabLocation($inputString)
    {
	$patents = $this->getLabTable()->listLabLocation($inputString);
	return $patents;
    }
    
    public function searchAction()
    {	
	$request 	= $this->getRequest();
	$response = $this->getResponse();
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'procedureArray' => $procedures)));
		//$fh = fopen("D:/test.txt","a");
        //fwrite($fh,print_r($response,1));
		return $response;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		$AOE = $this->getAOE($inputString,$dependentId);
		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'aoeArray' => $AOE)));
		return $response;
	    }
	    if($request->getPost('type') == 'getDiagnoses' ){ 
		$diagnoses = $this->getDiagnoses($inputString);
		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'diagnosesArray' => $diagnoses)));
		return $response;
	    }
	}
    }
    
    
    public function getProcedures($inputString,$labId)
    {
	$procedures = $this->getLabTable()->listProcedures($inputString,$labId);
	return $procedures;
    }
    
    public function getAOE($procedureCode,$labId)
    {
	$AOE = $this->getLabTable()->listAOE($procedureCode,$labId);
	return $AOE;
    }
    
    public function getDiagnoses($inputString)
    {
	$diagnoses = $this->getLabTable()->listDiagnoses($inputString);
	return $diagnoses;
    }
    
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\LabTable');
        }
        return $this->labTable;
    }   
}