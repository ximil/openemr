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
	
	$status = $helper->getList1("ord_status");
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
                //$clientorder_id = $this->getLabTable()->saveLab($Lab,$aoeArr);
		$clientorder_id = $this->getLabTable()->saveLab($request->getPost(),$aoeArr);
			
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
	
	 public function getLabOptionsAction()
    {
		$request = $this->getRequest();
	    $data =array();
		if($request->getQuery('opt')){
			switch ($request->getQuery('opt')) {
				case 'search':
					$data['opt'] = 'search';
					break;
				case 'status':
					$data['opt'] = 'status';
					break;
				case 'abnormal':
					$data['opt'] = 'abnormal';
					break;
			}
		}
		if($request->getQuery('optId')){
			switch ($request->getQuery('optId')) {
				case 'order':
					$data['optId'] = 'ord_status';
					break;
				case 'report':
					$data['optId'] = 'proc_rep_status';
					break;
				case 'result':
					$data['optId'] = 'proc_res_status';
					break;
				case 'abnormal':
					$data['optId'] = 'proc_res_abnormal';
					break;
			}
		}
	    $labOptions = $this->getLabTable()->listLabOptions($data);
	    $data = new JsonModel($labOptions);
	    return $data;
    }
	
    public function getLabStatusAction()
    {
		//$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');


		$request = $this->getRequest();
	    $data =array();
		if($request->getQuery('opt')){
		    $data['opt'] = 'search';
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

	public function getResultCommentsAction()
	{
		$request = $this->getRequest();
	    $data =array();
		if($request->getPost('prid')){
		    $data['procedure_result_id'] = $request->getPost('prid');
		}
		//$fh = fopen("D:/test.txt","a");
		//fwrite($fh,print_r($request->getPost(),1));
	    //$data['procedure_result_id'] = 5;
		$comments = $this->getLabTable()->listResultComment($data);
	    $data = new JsonModel($comments);
		return $data;
	}
	
    public function resultShowAction()
    {
	    $request = $this->getRequest();
	    $data =array();
	    if($request->isPost()){
		    $data = array(
			    'statusReport'	=> $request->getPost('statusReport'),
				'statusOrder'	=> $request->getPost('statusOrder'),
				'statusResult'	=> $request->getPost('statusResult'),
			    'dtFrom'		=> $request->getPost('dtFrom'),
			    'dtTo'			=> $request->getPost('dtTo'),
				'page'			=> $request->getPost('page'),
				'rows'			=> $request->getPost('rows'),
		    ); 
	    }
		//$fh = fopen("D:/test.txt","a");
		//fwrite($fh,print_r($request->getPost(),1));
	    $labResult = $this->getLabResult($data);
	    $data = new JsonModel($labResult);
	    return $data;
    }
    
    public function resultUpdateAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arr = explode('|', $request->getPost('comments'));
			$comments = '';
			$comments = $arr[2];
			if ($arr[3] != '') {
				$comments .=  "\n" . $arr[3];
			}
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
				    'result_text'			=> $request->getPost('result_text'),
				    'abnormal'				=> $request->getPost('abnormal'),
				    'result'				=> $request->getPost('result'),
				    'range'					=> $request->getPost('range'),
				    'units'					=> $request->getPost('units'),
				    'result_status'			=> $arr[0],
					'facility'				=> $arr[1],
					'comments'				=> $comments,
		    );
			//$fh = fopen("D:/test.txt","a");
			//fwrite($fh,print_r($data,1));
		    $this->getLabTable()->saveResult($data);
		    return $this->redirect()->toRoute('result');
		}
		return $this->redirect()->toRoute('result');
    }
    
    /**
    * Vipin
    */
    
    public function pullcompendiumtestAction()
    {
        
	ini_set("soap.wsdl_cache_enabled","0");
	
	ini_set('memory_limit', '-1');
	
	$options = array(
			    'location' => "http://192.168.1.139/webserver/lab_server.php",
			    'uri'      => "urn://zhhealthcare/lab"
			);

	$client = new Client(null,$options);
	
	$result = $client->check_for_tests();
	
	$testconfig_arr = $this->getLabTable()->pullcompendiumTestConfig();
	$this->getLabTable()->importDataCheck($result,$testconfig_arr);
	return $this->redirect()->toRoute('result');
    }
    
    public function pullcompendiumaoeAction()
    {
	ini_set("soap.wsdl_cache_enabled","0");
	ini_set('memory_limit', '-1');
	$options = array(
			    'location' => "http://192.168.1.139/webserver/lab_server.php",
			    'uri'      => "urn://zhhealthcare/lab"
			);
	$client = new Client(null,$options);
	$result = $client->check_for_aoe();
	$testconfig_arr = $this->getLabTable()->pullcompendiumAoeConfig();
	$this->getLabTable()->importDataCheck($result,$testconfig_arr);
	return $this->redirect()->toRoute('result');
    }

    /*
    //THIS ACTION IS MOVED INTO INDEX ACTION
    public function generateorderAction()
    {
        //$xmlfileurl = "ordernew.xml";
        //$xmlfile = $this->getLabTable()->generateOrderXml(5,$xmlfileurl);//Pateint ID, Lab ID, File Name to be created
        //print_r($xmlfile);
        
        $lab_id     = 1;//HARD CODED
        $patient_id = 7962;//HARD CODED
        
        $xmlresult_arr = $this->getLabTable()->generateOrderXml($patient_id,$lab_id,$xmlfileurl);
        
        
        //print_r($xmlresult_arr);
        //exit;
        //$fd = fopen("module/Lab/".$xmlfile,"r") or die("can't open xml file");
            
        //$xmlstring  = fread($fd,filesize("module/Lab/".$xmlfile));
        
        //return false;
        
        //$request = $this->getRequest();
        //if($request->isGet())
        //{
        //    $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
        //}
        
        
        //$data['procedure_order_id'] = 4; //HARD CODED FOR TESTING
        //print_r($data);
        
        ini_set("soap.wsdl_cache_enabled","0");
        
        ini_set('memory_limit', '-1');
        
        $options = array(
                            'location' => "http://192.168.1.139/webserver/lab_server.php",
                            'uri'      => "urn://zhhealthcare/lab"
                        );

        $client = new Client(null,$options);
        $i=0;
        foreach($xmlresult_arr as $xmlresult)
        {
            $i++;
            $order_id   = $xmlresult['order_id'];
            $xmlstring  = $xmlresult['xmlstring'];
            
            $fd = fopen("module/Lab/vipi$i.xml","w") or die("can't open xml file");
            
            $xmlstring  = fwrite($fd,$xmlstring);
            continue;
            $cred = $this->getLabTable()->getClientCredentials($order_id);
        
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $GLOBALS['OE_SITE_DIR'];
            
            //$client_id      = "1";
            //$clientorder_id = "1";
            //$lab_id         = "1";//HARD CODED
            
            $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
            //echo "Result <br>";
            //print_r($result);
        }
        
    }
    */    

    public function getlabrequisitionAction()
    {
        $site_dir           = $GLOBALS['OE_SITE_DIR'];            
        $requisition_dir    = $site_dir."/lab/requisition/";
            
        $request = $this->getRequest();
	if($request->isGet())
	{
	    $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
	}
        
        //$data['procedure_order_id'] = 3; //HARD CODED FOR TESTING
        
        
        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);
        
        if(($curr_status == "requisitionpulled")||($curr_status == "final"))
        {
            $labrequisitionfile    = $this->getLabTable()->getOrderRequisitionFile($data['procedure_order_id']);            
        }
	else
        {
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
                    
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $_SESSION['site_id'];
           
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                                );
            $client     = new Client(null,$options);
            $result     = $client->getLabRequisition($username,$password,$site_dir,$data['procedure_order_id']); //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID   
       
            $labrequisitionfile  = "labrequisition_".gmdate('YmdHis').".pdf";           
            
            if (!is_dir($requisition_dir))
            {
                mkdir($requisition_dir,0777,true);
            }
            
            $fp = fopen($requisition_dir.$labrequisitionfile,"wb");
            fwrite($fp,base64_decode($result));
            
            $status_res = $this->getLabTable()->changeOrderRequisitionStatus($data['procedure_order_id'],"requisitionpulled",$labrequisitionfile);
        }
        
	while(ob_get_level()){
	    ob_get_clean();
	}
	header('Content-Disposition: attachment; filename='.$labrequisitionfile );
	header("Content-Type: application/octet-stream" );
	header("Content-Length: " . filesize( $requisition_dir.$labrequisitionfile ) );		
	readfile( $requisition_dir.$labrequisitionfile );         
    }

    public function getlabresultAction()
    {
        $site_dir   = $GLOBALS['OE_SITE_DIR'];        
        $result_dir    = $site_dir."/lab/result/";
        
	$request = $this->getRequest();
	if($request->isGet())
	{
	    $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
	}
	
        //$data['procedure_order_id'] = 3; //HARD CODED FOR TESTING
        
        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);
        
        if($curr_status == "final")
        {
            $labresultfile    = $this->getLabTable()->getOrderResultFile($data['procedure_order_id']);            
        }
	else
        {
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
                
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $_SESSION['site_id'];
                
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
                                'uri'      => "urn://zhhealthcare/lab"
                                );
    
            $client     = new Client(null,$options);
            $result     = $client->getLabResult($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       
            //print_r($result);
            //exit;
            $labresultfile  = "labresult_".gmdate('YmdHis').".pdf";        
            
            if (!is_dir($result_dir))
            {
                mkdir($result_dir,0777,true);
            }
            $fp = fopen($result_dir.$labresultfile,"wb");
            fwrite($fp,base64_decode($result));
            
            $status_res = $this->getLabTable()->changeOrderResultStatus($data['procedure_order_id'],"final",$labresultfile);
        }
        
	while(ob_get_level()){
	    ob_get_clean();
	}
	header('Content-Disposition: attachment; filename='.$labresultfile );
	header("Content-Type: application/octet-stream" );
	header("Content-Length: " . filesize( $result_dir.$labresultfile ) );
	readfile( $result_dir.$labresultfile );         
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