<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Lab;
use Lab\Form\LabForm;
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

class LabController extends AbstractActionController
{
    protected $labTable;
	
    public function indexAction()
    {
        $form = new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider')->setValueOptions($providers);
	
	$labs = $helper->getLabs();
	$form->get('lab_id')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status");
	$form->get('status')->setValueOptions($status);
	//$form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Lab();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
		$Lab->exchangeArray($form->getData());
                $clientorder_id = $this->getLabTable()->saveLab($Lab);
			
			//Start Procedure Order Import 
            $xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$request->getPost('lab_id'),"");
            //print_r($xmlfile);        
            ini_set("soap.wsdl_cache_enabled","0");            
            ini_set('memory_limit', '-1');
            
            $options = array(
                                'location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                            );    
            $client = new Client(null,$options);
            
            $lab_id         = $request->getPost('lab_id');            
            
            foreach($xmlresult_arr as $xmlresult)
            {
                $order_id   = $xmlresult['order_id'];
                $xmlstring  = $xmlresult['xmlstring'];
                
                $cred = $this->getLabTable()->getClientCredentials($order_id);
            
                $username   = $cred['login'];
                $password   = $cred['password'];        
                $site_dir   = $GLOBALS['OE_SITE_DIR'];
                
                $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
                //echo "Result <br>";
                //print_r($result);
            }
			// End Prodedure Order Import
			
                //return $this->redirect()->toRoute('result');
            }
	    else {
		echo 'invalid ..';
		foreach ($form->getMessages() as $messageId => $message) {
			echo "Validation failure '$messageId':"; var_dump($message);
		} //echo '<pre>'; var_dump($form); echo '</pre>';
	    }//die("jjjjjjjjjj");
        }
        return array('form' => $form);
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
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$data = new JsonModel($procedures);
		//$data = json_encode($procedures);
		return $data;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		$AOE = $this->getAOE($inputString,$dependentId);
		$data = new JsonModel($AOE);
		return $data;
	    }
	}
    }
    public function search11Action()
    {
	//$fh = fopen("D:/AOE3.txt","a");
	//fwrite($fh,"gfgzGGGGGGG\r\n");
	$request 	= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	$viewModel = new ViewModel();
    //$viewModel->setTemplate('module/controller/action');
    $viewModel->setTerminal(true);
    
	//$fh = fopen("D:/AOE.txt","a");
		//fwrite($fh,"gfgzGGGGGGG\r\n");
		//fwrite($fh,print_r($request->getPost(),1));
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$data = new JsonModel($procedures);
		//$data = json_encode($procedures);
		return $data;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		//$fh = fopen("D:/AOE.txt","a");
		//fwrite($fh,"gfgzG");
		$AOE = $this->getAOE($inputString,$dependentId);
		$data = new JsonModel($AOE);
		return $data;
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
    
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\LabTable');
        }
        return $this->labTable;
    }
	
    public function resultAction()
    {
		
    }
	
	public function getLabStatusAction()
	{
		$request = $this->getRequest();
		$data =array();
		if($request->getQuery('opt')){
			$data['opt'] = 'search';
			//$fh = fopen("D:/test.txt","a");
			//fwrite($fh,print_r($data,1));
		}
		$labStatus = $this->getLabTable()->listLabStatus($data);
		$data = new JsonModel($labStatus);
		return $data;
	}
	
	public function getLabAbnormalAction()
	{
		$labAbnormal = $this->getLabTable()->listLabAbnormal();
		$data = new JsonModel($labAbnormal);
		return $data;
	}
	
	public function getLabResult($data)
	{
		$labResult = $this->getLabTable()->listLabResult($data);
		return $labResult;
	}
	
	public function resultShowAction()
	{
		$request = $this->getRequest();
		$data =array();
		if($request->getPost('status')){
			$data = array(
				'status'	=> $request->getPost('status'),
				'dtFrom'	=> $request->getPost('dtFrom'),
				'dtTo'	=> $request->getPost('dtTo'),
			); 
		}
		$labResult = $this->getLabResult($data);
		$data = new JsonModel($labResult);
		return $data;
	}
	
	public function resultUpdateAction()
	{
		$request = $this->getRequest();
        if ($request->isPost()) {
			$data = array(
					'procedure_report_id'	=> $request->getPost('procedure_report_id'),
					'procedure_result_id'	=> $request->getPost('procedure_result_id'),
					'procedure_order_id'	=> $request->getPost('procedure_order_id'),
					'specimen_num'			=> $request->getPost('specimen_num'),
					'report_status'  		=> $request->getPost('report_status'),
					'procedure_order_seq'	=> $request->getPost('procedure_order_seq'),
					'date_report'			=> $request->getPost('date_report'),
					'date_collected'		=> $request->getPost('date_collected'),
					'result_code'			=> $request->getPost('result_code'),
					'procedure_report_id'	=> $request->getPost('procedure_report_id'),
					'result_text'			=> $request->getPost('result_text'),
					'abnormal'				=> $request->getPost('abnormal'),
					'result'				=> $request->getPost('result'),
					'range'					=> $request->getPost('range'),
					'units'					=> $request->getPost('units'),
					'result_status'			=> $request->getPost('result_status'),
			);

			$this->getLabTable()->saveResult($data);
			return $this->redirect()->toRoute('result');
        }
        return $this->redirect()->toRoute('result');
	}
	
	public function formCommentsAction()
	{

	}
	
	/**
	* Vipin
	*/
	
	public function pullcompandiantestAction()
        {
            ini_set("soap.wsdl_cache_enabled","0");
            
            ini_set('memory_limit', '-1');
            
            $options = array(
                                'location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                            );
    
            $client = new Client(null,$options);
            
            $result = $client->check_for_tests();
            
            $testconfig_arr = $this->getLabTable()->pullCompandianTestConfig();
            $this->getLabTable()->importDataCheck($result,$testconfig_arr);
            return $this->redirect()->toRoute('result');
        }
        
        public function pullcompandianaoeAction()
        {
            ini_set("soap.wsdl_cache_enabled","0");
            ini_set('memory_limit', '-1');
            $options = array(
                                'location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                            );
            $client = new Client(null,$options);
            $result = $client->check_for_aoe();
            $testconfig_arr = $this->getLabTable()->pullCompandianAoeConfig();
            $this->getLabTable()->importDataCheck($result,$testconfig_arr);
            return $this->redirect()->toRoute('result');
        }
    
        public function getlabrequisitionAction()
        {
            $request = $this->getRequest();
            if($request->isGet())
            {
                $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
            }
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
            
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $GLOBALS['OE_SITE_DIR'];
           
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                                );
            $client     = new Client(null,$options);
            $result     = $client->getLabRequisition($username,$password,$site_dir,$data['procedure_order_id']); //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID   
        
            $labresultfile  = "labrequisition_".gmdate('YmdHis').".pdf";
            
            $fp = fopen("module/Lab/".$labresultfile,"wb");
            fwrite($fp,base64_decode($result));
            
            header('Content-Disposition: attachment; filename='.$labresultfile );
            header("Content-Type: application/octet-stream" );
            header("Content-Length: " . filesize( "module/Lab/".$labresultfile ) );		
            readfile( "module/Lab/".$labresultfile );         
        }
    
        public function getlabresultAction()
        {
            $request = $this->getRequest();
            if($request->isGet())
            {
                $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
            }
            
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
                
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $GLOBALS['OE_SITE_DIR'];
                
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                                );
    
            $client     = new Client(null,$options);
            $result     = $client->getLabResult($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       
            //print_r($result);
            
            $labresultfile  = "labresult_".gmdate('YmdHis').".pdf";
            $fp = fopen("module/Lab/".$labresultfile,"wb");
            fwrite($fp,base64_decode($result));
            header('Content-Disposition: attachment; filename='.$labresultfile );
            header("Content-Type: application/octet-stream" );
            header("Content-Length: " . filesize( "module/Lab/".$labresultfile ) );
            readfile( "module/Lab/".$labresultfile );         
        }
        
        public function getlabresultdetailsAction()
        {
            $request = $this->getRequest();
            if($request->isGet())
            {
                $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
            }
            
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
               
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $GLOBALS['OE_SITE_DIR'];
                
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                                );
    
            $client     = new Client(null,$options);
            $result     = $client->getLabResultDetails($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       
            
            $labresultdetailsfile  = "labresultdetails_".gmdate('YmdHis').".xml";
                
            $fp = fopen("module/Lab/".$labresultdetailsfile,"wb");
            fwrite($fp,base64_decode($result));
            
            
            $reader = new Config\Reader\Xml();
            $data   = $reader->fromFile("module/Lab/".$labresultdetailsfile);
        }
}