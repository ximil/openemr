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

use Zend\ZendPdf;

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
	
	$status = $helper->getList("ord_status",'pending');
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
            
            $options    = $this->getLabTable()->getWebserviceOptions();   
            $client     = new Client(null,$options);
            
            $lab_id     = $request->getPost('lab_id');            
            
	    //$order_no	= 0;
            foreach($xmlresult_arr as $xmlresult)
            {
                $order_id   = $xmlresult['order_id'];
                $xmlstring  = $xmlresult['xmlstring'];
		
		//$order_no++;
		//$fp	= fopen("D:/order_".$order_no,"w");
		//fwrite($fp,$xmlstring);
		//print_r($xmlstring);
		//echo "<br>";
		//continue;
               		
                $cred = $this->getLabTable()->getClientCredentials($order_id);
            
                $username   = $cred['login'];
                $password   = $cred['password'];        
                $site_dir   = $GLOBALS['OE_SITE_DIR'];
                
                $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
                //echo "Result <br>";
                //print_r($result);
            }
	    //exit;
			// End Prodedure Order Import
			
                return $this->redirect()->toRoute('result');
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
    
    public function sendimagetextAction($orderId) {
	if(!$orderId)
	$orderId = $_GET['order_id'];
  // Set font size
  $font_size = 2;
//$text = "Client: ".$client_id."\nLab Ref: ".$lab_ref."\nPat Name: ".$pat_name;
$row = sqlQuery("SELECT send_fac_id,CONCAT_WS('-',login,procedure_order_id) AS labref,CONCAT_WS(',',lname,fname) AS pname FROM procedure_order LEFT OUTER JOIN procedure_providers ON lab_id=ppid LEFT OUTER JOIN
patient_data ON pid=patient_id WHERE procedure_order_id=?",array($orderId));
$text = "Client: ".$row['send_fac_id']."\nLab Ref: ".$row['labref']."\nPat Name: ".$row['pname'];

//$dompdf = new DOMPDF();
//$dompdf->load_html($text);
//$dompdf->render();
//$dompdf->stream('sample.pdf');
//Zend_Loader::loadClass('Zend_Pdf');
$pdf1 = new ZendPdf();

// Load a PDF document from a file
$pdf2 = ZendPdf::load('D:/sample.pdf');

// Load a PDF document from a string
$pdf3 = ZendPdf::parse($text);

//  $ts=explode("\n",$text);
//  $total_lines = count($ts);
//  $width=0;
//  foreach ($ts as $k=>$string) { //compute width
//    $width=max($width,strlen($string));
//  }
//
//  // Create image width dependant on width of the string
//  //$width  = imagefontwidth($font_size)*$width;
//  $width  = 168;
//  // Set height to that of the font
//  //$height = imagefontheight($font_size)*count($ts);
//  $height = 72;
//  $el=imagefontheight($font_size);
//  $em=imagefontwidth($font_size);
//  // Create the image pallette
//  $img = imagecreatetruecolor($width,$height);
//  // Dark red background
//  $bg = imagecolorallocate($img, 255, 255, 255);
//  imagefilledrectangle($img, 0, 0,$width ,$height , $bg);
//  // White font color
//  $color = imagecolorallocate($img, 0, 0, 0);
//  
//  foreach ($ts as $k=>$string) {
//    // Length of the string
//    $len = strlen($string);
//    // Y-coordinate of character, X changes, Y is static
//    $ypos = 0;
//    // Loop through the string
//    for($i=0;$i<$len;$i++){
//      // Position of the character horizontally
//      $xpos = $i * $em;
//      $ypos = $k * $el;
//	  
//	  $center_x = ceil( ( ( imagesx($img) - ( $em * $len ) ) / 2 ) + ( $i * $em ) );
//	  $center_y = ceil( ( ( imagesy($img) - ( $el * $total_lines ) ) / 2)  + ( $k * $el ) );
//	  
//	  //error_log("aa:$xpos, $ypos---$center_x, $center_y");
//	  
//      // Draw character
//      imagechar($img, $font_size, $center_x, $center_y, $string, $color);
//      // Remove character from string
//      $string = substr($string, 1);
//    }
//  }
//  // Return the image
//  //$IMGING = imagepng($img);
//  //header("Content-Type: image/png");
//  //header('Content-Disposition: attachment; filename=Specimen Label.png' );
//  //  header("Content-Type: application/octet-stream" );
//  //  header("Content-Length: " . filesize( $IMGING ) );
//    ob_end_clean();
//    ob_start();
//    imagepng($img);
//    $IMGING = ob_get_contents();
//    $fh = fopen("D:/speclbl.pdf","w");
//    fwrite($fh,$IMGING);
//    header("Content-Type: application/octet-stream");
//    header('Content-Disposition: attachment; filename=SpecimenLabel.png' );
//    header("Content-Type: application/octet-stream" );
//    header("Content-Length: " . filesize( $IMGING ) );
//    // Remove image
//    imagedestroy($img);
}
   
    /**
    * Vipin
    */
    
    public function pullcompendiumtestAction()
    {        
	ini_set("soap.wsdl_cache_enabled","0");	
	ini_set('memory_limit', '-1');
	
	$options    = $this->getLabTable()->getWebserviceOptions();
	$client     = new Client(null,$options);	
	$result     = $client->check_for_tests();
	
	$testconfig_arr = $this->getLabTable()->pullcompendiumTestConfig();
        
	$this->getLabTable()->importDataCheck($result,$testconfig_arr);
	return $this->redirect()->toRoute('result');
    }
    
    public function pullcompendiumaoeAction()
    {
	ini_set("soap.wsdl_cache_enabled","0");
	ini_set('memory_limit', '-1');
	
        $options    = $this->getLabTable()->getWebserviceOptions();        
	$client     = new Client(null,$options);
	$result     = $client->check_for_aoe();
                
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
        $patient_id = 2;//HARD CODED
        
        $xmlresult_arr = $this->getLabTable()->generateOrderXml($patient_id,$lab_id,$xmlfileurl);
       
        //$fd = fopen("module/Lab/".$xmlfile,"r") or die("can't open xml file");
            
        //$xmlstring  = fread($fd,filesize("module/Lab/".$xmlfile));
        
        //return false;
        
        //$request = $this->getRequest();
        //if($request->isGet())
        //{
        //    $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
        //}
        
        
        //$data['procedure_order_id'] = 2; //HARD CODED FOR TESTING
        //print_r($data);
        
        ini_set("soap.wsdl_cache_enabled","0");
        
        ini_set('memory_limit', '-1');
        
        
        $options    = $this->getLabTable()->getWebserviceOptions();
        $client     = new Client(null,$options);
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
	if($request->isPost()) {
	    $data = array('procedure_order_id'	=> $request->getPost('order_id'));
	} elseif ($request->isGet()) {
	     $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
	}
	//$data = array('procedure_order_id'	=> '119');
        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);

        if(($curr_status == "requisitionpulled")||($curr_status == "final")) {
            $labrequisitionfile    = $this->getLabTable()->getOrderRequisitionFile($data['procedure_order_id']);
	} else {
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);

            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $_SESSION['site_id'];
           
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = $this->getLabTable()->getWebserviceOptions();
            $client     = new Client(null,$options);
            $result     = $client->getLabRequisition($username,$password,$site_dir,$data['procedure_order_id']); //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID   
       
            $labrequisitionfile  = "labrequisition_".gmdate('YmdHis').".pdf";           
            
            if (!is_dir($requisition_dir)) {
                mkdir($requisition_dir,0777,true);
            }

            $fp = fopen($requisition_dir.$labrequisitionfile,"wb");
            fwrite($fp,base64_decode($result));
            $status_res = $this->getLabTable()->changeOrderRequisitionStatus($data['procedure_order_id'],"requisitionpulled",$labrequisitionfile);
        }
	// Ajax Handling (Result success or failed)  
	if($request->isPost()) {
	    $arrResult = explode(':', $result);
	    if ($arrResult[0] == 'failed') {
		$return[0] = array('return'=>1, 'msg'=> $arrResult[1]);
		$arr = new JsonModel($return);
		return $arr;
	    } else {
		$return[0] = array('return'=>0, 'order_id'=> $data['procedure_order_id']);
		$arr = new JsonModel($return);
		return $arr;
	    }
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
	if($request->isPost()) {
	    $data = array('procedure_order_id'	=> $request->getPost('order_id'));
	} elseif ($request->isGet()) {
	     $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
	}

        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);
        
        if($curr_status == "final") {
            $labresultfile    = $this->getLabTable()->getOrderResultFile($data['procedure_order_id']);            
        } else {
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
                
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $_SESSION['site_id'];
                
            ini_set("soap.wsdl_cache_enabled","0");	
            ini_set('memory_limit', '-1');
            
            $options    = $this->getLabTable()->getWebserviceOptions();    
            $client     = new Client(null,$options);
            $result     = $client->getLabResult($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       

            $labresultfile  = "labresult_".gmdate('YmdHis').".pdf";        
            
            if (!is_dir($result_dir)) {
                mkdir($result_dir,0777,true);
            }
            $fp = fopen($result_dir.$labresultfile,"wb");
            fwrite($fp,base64_decode($result));
            
            $status_res = $this->getLabTable()->changeOrderResultStatus($data['procedure_order_id'],"final",$labresultfile);
        }
	// Ajax Handling (Result success or failed)
        if($request->isPost()) {
	    $arrResult = explode(':', $result);
	    if ($arrResult[0] == 'failed') {
		$return[0] = array('return'=>1, 'msg'=> $arrResult[1]);
		$arr = new JsonModel($return);
		return $arr;
	    } else {
		$return[0] = array('return'=>0, 'order_id'=> $data['procedure_order_id']);
		$arr = new JsonModel($return);
		return $arr;
	    }
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
        $site_dir               = $GLOBALS['OE_SITE_DIR'];        
        $resultdetails_dir      = $site_dir."/lab/resultdetails/";
        
	$request = $this->getRequest();
	if($request->isGet())
	{
	    $data = array('procedure_order_id'	=> $request->getQuery('order_id'));
	}
        
	$data['procedure_order_id'] = 2; //HARD CODED FOR TESTING
	
        $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
	   
	$username   = $cred['login'];
	$password   = $cred['password'];        
	$site_dir   = $GLOBALS['site_id'];
	    
	ini_set("soap.wsdl_cache_enabled","0");	
	ini_set('memory_limit', '-1');
	
	$options    = $this->getLabTable()->getWebserviceOptions();
	$client     = new Client(null,$options);
	$result     = $client->getLabResultDetails($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       
	
        $labresultdetailsfile  = "labresultdetails_".gmdate('YmdHis').".xml";
	
        if (!is_dir($resultdetails_dir))
        {
            mkdir($resultdetails_dir,0777,true);
        }
            
	$fp = fopen($resultdetails_dir.$labresultdetailsfile,"wb");
	fwrite($fp,$result);	
	
	$reader = new Config\Reader\Xml();
	$data   = $reader->fromFile($resultdetails_dir.$labresultdetailsfile);
        //print_r($data);
        
        $result_config_arr = $this->getLabTable()->mapResultXmlToColumn();
        
	$this->getLabTable()->importResultDetails($result_config_arr,$result);
        
        
    }
    
    public function testAction()
    {
        
    }
}