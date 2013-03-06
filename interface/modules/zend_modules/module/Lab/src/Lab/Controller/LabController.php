<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Lab;
use Lab\Form\LabForm;
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;

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
	    $Lab = new Lab();
	    $aoeArr = array();
	    foreach($request->getPost() as $key=>$val){
		if(substr($key,0,4)==='AOE_'){
		    $NewArr = explode("_",$key);
		    $aoeArr[$NewArr[1]][$NewArr[2]] = $val;
		}
	    }
            $form->setData($request->getPost());
	    if ($form->isValid()) {
		$Lab->exchangeArray($form->getData());
                $clientorder_id = $this->getLabTable()->saveLab($Lab,$aoeArr);
	    /////////////////////////////////////////////////////////////////////////////////////////////////////
	    $xmlfile = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$request->getPost('lab_id'),"");
	    $fd = fopen("module/Lab/".$xmlfile,"r") or die("can't open xml file");
	    $xmlstring  = fread($fd,filesize("module/Lab/".$xmlfile));
	    ini_set("soap.wsdl_cache_enabled","0");
	    ini_set('memory_limit', '-1');
	    $options = array(
				'location' => "http://192.168.1.139/webserver/lab_server.php",
				'uri'      => "urn://zhhealthcare/lab"
			    );
	    $client = new Client(null,$options);
	    $client_id      = "1";
	    $lab_id         = $request->getPost('lab_id');
	    $result = $client->importOrder($client_id,$clientorder_id,$lab_id,$xmlstring);
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
	$response = $this->getResponse();
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'procedureArray' => $procedures)));
		return $response;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		$AOE = $this->getAOE($inputString,$dependentId);
		$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'aoeArray' => $AOE)));
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
	
	public function resultSearchAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			//$request->getQuery();
			//echo '<pre>'; print_r($request->getQuery()); echo '</pre>';
			$data = array(
				'status'	=> $request->getPost('status'),
			); 
			$labResult = $this->getSearchLabResult($data);
			$data = new JsonModel($labResult);
			return $data; 
		}
    }
	
	public function getSearchLabResult($data)
	{
		$labResult = $this->getLabTable()->listSearchLabResult($data);
		return $labResult;
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
		
		//$fh = fopen("D:/test.txt","a");
		//fwrite($fh,print_r($data,1));
		$labResult = $this->getLabResult($data);
		$data = new JsonModel($labResult);
		return $data;
	}
	
	public function resultUpdateAction()
	{
		/*$form = new LabForm();
    	$lab = new Lab();
		$request = $this->getRequest();
    	if ($request->isPost()) {
			$form->setInputFilter($lab->getInputFilter());
			if ($form->isValid()) {
				$lab->exchangeArray($form->getData());
				print_r($form->getData());
				$this->getLabTable()->saveLab($lab);
				echo 'Successfuly inserted..';
			} else {
				echo 'invalid ..';
				foreach ($form->getMessages() as $messageId => $message) {
					echo "Validation failure '$messageId':"; print_r($message);
				}
			}
		}*/
		
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

			//$fh = fopen("D:/test.txt","a");
			//fwrite($fh,print_r($data,1));
			$this->getLabTable()->saveResult($data);
			return $this->redirect()->toRoute('result');
        }
        return $this->redirect()->toRoute('result');
	}
	
	public function formcommentsAction()
	{
		echo 'test';die;
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
        //print_r($testconfig_arr);
        
        //$this->getLabTable()->importData($result,$testconfig_arr);
        $this->getLabTable()->importDataCheck($result,$testconfig_arr);
        
        //print_r($xmlfile);
		return $this->redirect()->toRoute('result');
    }
    
    public function generateorderAction()
    {
        //$xmlfileurl = "ordernew.xml";
        //$xmlfile = $this->getLabTable()->generateOrderXml(5,$xmlfileurl);//Pateint ID, Lab ID, File Name to be created
        //print_r($xmlfile);
        
        $lab_id     = 0;
        $patient_id = 7962;
        
        $xmlfile = $this->getLabTable()->generateOrderXml($patient_id,$lab_id,$xmlfileurl);
        //print_r($xmlfile);
        
        $fd = fopen("module/Lab/".$xmlfile,"r") or die("can't open xml file");
            
        $xmlstring  = fread($fd,filesize("module/Lab/".$xmlfile));
        
        //return false;
    
        ini_set("soap.wsdl_cache_enabled","0");
	
	ini_set('memory_limit', '-1');
        
        $options = array(
                            'location' => "http://192.168.1.139/webserver/lab_server.php",
                            'uri'      => "urn://zhhealthcare/lab"
                        );

	$client = new Client(null,$options);
        
        $client_id      = "1";
        $clientorder_id = "1";
        $lab_id         = "1";
        
        $result = $client->importOrder($client_id,$clientorder_id,$lab_id,$xmlstring);
        echo "Result <br>";
        print_r($result);
    }
    
    
    public function getlabrequisitionAction()
    {
        ini_set("soap.wsdl_cache_enabled","0");
	
		ini_set('memory_limit', '-1');
	
        $options = array(
                            'location' => "http://192.168.1.139/webserver/lab_server.php",
                            'uri'      => "urn://zhhealthcare/lab"
                        );

	$client = new Client(null,$options);
        //echo "<br>REQUISITION RESULT <br>";
        $result = $client->getLabRequisition(1,101);       //CLIENT ID, CLIENT ORDER ID   
        
        //echo $result;
        
        $labresultfile  = "labrequisition_".gmdate('YmdHis').".pdf";
        
        $fp = fopen("module/Lab/".$labresultfile,"wb");
        fwrite($fp,base64_decode($result));
        
        //$tmpfilename = "/encrypted_".$labresultfile;
		//$tmpfile = fopen( $tmpfilepath.$tmpfilename, "w+" );
		//fwrite( $tmpfile, $ciphertext );
		//fclose( $tmpfile );
		header('Content-Disposition: attachment; filename='.$labresultfile );
		header("Content-Type: application/octet-stream" );
		header("Content-Length: " . filesize( "module/Lab/".$labresultfile ) );
		//ob_clean();
		//flush();
		readfile( "module/Lab/".$labresultfile );
         
    }
    
    public function getlabresultAction()
    {
        ini_set("soap.wsdl_cache_enabled","0");
	
	ini_set('memory_limit', '-1');
        
        $options = array(
                            'location' => "http://192.168.1.139/webserver/lab_server.php",
                            'uri'      => "urn://zhhealthcare/lab"
                        );

	$client = new Client(null,$options);
        //echo "<br>LAB RESULT <br>";
        $result = $client->getLabResult(1,101);  //CLIENT ID, CLIENT ORDER ID     
        
        //print_r($result);
        
        $labresultfile  = "labresult_".gmdate('YmdHis').".pdf";
        
        $fp = fopen("module/Lab/".$labresultfile,"wb");
        fwrite($fp,base64_decode($result));
		
		header('Content-Disposition: attachment; filename='.$labresultfile );
		header("Content-Type: application/octet-stream" );
		header("Content-Length: " . filesize( "module/Lab/".$labresultfile ) );

		readfile( "module/Lab/".$labresultfile );
         
    }
}