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
	if ($pid == '' && $_SESSION['encounter'] == '') {
	    $msg = 'N';  
	}
	$form = new LabForm();
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
    
	//return array('form' => $form);
    }
    
    public function ordereditAction()
    {
	$form = new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider[0][]')->setValueOptions($providers);
	
	$form->get('specimencollected[0][]')->setValue('onsite');
	
	// disable layout in the form
	//$result = new ViewModel(array('form' => $form));
	//$result->setTerminal(true);
	//return $result;
	$labs = $helper->getLabs('y');
	$form->get('lab_id[0][]')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority[0][]')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status",'pending');
	$form->get('status[0][]')->setValueOptions($status);
	
	//$orders = $this->getLabTable()->listLabOrders();
	//$form->bind($orders);
	//return array('form' => $form, 'orders' => $orders);
	return array('form' => $form,);
    }
    
    public function getOrderListAction()
    {
	$data = array(
		    'ordId'  => '247',
            ); 
	$labOrders = $this->getLabTable()->listLabOrders($data);
        $data = new JsonModel($labOrders);
        return $data;
    }
   
    public function formAction()
    {
	$form = new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider[0][]')->setValueOptions($providers);
	
	$labs = $helper->getLabs();
	$form->get('lab_id[0][]')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority[0][]')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status",'pending');
	$form->get('status[0][]')->setValueOptions($status);
	
	// disable layout in the form
	$result = new ViewModel(array('form' => $form));
	$result->setTerminal(true);
	return $result;
	
	//return array('form' => $form);
	
	/*$request = $this->getRequest();
        $data =array();
        if ($request->getQuery('index') == 'index') {
            $data = array(
                    'index'  => $request->getQuery('index'),
            ); 
        }*/
        //$fh = fopen("D:/test.txt","a");
        //fwrite($fh,print_r($data,1));
       // $labResult = $this->getLabResult($data);
        //$data = new JsonModel($labResult);
        //return $data;
    }
    
    public function saveDataAction()
    {
	$request = $this->getRequest();
	$data =array();
	$form = new LabForm();
        /*if($request->isPost()){
            $data = array(
                    'statusReport'  => $request->getPost('statusReport'),
                    'statusOrder'   => $request->getPost('statusOrder'),
                    'statusResult'  => $request->getPost('statusResult'),
                    'dtFrom'        => $request->getPost('dtFrom'),
                    'dtTo'          => $request->getPost('dtTo'),
                    'page'          => $request->getPost('page'),
                    'rows'          => $request->getPost('rows'),
            ); 
        }*/
	$fh = fopen("D:/test.txt","a");
        fwrite($fh,print_r($request->getPost(),1));
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
            //$form->setData($request->getPost());
	    if (/*$form->isValid()*/1) {
		//$Lab->exchangeArray($form->getData());
                //$clientorder_id = $this->getLabTable()->saveLab($Lab,$aoeArr);
		$clientorder_id = $this->getLabTable()->saveLab($request->getPost(),$aoeArr);
                
		$fh = fopen("D:/tt.txt","a");
		fwrite($fh,print_r($clientorder_id,1));
                
		//------------- STARTING PROCEDURE ORDER XML IMPORT -------------
                //GET CLIENT CREDENTIALS OF INITIATING ORDER
                $cred           = $this->getLabTable()->getClientCredentials($clientorder_id[0]);                
                $username       = $cred['login'];
                $password       = $cred['password'];
                $remote_host    = trim($cred['remote_host']);
                $site_dir       = $GLOBALS['OE_SITE_DIR'];
                
                if(($username <> "")&&($password <> "")&&($remote_host <> "")) {//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB               
                    //RETURNS AN ARRAY OF ALL PENDING ORDERS OF THE PATIENT
                    /*****$xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$request->getPost('lab_id'),"");
                    ***/
		    $xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),1,"");
                    ini_set("soap.wsdl_cache_enabled","0");            
                    ini_set('memory_limit', '-1');
                    
                    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                    $client     = new Client(null,$options);                    
                    
                    /****$lab_id     = $request->getPost('lab_id');   ****/
                    $lab_id     = 1; 
                    foreach($xmlresult_arr as $xmlresult){
                        $order_id   = $xmlresult['order_id'];
                        $xmlstring  = $xmlresult['xmlstring'];
			
			
                         fwrite($fh,$xmlstring);
                        //GET CLIENT CREDENTIALS OF EACH PENDING ORDER OF A PARTICULAR PATIENT   
                        $cred           = $this->getLabTable()->getClientCredentials($order_id);                    
                        $username       = $cred['login'];
                        $password       = $cred['password'];
                        $remote_host    = trim($cred['remote_host']);
                        $site_dir       = $GLOBALS['OE_SITE_DIR'];
                        
                        if(($username <> "")&&($password <> "")&&($remote_host <> "")){//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB
                            $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
                            fwrite($fh,$result);
                            if(is_numeric($result))// CHECKS IF ORDER IS SUCCESSFULLY IMPORTED
                            {
                                $this->getLabTable()->setOrderStatus($order_id,"routed");
                            }
                        }                        
                    }                    
                }
		//------------- END PROCEDURE ORDER XML IMPORT -------------
			
                return $this->redirect()->toRoute('result');
            }
	    else {
		echo 'invalid ..';
		foreach ($form->getMessages() as $messageId => $message) {
			echo "Validation failure '$messageId':"; var_dump($message);
		}
	    }
        }
        return array('form' => $form);
    }
    
    public function indexAction()
    {
	return $this->redirect()->toRoute('lab',array('action'=>'order'));
	// Start Row wise data save 
	$request = $this->getRequest();
        if ($request->isPost()) {
	    $fh = fopen("D:/test.txt","a");
	    fwrite($fh,print_r($request->getPost(),1));
	}
	// End Row wise data save
	
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
                
		
                
		//------------- STARTING PROCEDURE ORDER XML IMPORT -------------
                //GET CLIENT CREDENTIALS OF INITIATING ORDER
                $cred           = $this->getLabTable()->getClientCredentials($clientorder_id[0]);                
                $username       = $cred['login'];
                $password       = $cred['password'];
                $remote_host    = trim($cred['remote_host']);
                $site_dir       = $GLOBALS['OE_SITE_DIR'];
                
                if(($username <> "")&&($password <> "")&&($remote_host <> "")) {//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB               
                    //RETURNS AN ARRAY OF ALL PENDING ORDERS OF THE PATIENT
                    $xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$request->getPost('lab_id'),"");
                    
                    ini_set("soap.wsdl_cache_enabled","0");            
                    ini_set('memory_limit', '-1');
                    
                    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                    $client     = new Client(null,$options);                    
                    
                    $lab_id     = $request->getPost('lab_id');   
                    
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
			
                return $this->redirect()->toRoute('result');
            }
	    else {
		echo 'invalid ..';
		foreach ($form->getMessages() as $messageId => $message) {
			echo "Validation failure '$messageId':"; var_dump($message);
		}
	    }
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
    
    public function sendimagetextAction($orderId) {
	if(!$orderId)
	$orderId = $_GET['order_id'];
	// Set font size
	$font_size = 2;
	//$text = "Client: ".$client_id."\nLab Ref: ".$lab_ref."\nPat Name: ".$pat_name;
	$row = sqlQuery("SELECT send_fac_id,CONCAT_WS('-',login,procedure_order_id) AS labref,CONCAT_WS(',',lname,fname) AS pname FROM procedure_order LEFT OUTER JOIN procedure_providers ON lab_id=ppid LEFT OUTER JOIN
	patient_data ON pid=patient_id WHERE procedure_order_id=?",array($orderId));
	$text = "Client: ".$row['send_fac_id']."\nLab Ref: ".$row['labref']."\nPat Name: ".$row['pname'];

	$ts=explode("\n",$text);
	$total_lines = count($ts);
	$width=0;
	foreach ($ts as $k=>$string) { //compute width
	    $width=max($width,strlen($string));
	}

	// Create image width dependant on width of the string
	//$width  = imagefontwidth($font_size)*$width;
	$width  = 168;
	// Set height to that of the font
	//$height = imagefontheight($font_size)*count($ts);
	$height = 72;
	$el=imagefontheight($font_size);
	$em=imagefontwidth($font_size);
	// Create the image pallette
	$img = imagecreatetruecolor($width,$height);
	// Dark red background
	$bg = imagecolorallocate($img, 255, 255, 255);
	imagefilledrectangle($img, 0, 0,$width ,$height , $bg);
	// White font color
	$color = imagecolorallocate($img, 0, 0, 0);
  
    foreach ($ts as $k=>$string) {
	// Length of the string
	$len = strlen($string);
	// Y-coordinate of character, X changes, Y is static
	$ypos = 0;
	// Loop through the string
	for($i=0;$i<$len;$i++){
	    // Position of the character horizontally
	    $xpos = $i * $em;
	    $ypos = $k * $el;
	    
	    $center_x = ceil( ( ( imagesx($img) - ( $em * $len ) ) / 2 ) + ( $i * $em ) );
	    $center_y = ceil( ( ( imagesy($img) - ( $el * $total_lines ) ) / 2)  + ( $k * $el ) );
	    
	    //error_log("aa:$xpos, $ypos---$center_x, $center_y");
	    
	    // Draw character
	    imagechar($img, $font_size, $center_x, $center_y, $string, $color);
	    // Remove character from string
	    $string = substr($string, 1);
	}
    }
	// Return the image
	//$IMGING = imagepng($img);
	//header("Content-Type: image/png");
	//header('Content-Disposition: attachment; filename=Specimen Label.png' );
	//  header("Content-Type: application/octet-stream" );
	//  header("Content-Length: " . filesize( $IMGING ) );
	ob_end_clean();
	ob_start();
	//imagejpeg($img);
	$this->createImageBorder($img);
	$IMGING = ob_get_contents();
	header("Content-Type: image/jpg");
	header('Content-Disposition: attachment; filename=SpecimenLabel_'.$orderId.'.jpg' );
	header("Content-Type: application/octet-stream" );
	header("Content-Length: " . filesize( $IMGING ) );
	// Remove image
	imagedestroy($img);
	exit;
    }
    
    function createImageBorder($imgName){

     //$img     =  substr($imgName, 0, -4); // remove fileExtension
     //$ext     = ".jpg";
     //$quality = 95;
     $borderColor = 0;  // 255 = white
    
    /*
     a                         b
     +-------------------------+
     |                         
     |          IMAGE          
     |                         
     +-------------------------+
     c                         d  
    */
   
    //$scr_img = imagecreatefromjpeg($img.$ext);
    $scr_img = $imgName;
    $width   = imagesx($scr_img);
    $height  = imagesy($scr_img);
             
        // line a - b
        $abX  = 0;
        $abY  = 0;
        $abX1 = $width;
        $abY1 = 0;
       
        // line a - c
        $acX  = 0;
        $acY  = 0;
        $acX1 = 0;
        $acY1 = $height;
       
        // line b - d
        $bdX  = $width-1;
        $bdY  = 0;
        $bdX1 = $width-1;
        $bdY1 = $height;
       
        // line c - d
        $cdX  = 0;
        $cdY  = $height-1;
        $cdX1 = $width;
        $cdY1 = $height-1;
	
	$w   = imagecolorallocate($scr_img, 255, 255, 255);
	$b = imagecolorallocate($scr_img, 0, 0, 0);
	
	$style = array_merge(array_fill(0, 5, $b), array_fill(0, 5, $w));
	imagesetstyle($scr_img, $style);
           
       // DRAW LINES   
        imageline($scr_img,$abX,$abY,$abX1,$abY1,IMG_COLOR_STYLED);
        imageline($scr_img,$acX,$acY,$acX1,$acY1,IMG_COLOR_STYLED);
        imageline($scr_img,$bdX,$bdY,$bdX1,$bdY1,IMG_COLOR_STYLED);
        imageline($scr_img,$cdX,$cdY,$cdX1,$cdY1,IMG_COLOR_STYLED);
       
      // create copy from image   
        imagejpeg($scr_img);
        //imagedestroy($scr_img);
  }
   
}