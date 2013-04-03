<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

class ResultController extends AbstractActionController
{
    protected $labTable;
	
    public function indexAction()
    {
     $labresult1=$this->resultShowAction(); 
	  $viewModel = new ViewModel(array(
       "labresults"=>$labresult1
		));
	return $viewModel;	  
		
    }
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\ResultTable');
        }
        return $this->labTable;
    }
    
    public function resultShowAction()
    {
        $request = $this->getRequest();
        $data =array();
        if($request->isPost()){
            $data = array(
                    'statusReport'  => $request->getPost('statusReport'),
                    'statusOrder'   => $request->getPost('statusOrder'),
                    'statusResult'  => $request->getPost('statusResult'),
                    'dtFrom'        => $request->getPost('dtFrom'),
                    'dtTo'          => $request->getPost('dtTo'),
                    'page'          => $request->getPost('page'),
                    'rows'          => $request->getPost('rows'),
            ); 
        }
        $data = $this->getLabResult($data);
		  $file = fopen("D:/test9.txt","w");
           fwrite($file,print_r($data,1));
           fclose($file);
		   
       // $data = new JsonModel($labResult);
	
		return $data;
				
    }
    
    public function getLabResult($data)
    {
        $labResult = $this->getLabTable()->listLabResult($data);
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
                $data['procedure_result_id'] = $request->getPost('prid');
            }
        $comments = $this->getLabTable()->listResultComment($data);
        $data = new JsonModel($comments);
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
            $this->getLabTable()->saveResult($data);
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

        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);
        if($curr_status == "final") {
            $labresultfile    = $this->getLabTable()->getOrderResultFile($data['procedure_order_id']);
        } else {
            $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
                
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
                    $status_res = $this->getLabTable()->changeOrderResultStatus($data['procedure_order_id'],"final",$labresultfile);
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
        $cred = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
	  
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
	$pulled_count 	= $this->getLabTable()->getOrderResultPulledCount($order_id);
	$fp = fopen("D:/sql.txt","w");
		    fwrite($fp,"\n Pulled count ....................  :".print_r($pulled_count,1));
	if($pulled_count == 0)
	{
	    //SEPERATES EACH TEST DETAILS
	    $test_arr              	= explode("#--#",$xmldata['test_ids']);
	    $result_test_arr        	= explode("!-#@#-!",$xmldata['result_values']);
	    $resultcomments_test_arr    = explode("#-!!-#",$xmldata['res_report_comments']);
	    
	    $fp	= fopen("D:/abc.txt", "a");
		    fwrite($fp," \n XML  array ".print_r($result,1));
	    
	    
	    $fp	= fopen("D:/abc.txt", "a");
		    fwrite($fp," \n\n\n res_report_comments ".$xmldata['res_report_comments']);
	    
	    $fp	= fopen("D:/abc.txt", "a");
		    fwrite($fp," \n test comments  array ".print_r($resultcomments_test_arr,1));
		    
		
	    /* HARD CODED */
	    $source         = "source";
	    $report_notes   = "report_notes";
	    $comments       = 'comments';
	    /* HARD CODED */
	    
	    $index = 0;
	    $order_seq = $this->getLabTable()->getProcedureOrderSequences($data['procedure_order_id']);        
	    foreach($order_seq as $seq) { //ITERATING THROUGH NO OF TESTS IN AN ORDER.
		
		$has_subtest    = 0;    //FLAG FOR INDICATING IF ith TEST HAS SUBTEST OR NOT
		$testdetails    = $test_arr[$index]; // i th  test
	       
		if(trim($testdetails) <> "") { //CHECKING IF THE RESULT CONTAINS DATA FOR THE TEST
		    
		    //SEPERATES TEST SPECIFIC DETAILS
		    $testdetails_arr    = explode("#!#",$testdetails);
		    list($test_code, $spec_collected_time, $spec_received_time, $res_reported_time) = $testdetails_arr;
		  
		    $sql_report     = "INSERT INTO procedure_report (procedure_order_id,procedure_order_seq,date_collected,date_report,source,
						    specimen_num,report_status,review_status,report_notes) VALUES (?,?,?,?,?,?,?,?,?)";
					    
		    $report_inarray = array($data['procedure_order_id'],$seq['procedure_order_seq'],$spec_collected_time,$res_reported_time,$source,
					    '','','received',$report_notes);
		    $procedure_report_id = $this->getLabTable()->insertProcedureReport($sql_report,$report_inarray);   
		    
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
		    
		    $fp	= fopen("D:/abc.txt", "a");
		    fwrite($fp," \n test comments  ".$result_test_comments);
		    
		    $no_of_subtests	= substr_count($resultdetails_test, "!#@#!") ; //IF THERE IS ONE SEPERATOR, THERE WILL BE TWO SUBTESTS, SO ADD ONE TO THE NO OF SEPERATORS
		     $fp	= fopen("D:/abc.txt", "a");
		    fwrite($fp," \n no of subtests  ".$no_of_subtests);
		    if(trim($resultdetails_test) <> "") { //CHECKING IF THE RESULT CONTAINS DATA FOR THE SUBTEST OR TEST DETAILS
			if($no_of_subtests   < 2) {
			    $subtest_comments	    = $resultcomments_arr[0];
				
			    $subtest_resultdetails_arr  = explode("!@!",$resultdetails_subtest_arr[0]);
			    list($subtest_code,$subtest_name,$result_value,$units,$range,$abn_flag,$result_status,$result_time,$providers_id) = $subtest_resultdetails_arr;
			   
			    $sql_test_result = "INSERT INTO procedure_result(procedure_report_id,result_code,result_text,date,
							    facility,units,result,`range`,abnormal,comments,result_status)
							VALUES (?,?,?,?,?,?,?,?,?,?,?)";
			    $result_inarray = array($procedure_report_id,$subtest_code,$subtest_name,'','',$units,$result_value,$range,$abn_flag,
						    $subtest_comments,$result_status);            
			    $this->getLabTable()->insertProcedureResult($sql_test_result,$result_inarray);
			} else {
			    
			    for($j=0;$j<$no_of_subtests;$j++)
			    {
				$subtest_comments	    = $resultcomments_arr[$j];
				
				$subtest_resultdetails_arr  = explode("!@!",$resultdetails_subtest_arr[$j]);
				list($subtest_code,$subtest_name,$result_value,$units,$range,$abn_flag,$result_status,$result_time,$providers_id) = $subtest_resultdetails_arr;
				
				$sql_subtest_result = "INSERT INTO procedure_subtest_result(procedure_report_id,subtest_code,subtest_desc,
							    result_value,units,`range`,abnormal_flag,result_status,result_time,providers_id,comments)
							VALUES (?,?,?,?,?,?,?,?,?,?,?)";
				$result_inarray = array($procedure_report_id,$subtest_code,$subtest_name,$result_value,$units,$range,
							$abn_flag,$result_status,$result_time,$providers_id,$subtest_comments);
				$this->getLabTable()->insertProcedureResult($sql_subtest_result,$result_inarray);
			    }                        
			}
		    }
		}
		$index++;
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

        $curr_status    = $this->getLabTable()->getOrderStatus($data['procedure_order_id']);

        if(($curr_status == "requisitionpulled")||($curr_status == "final")) {
            $labrequisitionfile    = $this->getLabTable()->getOrderRequisitionFile($data['procedure_order_id']);                       
        } else {
            $cred       = $this->getLabTable()->getClientCredentials($data['procedure_order_id']);
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
                    $status_res = $this->getLabTable()->changeOrderRequisitionStatus($data['procedure_order_id'],"requisitionpulled",$labrequisitionfile);
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
}