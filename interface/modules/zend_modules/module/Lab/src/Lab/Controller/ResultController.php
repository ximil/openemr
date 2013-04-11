<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Lab\Form\ResultForm;
use Zend\Json\Json;
use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

class ResultController extends AbstractActionController
{
    protected $labTable;
	
    public function indexAction()
    {
	$request = $this->getRequest();
	$pageno = 1;
	if($request->isGet()){
	
	 $pageno = ($request->getQuery('pageno')<> "") ? $request->getQuery('pageno') : 1;
	}
	 
     $labresult1=$this->resultShowAction($pageno); 
	 //$data = new JsonModel($labresult1);
	    $viewModel = new ViewModel(array(
	    "labresults"=>$labresult1
		));
	return $viewModel;	  
		
    }
    public function getLabelDownloadAction($orderId) {
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
	////////imagejpeg($img);
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
    
    public function paginationAction()
    {   
	
	$request = $this->getRequest();
	$pageno = 1;
	if($request->isGet()){
	
	 $pageno = ($request->getQuery('pageno')<> "") ? $request->getQuery('pageno') : 1;
	}
	 	 
	$labresult1=$this->resultShowAction($pageno); 
	    $viewModel = new ViewModel(array(
	    "labresults"=>$labresult1
		));
	return $viewModel;	   
	
    }
    public function getResultTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\ResultTable');
        }
        return $this->labTable;
    }
    
    public function resultShowAction($pageno)
    {		
        $request = $this->getRequest();
        $data =array();
        if($request->isPost()){
            $data = array(
                    'statusReport'  => $request->getPost('searchStatusReport'),
                    'statusOrder'   => $request->getPost('searchStatusOrder'),
                    'statusResult'  => $request->getPost('searchStatusResult'),
                    'dtFrom'        => $request->getPost('dtFrom'),
                    'dtTo'          => $request->getPost('dtTo'),
                    'page'          => $request->getPost('page'),
                    'rows'          => $request->getPost('rows'),
            ); 
        }
						
        $data = $this->getLabResult($data,$pageno);
       // $data = new JsonModel($labResult);
				return $data;
				
    }
    
    public function getLabResult($data,$pageno)
    {
        $labResult = $this->getResultTable()->listLabResult($data,$pageno);
	     return $labResult;
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
                        $data['select'] = 'pending';
                        break;
                    case 'report':
                        $data['optId'] = 'proc_rep_status';
                        $data['select'] = '';
                        break;
                    case 'result':
                        $data['optId'] = 'proc_res_status';
                        $data['select'] = '';
                        break;
                    case 'abnormal':
                        $data['optId'] = 'proc_res_abnormal';
                        $data['select'] = '';
                        break;
                }
            }

        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	   $labOptions = $helper->getList($data['optId'],$data['select'],$data['opt']);
        $data = new JsonModel($labOptions);
        return $data;
    }

    public function getResultCommentsAction()
    {
        $request = $this->getRequest();
        $data =array();
            if($request->getPost('prid')){
                $data['procedure_report_id'] = $request->getPost('prid');
            }
        $comments = $this->getResultTable()->listResultComment($data);
        $data = new JsonModel($comments);
        return $data;
    }
	
    public function insertLabCommentsAction()
    {  
	$request=$this->getRequest();
	$data =array();
        if($request->isPost()){
            $data = array(
		    'procedure_report_id' => $request->getPost('procedure_report_id'),
                    'result_status'  => $request->getPost('result_status'),
                    'facility'   => $request->getPost('facility'),
                    'comments'  => $request->getPost('comments'),
                    'notes'        => $request->getPost('notes'),
                    
            );
	    $this->getResultTable()->saveResultComments($data['result_status'],$data['facility'],$data['comments'],$data['procedure_report_id']);
            //return $this->redirect()->toRoute('result');
	    $return	= array();
	    $return[0]  = array('return' => 0, 'report_id' => $data['procedure_report_id']);
	    $arr        = new JsonModel($return);
	    return $arr;
        }
	
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
                            'procedure_report_id'   => $request->getPost('procedure_report_id'),
                            'procedure_result_id'   => $request->getPost('procedure_result_id'),
                            'procedure_order_id'    => $request->getPost('procedure_order_id'),
                            'specimen_num'	    => $request->getPost('specimen_num'),
                            'report_status'  	    => $request->getPost('report_status'),
                            'procedure_order_seq'   => $request->getPost('procedure_order_seq'),
                            'date_report'	    => $request->getPost('date_report'),
                            'date_collected'	    => $request->getPost('date_collected'),
                            'result_code'	    => $request->getPost('result_code'),
                            'result_text'	    => $request->getPost('result_text'),
                            'abnormal'		    => $request->getPost('abnormal'),
                            'result'		    => $request->getPost('result'),
                            'range'		    => $request->getPost('range'),
                            'units'		    => $request->getPost('units'),
                            'result_status'	    => $arr[0],
                            'facility'		    => $arr[1],
                            'comments'		    => $comments,
            );
            $this->getResultTable()->saveResult($data);
            return $this->redirect()->toRoute('result');
        }
        return $this->redirect()->toRoute('result');
    }
    
    /**
     * Result pulling and view
    */
    
    public function getLabResultPDFAction()
    {
        $site_dir           = $GLOBALS['OE_SITE_DIR'];        
        $result_dir         = $site_dir."/lab/result/";
        $result             = array();
        $request = $this->getRequest();
	if($request->isPost()) {
	    $data   = array('procedure_order_id'    => $request->getPost('order_id'));
	} elseif ($request->isGet()) {
	    $data   = array('procedure_order_id'    => $request->getQuery('order_id'));
	}

        $curr_status    = $this->getResultTable()->getOrderStatus($data['procedure_order_id']);
        if($curr_status == "final") {
            $labresultfile    = $this->getResultTable()->getOrderResultFile($data['procedure_order_id']);
        } else {
            $cred = $this->getResultTable()->getClientCredentials($data['procedure_order_id']);
                
            $username   = $cred['login'];
            $password   = $cred['password'];        
            $site_dir   = $_SESSION['site_id'];
            $remote_host   	= trim($cred['remote_host']);
	 
            if(($username == "")||($password == "")) {
                $return[0]  = array('return' => 1, 'msg' => xlt("Lab Credentials not found"));
		$arr        = new JsonModel($return);
		return $arr;
            } else if($remote_host == "") {
                $return[0]  = array('return' => 1, 'msg' =>  xlt("Remote Host not found"));
		$arr        = new JsonModel($return);
		return $arr;
            } else {
                ini_set("soap.wsdl_cache_enabled","0");	
                ini_set('memory_limit', '-1');
                
                $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                try {
                    $client     = new Client(null,$options);
                    $result     = $client->getLabResult($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID
                } catch(\Exception $e){
                    $return[0]  = array('return' => 1, 'msg' => xlt("Could not connect to the web service"));
                    $arr        = new JsonModel($return);
                    return $arr;
                }
            }
        }
        
	// Ajax Handling (Result success or failed)
        if($request->isPost()) {
	    if ($result['status'] == 'failed') {
		$return[0]  = array('return' => 1, 'msg' => xlt($result['content']));
		$arr        = new JsonModel($return);
		return $arr;
	    }else { //IF THE RESULT RETURNS VALID OUTPUT
                if($curr_status <> "final" || $labresultfile == "") { //IF DOESN'T HAVE RESULT FILE
                    $labresultfile  = "labresult_".gmdate('YmdHis').".pdf";
                    if (!is_dir($result_dir)) {
                        mkdir($result_dir,0777,true);
                    }
                    $fp = fopen($result_dir.$labresultfile,"wb");
                    fwrite($fp,base64_decode($result['content']));
                    $status_res = $this->getResultTable()->changeOrderResultStatus($data['procedure_order_id'],"final",$labresultfile);
		    //PULING RESULT DETAILS INTO THE OPENEMR TABLES
		    $this->getLabResultDetails($data['procedure_order_id']);
                }
		
		$return[0]  = array('return' => 0, 'order_id' => $data['procedure_order_id']);
		$arr        = new JsonModel($return);
		return $arr;
	    }
                    
	}
        
        if($labresultfile <> "") {
            
            while(ob_get_level()) {
                ob_get_clean();
            }
            header('Content-Disposition: attachment; filename='.$labresultfile );
            header("Content-Type: application/octet-stream" );
            header("Content-Length: " . filesize( $result_dir.$labresultfile ) );
            readfile( $result_dir.$labresultfile );
            return false;
        }
    }
    
    public function getLabResultDetails($order_id)
    {
        $site_dir               = $GLOBALS['OE_SITE_DIR'];        
        $resultdetails_dir      = $site_dir."/lab/resultdetails/";
        
	$data['procedure_order_id'] = $order_id;
        $cred = $this->getResultTable()->getClientCredentials($data['procedure_order_id']);
	  
	$username       = $cred['login'];
	$password       = $cred['password'];
        $remote_host   	= trim($cred['remote_host']);
	$site_dir       = $GLOBALS['site_id'];
	    
	ini_set("soap.wsdl_cache_enabled","0");	
	ini_set('memory_limit', '-1');
	
	$options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
       	$client     = new Client(null,$options);
	$result     = $client->getLabResultDetails($username,$password,$site_dir,$data['procedure_order_id']);  //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID       
        $labresultdetailsfile  = "labresultdetails_".gmdate('YmdHis').".xml";
	
        if (!is_dir($resultdetails_dir)) {
            mkdir($resultdetails_dir,0777,true);
        }
            
	$fp = fopen($resultdetails_dir.$labresultdetailsfile,"wb");
	fwrite($fp,$result);	
	
	$reader     = new Config\Reader\Xml();
	$xmldata    = $reader->fromFile($resultdetails_dir.$labresultdetailsfile);
	
	//CHECKS IF THE RESULT DETAIL IS ALREADY PULLED
	$pulled_count 	= $this->getResultTable()->getOrderResultPulledCount($order_id);
	if($pulled_count == 0)
	{
	    //SEPERATES EACH TEST DETAILS
	    $test_arr              	= explode("#--#",$xmldata['test_ids']);
	    $result_test_arr        	= explode("!-#@#-!",$xmldata['result_values']);
	    $resultcomments_test_arr    = explode("#-!!-#",$xmldata['res_report_comments']);
	    
	    $test_count = count($test_arr) - 1;
	    
	    /* HARD CODED */
	    $source         = "source";
	    $report_notes   = "report_notes";
	    $comments       = 'comments';
	    /* HARD CODED */
	    
	   // $index = 0;
	    //$order_seq = $this->getResultTable()->getProcedureOrderSequences($data['procedure_order_id']);
	    
	    
	    for($index=0; $index < $test_count; $index++ ) { //ITERATING THROUGH NO OF TESTS IN AN ORDER.
		
		$has_subtest    = 0;    //FLAG FOR INDICATING IF ith TEST HAS SUBTEST OR NOT
		$testdetails    = $test_arr[$index]; // i th  test
	       
		if(trim($testdetails) <> "") { //CHECKING IF THE RESULT CONTAINS DATA FOR THE TEST
		    
		    //SEPERATES TEST SPECIFIC DETAILS
		    $testdetails_arr    = explode("#!#",$testdetails);
		    list($test_code, $profile_title, $code_suffix, $order_title, $spec_collected_time, $spec_received_time, $res_reported_time) = $testdetails_arr;
					
		    $order_seq = $this->getResultTable()->getProcedureOrderSequence($data['procedure_order_id'],$code_suffix);
		    
		    if(empty($order_seq))
			$order_seq = 1;
		    
		    $sql_report     = "INSERT INTO procedure_report (procedure_order_id,procedure_order_seq,date_collected,date_report,source,
						    specimen_num,report_status,review_status,report_notes) VALUES (?,?,?,?,?,?,?,?,?)";
					    
		    $report_inarray = array($data['procedure_order_id'],$order_seq,$spec_collected_time,$res_reported_time,$source,
					    '','','received',$report_notes);
		    
		    $procedure_report_id = $this->getResultTable()->insertProcedureReport($sql_report,$report_inarray);   
		    
		    // RESULT REPORT COMMENTS OF ith TEST	
		    $result_test_comments    = $resultcomments_test_arr[$index];
		    
		    //SEPERATES RESULT REPORT COMMENTS OF EACH SUBTEST OF ith TEST
		    $resultcomments_arr = explode("#!!#",$result_test_comments);
		    
		    //RESULT VALUES/DETAILS OF ith TEST
		    $resultdetails_test      = $result_test_arr[$index];
		    //SEPERATES RESULT VALUES/DETAILS OF EACH SUBTEST OF ith TEST
		    $resultdetails_subtest_arr  = explode("!#@#!",$resultdetails_test);
		    
		    //CHECKING THE NO OF SUBTESTS IN A TEST, IF IT HAS MORE THAN ONE SUBTEST, THE RESULT DETAILS WLL BE ENTERD INTO THE
		    //SUBTEST RESULT DETAILS TABLE, OTHER WISE INSERT DETAILS INTO THE PROCEDURE RESULT TABLE.
		    
		    $no_of_subtests	= substr_count($resultdetails_test, "!#@#!") ; //IF THERE IS ONE SEPERATOR, THERE WILL BE TWO SUBTESTS, SO ADD ONE TO THE NO OF SEPERATORS
		    if(trim($resultdetails_test) <> "") { //CHECKING IF THE RESULT CONTAINS DATA FOR THE SUBTEST OR TEST DETAILS
			if($no_of_subtests   < 2) {
			    $subtest_comments	    = $resultcomments_arr[0];
			    $subtest_comments = str_replace("\n","\\r\\n",$subtest_comments);
			    $subtest_resultdetails_arr  = explode("!@!",$resultdetails_subtest_arr[0]);
			    list($subtest_code,$subtest_name,$result_value,$units,$range,$abn_flag,$result_status,$result_time,$providers_id) = $subtest_resultdetails_arr;
			   
			    $sql_test_result = "INSERT INTO procedure_result(procedure_report_id,result_code,result_text,date,
							    facility,units,result,`range`,abnormal,comments,result_status,order_title,code_suffix,profile_title)
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			    $result_inarray = array($procedure_report_id,$subtest_code,$subtest_name,'','',$units,$result_value,$range,$abn_flag,
						    $subtest_comments,$result_status,$order_title,$code_suffix,$profile_title);
			    $this->getResultTable()->insertProcedureResult($sql_test_result,$result_inarray);
			} else {
			    
			    for($j=0;$j<$no_of_subtests;$j++)
			    {
				$subtest_comments	    = $resultcomments_arr[$j];
	    			$subtest_comments = str_replace("\n","\\r\\n",$subtest_comments);
				$subtest_resultdetails_arr  = explode("!@!",$resultdetails_subtest_arr[$j]);
				list($subtest_code,$subtest_name,$result_value,$units,$range,$abn_flag,$result_status,$result_time,$providers_id) = $subtest_resultdetails_arr;
				
				$sql_subtest_result = "INSERT INTO procedure_subtest_result(procedure_report_id,subtest_code,subtest_desc,
							    result_value,units,`range`,abnormal_flag,result_status,result_time,providers_id,comments,
							    order_title,code_suffix,profile_title)
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$result_inarray = array($procedure_report_id,$subtest_code,$subtest_name,$result_value,$units,$range,
							$abn_flag,$result_status,$result_time,$providers_id,$subtest_comments,$order_title,$code_suffix,$profile_title);
				$this->getResultTable()->insertProcedureResult($sql_subtest_result,$result_inarray);
			    }                        
			}
		    }
		}
		
	    }
	}
    }
    
    public function getLabRequisitionPDFAction()
    {
        $site_dir           = $GLOBALS['OE_SITE_DIR'];            
        $requisition_dir    = $site_dir."/lab/requisition/";
	
        $request    = $this->getRequest();
	if($request->isPost()) {
	    $data   = array('procedure_order_id'    => $request->getPost('order_id'));
	} elseif ($request->isGet()) {
	    $data  = array('procedure_order_id'    => $request->getQuery('order_id'));
	}

        $curr_status    = $this->getResultTable()->getOrderStatus($data['procedure_order_id']);

        if(($curr_status == "requisitionpulled")||($curr_status == "final")) {
            $labrequisitionfile    = $this->getResultTable()->getOrderRequisitionFile($data['procedure_order_id']);                       
        } else {
            $cred       = $this->getResultTable()->getClientCredentials($data['procedure_order_id']);
            $username   = $cred['login'];
            $password   = $cred['password'];
            $site_dir   = $_SESSION['site_id'];
            
            $remote_host   	= trim($cred['remote_host']);
	
            if(($username == "")||($password == "")) {
                $return[0]  = array('return' => 1, 'msg' => "Lab Credentials not found");
		$arr        = new JsonModel($return);
		return $arr;
            } else if($remote_host == "") {
                $return[0]  = array('return' => 1, 'msg' => "Remote Host not found");
		$arr        = new JsonModel($return);
		return $arr;
            } else {           
                ini_set("soap.wsdl_cache_enabled","0");	
                ini_set('memory_limit', '-1');
                
                $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                try {
                    $client     = new Client(null,$options);
                    $result     = $client->getLabRequisition($username,$password,$site_dir,$data['procedure_order_id']); //USERNAME, PASSWORD, SITE DIRECTORY, CLIENT PROCEDURE ORDER ID
                } catch(\Exception $e) {
                    $return[0]  = array('return' => 1, 'msg' => "Could not connect to the web service");
                    $arr        = new JsonModel($return);
                    return $arr;
                }
            }
            
        }
        
	// Ajax Handling (Result success or failed)  
	if($request->isPost()) {
	    if ($result['status'] == 'failed') {
		$return[0]  = array('return' => 1, 'msg' => xlt($result['content']));
		$arr        = new JsonModel($return);
		return $arr;
	    } else { //IF THE REQUISITION RETURNS VALID OUTPUT
                if(($curr_status <> "requisitionpulled")&&($curr_status <> "final")) { //IF THE REQUISITION/RESULT IS ALREADY DOWNLOADED
                    $labrequisitionfile  = "labrequisition_".gmdate('YmdHis').".pdf";           
                    if (!is_dir($requisition_dir)) {
                        mkdir($requisition_dir,0777,true);
                    }
        
                    $fp     = fopen($requisition_dir.$labrequisitionfile,"wb");
                    fwrite($fp,base64_decode($result['content']));
                    $status_res = $this->getResultTable()->changeOrderRequisitionStatus($data['procedure_order_id'],"requisitionpulled",$labrequisitionfile);
                }
		$return[0]  = array('return' => 0, 'order_id' => $data['procedure_order_id']);
		$arr        = new JsonModel($return);
		return $arr;
	    }
	}
        if($labrequisitionfile <> "")
        {
            while(ob_get_level())
            {
                ob_get_clean();
            }
            header('Content-Disposition: attachment; filename='.$labrequisitionfile );
            header("Content-Type: application/octet-stream" );
            header("Content-Length: " . filesize( $requisition_dir.$labrequisitionfile ) );		
            readfile( $requisition_dir.$labrequisitionfile ); 
            return false;
        }
    }
		
		public function resultEntryAction()
    {
				global $pid;
				$form = new ResultForm();
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$statuses_abn = $helper->getList("proc_res_abnormal");
				$form->get('abnormal[]')->setValueOptions($statuses_abn);
				$statuses_units = $helper->getList("proc_unit");
				$form->get('units[]')->setValueOptions($statuses_units);
				if($pid){
						$form->get('patient_id')->setValue($pid);
						$search_pid = $pid;
				}
				$form->get('search_patient')->setValue($this->getResultTable()->getPatientName($pid));
				$request = $this->getRequest();
				$from_dt = null;
				$to_dt = null;
        if ($request->isPost()) {
						$search_pid = $request->getPost()->patient_id;
						$form->get('search_patient')->setValue($this->getResultTable()->getPatientName($search_pid));
						$from_dt = $request->getPost()->search_from_date;
						$to_dt = $request->getPost()->search_to_date;
						$form->get('patient_id')->setValue($search_pid);
						$form->get('search_from_date')->setValue($from_dt);
						$form->get('search_to_date')->setValue($to_dt);
				}
				$this->layout()->res = $this->getResultTable()->listResults($search_pid,$from_dt,$to_dt);
        return array('form' => $form);
    }
		
		public function saveResultEntryAction()
    {
				$request = $this->getRequest();
        if ($request->isPost()) {
						$this->getResultTable()->saveResultEntryDetails($request->getPost());
						return $this->redirect()->toRoute('result',array('action' => 'resultEntry'));
				}
    }
}