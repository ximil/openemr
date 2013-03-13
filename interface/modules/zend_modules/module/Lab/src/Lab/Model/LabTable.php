<?php
namespace Lab\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

use Zend\Config;
use Zend\Config\Writer;
use Zend\Soap\Client;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class LabTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
	
	public function listLabOptions($data)
	{
		if (isset($data['option_id'])) { 
			$where = " AND option_id='$data[option_id]'";
		}
		$sql = "SELECT option_id, title FROM list_options 
						WHERE list_id='" . $data['optId'] . "' $where 
						ORDER BY seq, title";
		$result = sqlStatement($sql);
		$arr = array();
		$i = 0;
		if ($data['opt'] == 'search') {
			$arr[$i] = array (
				'option_id' => 'all',
				'title' => xlt('All'),
				'selected' => TRUE,
			);
			$i++;
		}
		
		while($row = sqlFetchArray($result)) {
			$arr[$i] = array (
				'option_id' => htmlspecialchars($row['option_id'],ENT_QUOTES),
				'title' => xlt($row['title']),
			);
			if ($data['optId'] == 'ord_status' && $row['option_id'] == 'pending') {
				$arr[$i]['selected'] = true;
			}
			$i++;
			
		}
		return $arr;
	}
	
	public function listLabStatus($data)
	{
		$sql = "SELECT option_id, title FROM list_options 
						WHERE list_id='proc_rep_status' 
						ORDER BY seq, title";
		$result = sqlStatement($sql);
		$arr = array();
		$i = 0;
		if ($data['opt'] == 'search') {
			$arr[$i]['option_id'] = 'all';
			$arr[$i]['title'] = 'All';
			$arr[$i]['selected'] = true;
		}
		while($row = sqlFetchArray($result)) {
			$arr[] = $row;
		}
		return $arr;
	}
	
	public function listLabAbnormal()
	{
		$sql = "SELECT option_id, title FROM list_options 
						WHERE list_id='proc_res_abnormal' 
						ORDER BY seq, title";
		$result = sqlStatement($sql);
		$arr = array();
		while($row = sqlFetchArray($result)) {
			$arr[] = $row;
		}
		return $arr;
	}
	
	public function listResultComment($data)
	{
		$sql = "SELECT result_status, facility, comments FROM procedure_result 
						WHERE procedure_result_id='" . $data['procedure_result_id'] . "'";
		$result = sqlStatement($sql);
		$string = '';
		$arr = array();
		while($row = sqlFetchArray($result)) {
			
			$result_notes = '';
			$i = strpos($row['comments'], "\n");
			if ($i !== FALSE) {
				$result_notes = trim(substr($row['comments'], $i + 1));
				$result_comments = substr($row['comments'], 0, $i);
			}
			$result_comments = trim($result_comments);
			$string = $row['result_status'] . '|' . $row['facility'] . '|' . $result_comments . '|' . $result_notes;
			$title = $this->listLabOptions(array('option_id'=> $row['result_status'], 'optId'=> 'proc_res_status'));
			$arr[0]['title'] = $title[0]['title'];
			$arr[0]['result_status'] = trim($row['result_status']);
			$arr[0]['facility'] = $row['facility'];
			$arr[0]['comments'] = $result_comments;
			$arr[0]['notes'] = $result_notes;
			$arr[0]['selected'] = true;
			
		}
		return $arr;
	}
	
	public function listLabResult($data)
	{	global $pid;
		$flagSearch = 0;
//$data['statusOrder'] = 'complete';
//$data['statusReport'] = 'final';
		if (isset($data['statusReport']) && $data['statusReport'] != 'all') {
			$statusReport = $data['statusReport'];
			$flagSearch = 1;
		}
		if (isset($data['statusOrder']) && $data['statusOrder'] == 'pending') {
			$statusOrder = $data['statusOrder'];
		} elseif (isset($data['statusOrder'])){
			if ($data['statusOrder'] != 'all') {
				$statusOrder = $data['statusOrder'];
			}
			$flagSearch = 1;
		} 
		
		if (isset($data['statusResult']) && $data['statusResult'] != 'all') {
			$statsResult = $data['statusResult'];
		}
		if (isset($data['dtFrom'])) {
			$dtFrom = $data['dtFrom'];
		}
		if (isset($data['dtTo'])) {
			$dtTo = $data['dtTo'];
		}
		
		if (isset($data['dtFrom']) && $data['dtTo'] == '') {
			$dtTo = $data['dtFrom'];
		}
		
		$form_review = 1; // review mode
		$lastpoid = -1;
		$lastpcid = -1;
		$lastprid = -1;
		$encount = 0;
		$lino = 0;
		$extra_html = '';
		$lastrcn = '';
		$facilities = array();
		//$pid = 1;
		$selects =
			"CONCAT(pa.lname, ',', pa.fname) AS patient_name, po.encounter_id, po.lab_id, pp.remote_host, pp.login, pp.password, po.order_status, po.procedure_order_id, po.date_ordered, pc.procedure_order_seq, " .
			"pt1.procedure_type_id AS order_type_id, pc.procedure_name, " .
			"pr.procedure_report_id, pr.date_report, pr.date_collected, pr.specimen_num, " .
			"pr.report_status, pr.review_status";

		$joins =
			"JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
			"LEFT JOIN procedure_type AS pt1 ON pt1.lab_id = po.lab_id AND pt1.procedure_code = pc.procedure_code " .
			"LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
			"pr.procedure_order_seq = pc.procedure_order_seq 
			LEFT JOIN patient_data AS pa ON pa.id=po.patient_id 
			LEFT JOIN procedure_providers AS pp ON pp.ppid=po.lab_id";
		$groupby = '';
		if ($flagSearch == 1) {
			//$groupby = "GROUP By po.procedure_order_id";
		}
		
		$orderby =
			"po.date_ordered, po.procedure_order_id, " .
			"pc.procedure_order_seq, pr.procedure_report_id";

		$where = "1 = 1";
		if($statusReport) {
			$where .= " AND pr.report_status='$statusReport'";
		}
		if($statusOrder) {
			$where .= " AND po.order_status='$statusOrder'";
		}
		
		if ($dtFrom) {
			$where .= " AND po.date_ordered BETWEEN '$dtFrom' AND '$dtTo'";
		}
		
		$start = isset($data['page']) ? $data['page'] :  0;
		$rows = isset($data['rows']) ? $data['rows'] : 20;

		if ($data['page'] == 1) {
			$start = $data['page'] - 1;
		} elseif ($data['page'] > 1) {
			$start = (($data['page'] - 1) * $rows);
		}

		$sql = "SELECT $selects " .
					  "FROM procedure_order AS po " .
					  "$joins " .
					  "WHERE po.patient_id = '$pid' AND $where " .
					  "$groupby ORDER BY $orderby LIMIT $start, $rows";
//echo $sql;
      	$result = sqlStatement($sql);
		$arr1 = array();
		
		$i = 0;
		while($row = sqlFetchArray($result)) {
			$order_type_id  = empty($row['order_type_id']) ? 0 : ($row['order_type_id' ] + 0);
			$order_id       = empty($row['procedure_order_id']) ? 0 : ($row['procedure_order_id' ] + 0);
			$order_seq      = empty($row['procedure_order_seq']) ? 0 : ($row['procedure_order_seq'] + 0);
			$report_id      = empty($row['procedure_report_id']) ? 0 : ($row['procedure_report_id'] + 0);
			$date_report    = empty($row['date_report'     ]) ? '' : $row['date_report'];
			$date_collected = empty($row['date_collected'  ]) ? '' : substr($row['date_collected'], 0, 16);
			$specimen_num   = empty($row['specimen_num'    ]) ? '' : $row['specimen_num'];
			$report_status  = empty($row['report_status'   ]) ? '' : $row['report_status']; 
			$review_status  = empty($row['review_status'   ]) ? 'received' : $row['review_status'];
			$remoteHost		= empty($row['remote_host'      ]) ? '' : $row['remote_host' ];
			$remoteUser		= empty($row['login']) ? '' : $row['login' ];
			$remotePass		= empty($row['password']) ? '' : $row['password' ];
			
			if ($flagSearch == 0) {
				if ($form_review) {
					if ($review_status == "reviewed") continue;
				} else {
					if ($review_status == "received") continue;
				}
			}

			$selects = "pt2.procedure_type, pt2.procedure_code, pt2.units AS pt2_units, " .
						"pt2.range AS pt2_range, pt2.procedure_type_id AS procedure_type_id, " .
						"pt2.name AS name, pt2.description, pt2.seq AS seq, " .
						"ps.procedure_result_id, ps.result_code AS result_code, ps.result_text, ps.abnormal, ps.result, " .
						"ps.range, ps.result_status, ps.facility, ps.comments, ps.units, ps.comments";
			
			// Skip LIKE Cluse for Ext Lab if not set the procedure code or parent
			$pt2cond = '';
			$editor = 0;
			if ($remoteHost != '' && $remoteUser != '' && $remotePass != '') {
				$pt2cond = "pt2.parent = $order_type_id ";
				$editor = 1;
			} else {
				$pt2cond = "pt2.parent = $order_type_id AND " .
						"(pt2.procedure_type LIKE 'res%' OR pt2.procedure_type LIKE 'rec%')";
			}
			
			/* $pt2cond = "pt2.parent = $order_type_id AND " .
						"(pt2.procedure_type LIKE 'res%' OR pt2.procedure_type LIKE 'rec%')"; */
			$pscond = "ps.procedure_report_id = $report_id";

			$joincond = "ps.result_code = pt2.procedure_code";
			if($statusResult) {
				$where .= " AND ps.result_status='$statusResult'";
			}
			
			$query = "(SELECT $selects FROM procedure_type AS pt2 " .
							"LEFT JOIN procedure_result AS ps ON $pscond AND $joincond " .
							"WHERE $pt2cond" .
							") UNION (" .
							"SELECT $selects FROM procedure_result AS ps " .
							"LEFT JOIN procedure_type AS pt2 ON $pt2cond AND $joincond " .
							"WHERE $pscond) " .
							"ORDER BY seq, name, procedure_type_id, result_code";

			$rres = sqlStatement($query);

			while ($rrow = sqlFetchArray($rres)) {
				$restyp_code      = empty($rrow['procedure_code'  ]) ? '' : $rrow['procedure_code'];
				$restyp_type      = empty($rrow['procedure_type'  ]) ? '' : $rrow['procedure_type'];
				$restyp_name      = empty($rrow['name'            ]) ? '' : $rrow['name'];
				$restyp_units     = empty($rrow['pt2_units'       ]) ? '' : $rrow['pt2_units'];
				$restyp_range     = empty($rrow['pt2_range'       ]) ? '' : $rrow['pt2_range'];

				$result_id        = empty($rrow['procedure_result_id']) ? 0 : ($rrow['procedure_result_id'] + 0);
				$result_code      = empty($rrow['result_code'     ]) ? $restyp_code : $rrow['result_code'];
				$result_text      = empty($rrow['result_text'     ]) ? $restyp_name : $rrow['result_text'];
				$result_abnormal  = empty($rrow['abnormal'        ]) ? '' : $rrow['abnormal'];
				$result_result    = empty($rrow['result'          ]) ? '' : $rrow['result'];
				$result_units     = empty($rrow['units'           ]) ? $restyp_units : $rrow['units'];
				$result_facility  = empty($rrow['facility'        ]) ? '' : $rrow['facility'];
				$result_comments  = empty($rrow['comments'        ]) ? '' : $rrow['comments'];
				$result_range     = empty($rrow['range'           ]) ? $restyp_range : $rrow['range'];
				$result_status    = empty($rrow['result_status'   ]) ? '' : $rrow['result_status'];
				
				if ($lastpoid != $order_id || $lastpcid != $order_seq) {
					$lastprid = -1;
					if ($lastpoid != $order_id) {
						if ($arr1[$i - 1]['procedure_name'] != $row['procedure_name'] || $arr1[$i - 1]['order_id'] != $row['order_id']) {
							$arr1[$i]['date_ordered'] = $row['date_ordered'];
						}
					}
				}
				/* if ($arr1[$i - 1]['date_ordered'] != $row['date_ordered']) {
					$arr1[$i]['date_ordered'] = $row['date_ordered'];
				} */
				if ($arr1[$i - 1]['procedure_name'] != $row['procedure_name'] || $arr1[$i - 1]['order_id'] != $row['order_id']) {
					$arr1[$i]['procedure_name'] = xlt($row['procedure_name']);
					$arr1[$i]['date_report'] = $date_report;
					$arr1[$i]['date_collected'] = $date_collected;
					
					$arr1[$i]['order_id'] = $order_id;
					$arr1[$i]['patient_name'] = xlt($row['patient_name']);
					$arr1[$i]['encounter_id'] = $row['encounter_id'];

					$title = $this->listLabOptions(array('option_id'=> $row['order_status'], 'optId'=> 'ord_status'));
					//$arr1[$i]['order_title'] = isset($title) ? xlt($title[0]['title']) : '';
					$arr1[$i]['order_status'] = isset($title) ? xlt($title[0]['title']) : '';
					//$arr1[$i]['order_status'] = xlt($row['order_status']);
				}
				$arr1[$i]['specimen_num'] = xlt($specimen_num);
				$title = $this->listLabOptions(array('option_id'=> $report_status, 'optId'=> 'proc_rep_status'));
				//$arr1[$i]['report_status'] = isset($title) ? xlt($title[0]['title']) : '';
				$arr1[$i]['report_status'] = xlt($report_status);
				$arr1[$i]['report_title'] = isset($title) ? xlt($title[0]['title']) : '';
				$arr1[$i]['order_type_id'] = $order_type_id ;
				$arr1[$i]['procedure_order_id'] = $order_id;
				$arr1[$i]['procedure_order_seq'] = $order_seq;
				$arr1[$i]['procedure_report_id'] = $report_id;
				$arr1[$i]['review_status'] = xlt($review_status);
				$arr1[$i]['procedure_code'] = xlt($restyp_code);
				$arr1[$i]['procedure_type'] = xlt($restyp_type);
				$arr1[$i]['name'] = xlt($restyp_name);
				$arr1[$i]['pt2_units'] = xlt($restyp_units);
				$arr1[$i]['pt2_range'] = xlt($restyp_range);
				$arr1[$i]['procedure_result_id'] = $result_id;
				$arr1[$i]['result_code'] = xlt($result_code);
				$arr1[$i]['result_text'] = xlt($result_text);
				
				$title = $this->listLabOptions(array('option_id'=> $result_abnormal, 'optId'=> 'proc_res_abnormal'));
				
				$arr1[$i]['abnormal_title'] = isset($title) ? xlt($title[0]['title']) : '';
				$arr1[$i]['abnormal'] = xlt($result_abnormal);
				
				$arr1[$i]['result'] = xlt($result_result);
				$arr1[$i]['units'] = xlt($result_units);
				$arr1[$i]['facility'] = xlt($facility);
				$arr1[$i]['comments'] = xlt($result_comments);
				$arr1[$i]['range'] = xlt($result_range);
				$arr1[$i]['result_status'] = xlt($result_status);
				$arr1[$i]['editor'] = $editor;
				$i++;
				$lastpoid = $order_id;
				$lastpcid = $order_seq;
				$lastprid = $report_id;
			}
		}
		$arr1[$i]['total'] = $i-1;
		//echo '<pre>'; print_r($arr1); echo '</pre>';
		return $arr1;
	}
	
	public function saveResult($data)
	{	
		$report_id		= $data['procedure_report_id'];
		$order_id 		= $data['procedure_order_id'];
		$result_id 		= $data['procedure_result_id'];
		$specimen_num 	= $data['specimen_num'];
		$report_status 	= $data['report_status'];
		$order_seq 		= $data['procedure_order_seq'];
		$date_report 	= $data['date_report'];
		$date_collected = $data['date_collected'];
		
		$result_code			= $data['result_code'];
		$procedure_report_id	= $data['procedure_report_id'];
		$result_text			= $data['result_text'];
		$abnormal				= $data['abnormal'];
		$result					= $data['result'];
		$range					= $data['range'];
		$units					= $data['units'];
		$result_status			= $data['result_status'];
		$facility				= $data['facility'];
		$comments				= $data['comments'];
		 
		if (!empty($date_report)) {
			if ($report_id > 0) {
				$arr = array(
					$order_id,
					$specimen_num,
					$report_status,
					$order_seq,
					$date_report,
					$date_collected,
					'reviewed',
					$report_id,
				);
				
				/* $sql = "UPDATE procedure_report 
									SET procedure_order_id='$order_id', 
									specimen_num='$specimen_num', 
									report_status='$report_status', 
									procedure_order_seq='$order_seq', 
									date_report='$date_report', 
									date_collected='$date_collected', 
									review_status = ? 								
									WHERE procedure_report_id = '$report_id'";
				sqlStatement($sql); */
				$sql = "UPDATE procedure_report 
									SET procedure_order_id= ?, 
									specimen_num= ?, 
									report_status= ?, 
									procedure_order_seq= ?, 
									date_report= ?, 
									date_collected= ?, 
									review_status = ?  								
									WHERE procedure_report_id = ?";
				sqlQuery($sql, $arr);
			} else {
				$arr = array(
						$order_id,
						$specimen_num,
						$report_status,
						$order_seq,
						$date_report,
						$date_collected,
						'reviewed',
					);
				/* $sql = "INSERT INTO procedure_report 
									SET procedure_order_id='$order_id', 
									specimen_num='$specimen_num', 
									report_status='$report_status',
									procedure_order_seq='$order_seq', 
									date_report='$date_report',
									date_collected='$date_collected' 								
									review_status = 'reviewed'"; */
				$sql = "INSERT INTO procedure_report 
									SET procedure_order_id= ?, 
									specimen_num= ?, 
									report_status= ?,
									procedure_order_seq= ?, 
									date_report= ?,
									date_collected= ?, 								
									review_status = ?";
				$report_id = sqlInsert($sql, $arr);
			}
		}
		if (!empty($date_report)) {
			if ($result_id > 0) {
				$arr = array(
					$report_id,
					$result_code,
					$result_text,
					$abnormal,
					$result,
					$range,
					$units,
					$result_status,
					$facility,
					$comments,
					$result_id,
				);
				/* $sql = "UPDATE procedure_result 
							SET procedure_report_id='$report_id', 
								result_code='$result_code', 
								result_text='$result_text', 
								abnormal='$abnormal', 
								result='$result', 
								`range`='$range', 
								units='$units', 
								result_status='$result_status', 
								facility='$facility', 
								comments='$comments'								
								WHERE procedure_result_id = '$result_id'";
				sqlStatement($sql); */
				$sql = "UPDATE procedure_result 
							SET procedure_report_id= ?, 
								result_code= ?, 
								result_text= ?, 
								abnormal= ?, 
								result= ?, 
								`range`= ?, 
								units= ?, 
								result_status= ?, 
								facility= ?, 
								comments= ?							
								WHERE procedure_result_id = ?";
				sqlQuery($sql, $arr);
			} else {
				$arr = array(
					$report_id,
					$result_code,
					$result_text,
					$abnormal,
					$result,
					$range,
					$units,
					$result_status,
					$facility,
					$comments,
				);
				$sql = "INSERT INTO procedure_result 
							SET procedure_report_id= ?, 
								result_code= ?, 
								result_text= ?, 
								abnormal= ?, 
								result= ?, 
								`range`= ?, 
								units= ?, 
								result_status= ?, 
								facility= ?, 
								comments= ?";
				sqlInsert($sql, $arr);
			}
		}
	}
//   public function saveLab(Lab $lab,$aoe)
//    {
//	$fh = fopen("D:/SAVELAB.txt","a");
//	fwrite($fh,print_r($lab->procedures,1));
//	fwrite($fh,print_r($lab->procedure_code,1));
//	fwrite($fh,print_r($aoe,1));
//	$procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,order_status,
//		  diagnoses,patient_instructions,lab_id,psc_hold) VALUES(?,?,?,?,?,?,?,?,?,?,?)",
//		  array($lab->provider,$lab->pid,$lab->encounter,$lab->timecollected,$lab->orderdate,$lab->priority,$lab->status,
//			"ICD9:".$lab->diagnoses,$lab->patient_instructions,$lab->lab_id,$lab->specimencollected));
//	$seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix)
//		     VALUES (?,?,?,?)",array($procedure_type_id,$lab->procedurecode,$lab->procedures,$lab->proceduresuffix));
//	$fh = fopen("D:/AAOOEE.txt","a");
//	fwrite($fh,print_r($aoe,1));
//	foreach($aoe as $ProcedureOrder=>$QuestionArr){
//	    if($ProcedureOrder==$lab->procedurecode){
//		foreach($QuestionArr as $Question=>$Answer){
//		    sqlStatement("INSERT INTO procedure_answers (procedure_order_id,procedure_order_seq,question_code,answer) VALUES (?,?,?,?)",
//		    array($procedure_type_id,$seq,$Question,$Answer));
//		}
//	    }
//	}
//	return $procedure_type_id;
//    }
    public function insertAoe($procedure_type_id,$seq,$aoe,$procedure_code_i){
	foreach($aoe as $ProcedureOrder=>$QuestionArr){
	    if($ProcedureOrder==$procedure_code_i){
		foreach($QuestionArr as $Question=>$Answer){
		    sqlStatement("INSERT INTO procedure_answers (procedure_order_id,procedure_order_seq,question_code,answer) VALUES (?,?,?,?)",
		    array($procedure_type_id,$seq,$Question,$Answer));
		}
	    }
	}
    }
    public function insertProcedureMaster($post){
	$procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,
				       order_status,patient_instructions,lab_id,psc_hold,billto,internal_comments) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)",
		    array($post['provider'],$post['patient_id'],$post['encounter_id'],$post['timecollected'],$post['orderdate'],$post['priority'],
		    'pending',$post['patient_instructions'],$post['lab_id'],$post['specimencollected'],
		    $post['billto'],$post['internal_comments']));
	return $procedure_type_id;
    }
    public function saveLab($post,$aoe)
    {
	$papArray = array();
	$specimenState = array();
	$procedure_type_id_arr = array();
	$j=0;
	$prevState = '';
	for($i=0;$i<sizeof($post['procedures']);$i++){
	    $PRow = sqlQuery("SELECT * FROM procedure_type WHERE procedure_code=? AND suffix=? ORDER BY pap_indicator,specimen_state",
			     array($post['procedure_code'][$i],$post['procedure_suffix'][$i]));
	    if(!isset(${$PRow['specimen_state']."_j"}))
	    ${$PRow['specimen_state']."_j"} = 0;
	    if($PRow['pap_indicator']=="P"){
		$papArray[$post['procedure_code'][$i]."|-|".$post['procedure_suffix'][$i]]['procedure'] = $PRow['name'];
		$papArray[$post['procedure_code'][$i]."|-|".$post['procedure_suffix'][$i]]['diagnoses'] = $post['diagnoses'][$i];
	    }
	    else{
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure_code'] = $PRow['procedure_code'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure'] = $PRow['name'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure_suffix'] = $PRow['suffix'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['diagnoses'] = $post['diagnoses'][$i];
		${$PRow['specimen_state']."_j"}++;
	    }
	}
	if(sizeof($papArray)>0){
	    foreach($papArray as $procode_suffix=>$pronameArr ){
	    	$PSArray = explode("|-|",$procode_suffix);
		$procode = $PSArray[0];
		$prosuffix = $PSArray[1];
		$proname = $pronameArr['procedure'];
		$diagnoses = $pronameArr['diagnoses'];
		$PAPprocedure_type_id = $this->insertProcedureMaster($post);
		$procedure_type_id_arr[] = $PAPprocedure_type_id;
		$PAPseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
		     VALUES (?,?,?,?,?)",array($PAPprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses));
		$this->insertAoe($PAPprocedure_type_id,$PAPseq,$aoe,$procode);
	    }
	}
	if($post['specimencollected']=="onsite"){
	    if(sizeof($specimenState)>0){
		foreach($specimenState as $k=>$vArray){
		    $SPEprocedure_type_id = $this->insertProcedureMaster($post);
		    $procedure_type_id_arr[] = $SPEprocedure_type_id;
		    for($i=0;$i<sizeof($vArray);$i++){
			$procode = $vArray[$i]['procedure_code'];
			$proname = $vArray[$i]['procedure'];
			$prosuffix = $vArray[$i]['procedure_suffix'];
			$diagnoses = $vArray[$i]['diagnoses'];
			$SPEseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
					VALUES (?,?,?,?,?)",array($SPEprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses));
			$this->insertAoe($SPEprocedure_type_id,$SPEseq,$aoe,$procode);
		    }
		}
	    }
	}
	else{
	    for($i=0;$i<sizeof($post['procedures']);$i++){
		$procedure_code = $post['procedure_code'][$i];
		$procedure_suffix = $post['procedure_suffix'][$i];
		if(array_key_exists($procedure_code."|-|".$procedure_suffix,$papArray)) continue;
		if($i==0){
		$procedure_type_id = $this->insertProcedureMaster($post);
		$procedure_type_id_arr[] = $procedure_type_id;
		}
		$seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
		    VALUES (?,?,?,?,?)",array($procedure_type_id,$post['procedure_code'][$i],$post['procedures'][$i],$post['procedure_suffix'][$i]));
		$this->insertAoe($procedure_type_id,$seq,$aoe,$post['procedure_code'][$i],$post['diagnoses'][$i]);
	    }
	}
	return $procedure_type_id_arr;
    }
    
//    public function listLabLocation($inputString)
//    {
//	$sql = "SELECT * FROM labs WHERE lab_name=?,array($inputString)";
//	$result = sqlStatement($sql);
//	$i = 0;
//	
//	while($row=sqlFetchArray($res)) {
//		$rows[$i] = array (
//			'value' => $row['ppid'],
//			'label' => $row['name'],
//		);
//		$i++;
//	}
//	return $rows;
//
//    }
    public function listProcedures($inputString,$labId)
    {
	$sql = "SELECT * FROM procedure_type AS pt WHERE pt.lab_id=? AND (name LIKE ? OR procedure_code LIKE ?) AND pt.activity=1";
	$result = sqlStatement($sql,array($labId,$inputString."%",$inputString."%"));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[] =  htmlspecialchars($tmp['procedure_type_id'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['procedure_code'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['suffix'],ENT_QUOTES).'|-|'.htmlspecialchars($tmp['name'],ENT_QUOTES);
	}
	return $arr;
    }
    
    public function listAOE($procedureCode,$labId){
	$sql = "SELECT * FROM procedure_questions WHERE lab_id=? AND procedure_code=? AND activity=1 ORDER BY seq ASC";
	$result = sqlStatement($sql,array($labId,$procedureCode));
	$arr = array();
	$i = 0;
	//$inputFilter = new InputFilter();
	//$factory     = new InputFactory();

    //$form->setInputFilter($inputFilter);
	while($tmp = sqlFetchArray($result)) {
	//    $inputFilter->add($factory->createInput(array(
	//	'name'     => 'AOE_'.$procedureCode."_".$tmp['question_code']
	//    )));
	    $arr[] = htmlspecialchars($tmp['question_text'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['required'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['question_code'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['tips'],ENT_QUOTES);
	}
	return $arr;
    }
	
    /**
    * Vipin
    **/
    
    public function getColumns($result)
    {
	$result_columns	= array();
	foreach($result as $res)
	{			
	    foreach($res as $key => $val)
	    {
		if(is_numeric($key))
		    continue;
		$result_columns[] = $key;							
	    }
	    break;
	}
	return $result_columns;
    }

    public function columnMapping($column_map,$result_col)
    {
	$table_sql	= array();
	
	foreach($result_col as $col)
	{
	    if(($column_map[$col]['colconfig']['table'] <> "")&&($column_map[$col]['colconfig']['column'] <> ""))
	    {
		$table 		= $column_map[$col]['colconfig']['table'];
		$column 	= $column_map[$col]['colconfig']['column'];
		
		$table_sql[$table][$col]	= $column;				
	    }
	    //echo "<br>";
	}
	return $table_sql;		
    }
    /*    
    function importData($result,$column_map)
    {
	    
	$result_col	= $this->getColumns($result);
	
	$mapped_tables	= $this->columnMapping($column_map,$result_col);
	
	//$count = 0;
	//$insert = 0;
	foreach($result as $res)
	{
	    foreach($result_col as $col)
	    {
		${$col}	= $res[$col];//GETTING IMPORTED VALUES
	    }
	    //echo "<br>";
	    foreach($mapped_tables as $table => $columns)
	    {
		//echo $key." => ".$val;
		//$table_name	= $table;
		
		$value_arr	= array();
		foreach($columns as $servercol => $column)
		{
		    if($column_map[$servercol]['colconfig']['insert_id'] == "1")
		    {
			$$servercol = $insert_id;
		    }
		    if($column_map[$servercol]['colconfig']['value_map'] == "1")
		    {
			//$value_map['test_status_indicator'] 	= array('A' => "1", 'I' => "0");
			$$servercol = $column_map[$servercol]['valconfig'][$$servercol];
		    }
		    $value_arr[] =  ${$servercol};
		}
		
		$fields		= implode(",",$columns);
		$col_count	= count($columns);
		$field_vars	= "$".implode(",$",$columns);
		$params		= rtrim(str_repeat("?,",$col_count),",");
		
		echo "<br>".$sql	= "INSERT INTO ".$table."(".$fields.") VALUES (".$params.")";
		echo "<br>".$insert_id 	= sqlInsert($sql,$value_arr);
		print_r($value_arr);
	    }
	    //echo "<br>";
	    $count++;
	    
	    if($count > 5)
	    {
		break;
	    }
	}
    }*/

    function importDataCheck($result,$column_map)//CHECK DATA IF ALREADY EXISTS
    {
	
	$result_col	= $this->getColumns($result);
	
	$mapped_tables	= $this->columnMapping($column_map,$result_col);
	
	foreach($result as $res)
	{
	    foreach($result_col as $col)
	    {
                ${$col}	= $res[$col];//GETTING IMPORTED VALUES
	    }
	   
	    foreach($mapped_tables as $table => $columns)
	    {
		$value_arr	= array();
		foreach($columns as $servercol => $column)
		{
		    if($column_map[$servercol]['colconfig']['insert_id'] == "1")
		    {
			$$servercol = $insert_id;
		    }
		    if($column_map[$servercol]['colconfig']['value_map'] == "1")
		    {
			$$servercol = $column_map[$servercol]['valconfig'][$$servercol];
		    }
		    $value_arr[$column] =  ${$servercol};                    
		}
	
		$fields	        = implode(",",$columns);
		$col_count	= count($columns);
		$field_vars	= "$".implode(",$",$columns);
		$params	        = rtrim(str_repeat("?,",$col_count),",");
		
		$primary_key_arr = $column_map['contraints'][$table]['primary_key'];
		if(count($primary_key_arr) > 0)
		{
		    $index      	= 0;
		    $condition  	= "";
		    $check_value_arr    = array();
		    
		    foreach($primary_key_arr as $pkey)
		    {
			if($index > 0)
			{
			    $condition.=" AND ";
			}
			$condition.=" ".$pkey." = ? ";
			$index++;
			$check_value_arr[$pkey] = $value_arr[$pkey];
		    }
		    
		    $update_arr = array();
		    foreach($value_arr as $key => $val)
		    {
			if(! in_array($key,$primary_key_arr))
			{                            
			    $update_arr[$key] = $val;
			}
		    }
		    
		    $update_combined_arr    = array_merge($update_arr,$check_value_arr);
		    
		    $index      	= 0;
		    $update_key_arr    	= array();
		    
		    foreach($update_arr as $upkey => $upval)
		    {
			$update_key_arr[]   = $upkey;
		    }
		    
		    $update_expr    = implode(" = ? ,",$update_key_arr);
		    $update_expr.=" = ? ";
		    
		    /*echo "<br>".*/$sql_check  = "SELECT COUNT(*) as data_exists FROM ".$table." WHERE ".$condition;
		    $pat_data_check         = sqlQuery($sql_check,$check_value_arr);
		    //print_r($check_value_arr);
		   
		    //echo "<br>";
		    //print_r($update_combined_arr);
		    if($pat_data_check['data_exists'])
		    {
			/*echo "<br>".*/$sqlup	= "UPDATE ".$table." SET ".$update_expr." WHERE ".$condition;
			$pat_data_check         = sqlQuery($sqlup,$update_combined_arr);			
		    }
		    else
		    {
			/*echo "<br>".*/$sql	= "INSERT INTO ".$table."(".$fields.") VALUES (".$params.")";
			/*echo "<br>".*/$insert_id 	= sqlInsert($sql,$value_arr);
                        //print_r($value_arr);
		    }
		}		
	    }
	    
	    $count++;
	    
	    //if($count > 5)
	    //{
	    //    break;
	    //}
	}
	sqlStatement("UPDATE procedure_type SET parent=procedure_type_id");
	sqlStatement("UPDATE procedure_type SET name=description");
    }

    //$constraint_arr
    
    public function getWebserviceOptions()
    {
	$options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
			    'uri'      => "urn://zhhealthcare/lab"
			    );
	return $options;
    }
    
    public function pullcompendiumTestConfig()
    {
	/*$column_map['test_id'] 		        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "procedure_type_id",
									    'value_map' => "0",
									    'insert_id' => "0"));*/
	
	$column_map['test_lab_id']	                = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "lab_id",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_lab_entity'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "seccol",
									    'value_map' => "0",
									    'insert_id' => "1"));
	$column_map['test_code'] 		        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "procedure_code",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_specimen_state'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "specimen_state",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_unit_code'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_status_indicator'] 		= array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "activity",
									    'value_map' => "1",
									    'insert_id' => "0"),
								'valconfig' => array(
									    'A'         => "1",
									    'I'         => "0"));
	
	$column_map['test_insert_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_description'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "description",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_specimen_type'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_service_code'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_lab_site'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_update_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_update_user'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_code_suffix'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "suffix",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_profile'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_select'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_performing_site'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_flag'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_not_billed'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_billed_only'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_reflex_count'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_conforming_indicator']    	= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_alternate_temp'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_pap_indicator'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "pap_indicator",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_last_updatetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	
	$column_map['contraints']   = array('procedure_type' => array(
									'primary_key' => array(
												'0'     => "lab_id",
												'1'     => "procedure_code",
												'2'	=> "suffix"))); 
	
	return $column_map;
    }
    
    //    public function listLabLocation($inputString)
    //    {
    //	$sql = "SELECT * FROM labs WHERE lab_name=?,array($inputString)";
    //	$result = sqlStatement($sql);
    //	$i = 0;
    //	
    //	while($row=sqlFetchArray($res)) {
    //		$rows[$i] = array (
    //			'value' => $row['ppid'],
    //			'label' => $row['name'],
    //		);
    //		$i++;
    //	}
    //	return $rows;
    //
    //    }
    
    

    public function pullcompendiumAoeConfig()
    {
	/*$column_map['aoe_id'] 		        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "procedure_type_id",
									    'value_map' => "0",
									    'insert_id' => "0"));*/
	
	$column_map['aoe_lab_id']	                = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "lab_id",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_lab_entity'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_performing_site'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_unit_code'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_code'] 		        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "procedure_code",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_analyte_code'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_code",
									    'value_map' => "0",
									    'insert_id' => "0"));            
	
	$column_map['aoe_question'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['aoe_status_indicator'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "activity",
									    'value_map' => "1",
									    'insert_id' => "0"),
                                                                'valconfig' => array(
									    'A'         => "1",
									    'I'         => "0"));
        
	$column_map['aoe_profile_component'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_insert_datetime'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_question_description'] 	= array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_text",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_suffix'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_result_filter'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "tips",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_code_mneumonic'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_flag'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	$column_map['aoe_upadate_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_update_user'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_component_name'] 		= array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_component",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_last_updatetime'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	
	$column_map['contraints']   = array('procedure_questions' => array(
									'primary_key' => array(
												'0'     => "lab_id",
												'1'     => "procedure_code",
												'2'     => "question_code"))); 
	
	return $column_map;
    }
    
    
    public function mapcolumntoxml()
    {
	$xmlconfig['patient_data']          = array(                      
						'column_map'    => array(
									'pid'           => 'pid',
									'fname'         => 'patient_fname',
									'DOB'           => 'patient_dob',
									'sex'           => 'patient_sex',
									'lname'         => 'patient_lname',
									'street'        => 'patient_street_address',
									'city'          => 'patient_city',
									'state'         => 'patient_state',
									'postal_code'   => 'patient_postal_code',
									'country_code'  => 'patient_country',
									'phone_contact' => 'patient_phone_no',
									'ss'            => 'patient_ss_no' 
									 ),
						'primary_key'   => array('pid'),
						'match_value'   => array('pid'));
	
	
	$xmlconfig['insurance_data']        = array(
						'column_map'    => array(
									'type'                      => '#type',
									'provider'                  => '#provider',
									'subscriber_street'         => '$type_insurance_address',
									'subscriber_city'           => '$type_insurance_city',
									'subscriber_state'          => '$type_insurance_state',
									'subscriber_postal_code'    => '$type_insurance_postal_code',
									'subscriber_lname'          => '$type_insurance_person_lname',
									'subscriber_fname'          => '$type_insurance_person_fname',
									'subscriber_relationship'   => '$type_insurance_person_relationship',
									'policy_number'             => '$type_insurance_policy_no',
														       
									'group_number'              => '$type_insurance_group_no',
									'subscriber_mname'          => '$type_insurance_person_mname',
									
									),
						'primary_key'   => array('pid'),
						'match_value'   => array('pid'),
						'child_table'   => 'insurance_companies');
	
	$xmlconfig['insurance_companies']   = array(
						'column_map'    => array(
									'name'  => '$type_insurance_name'                                                
									),
						'primary_key'   => array('id'),
						'match_value'   => array('provider'),
						'parent_table'  => 'insurance_data'
						);
       
        $xmlconfig['procedure_providers']   = array(                      
						'column_map'    => array(
									'send_app_id'       => 'send_app_id',
                                                                        'recv_app_id'       => 'recv_app_id',    
									'send_fac_id'       => 'send_fac_id',
									'recv_fac_id'       => 'recv_fac_id',
									'DorP'              => 'DorP'
									 ),
						'primary_key'   => array('ppid'),
						'match_value'   => array('lab_id'));
        
        $xmlconfig['procedure_order']       = array(                      
						'column_map'    => array(
                                                                         'psc_hold'         	=> 'recv_app_id',
									 'billto'	    	=> 'bill_to',
									 'patient_instructions'	=> 'patient_internal_comments',
									 'internal_comments'	=> 'observation_request_comments',
									 ),
                                                'value_map'     => array(
                                                                        'psc_hold'          => array(
                                                                                                        'onsite'    => '',
                                                                                                        'labsite'   => 'PSC'
                                                                                                    )
                                                                         ),
						'primary_key'   => array('procedure_order_id'),
						'match_value'   => array('order_id'));
               
	return $xmlconfig;
    }

		    
    public function mapResultXmlToColumn()
    {			
	$xmlconfig['procedure_report'] 	    = array(                      
						'xmltag_map'    => array(
									'$procedure_order_id'   => 'procedure_order_id',
									'$procedure_order_seq'	=> 'procedure_order_seq',
									'date_report'           => 'patient_dob',
									'date_collected'       	=> 'patient_sex',
									'specimen_num'        	=> 'patient_lname',
									'report_status'        	=> 'report_status',
									'$review_status'      	=> 'review_status'
									 ),
						'primary_key'   => array('procedure_report_id'),
						'match_value'   => array('procedure_report_id'),
						'child_table'   => 'procedure_result');
	
	
	$xmlconfig['procedure_result']	    = array(
						'xmltag_map'    => array(
									'$procedure_report_id'  => 'procedure_report_id',
									'abnormal'              => 'abnormal',
									'result'         	=> 'result',
									'range'           	=> 'range',
									'units'        		=> 'units',
									'facility'    		=> 'facility',
									'comments'          	=> 'comments',
									'$result_status'         => 'result_status'							
									),
						'primary_key'   => array('procedure_result_id'),
						'match_value'   => array('procedure_result_id'),
						'parent_table'   => 'procedure_report');
               
	return $xmlconfig;
    }


    /*public function generateOrderXml($patient_id,$xmlfile)
    {
	$sql1   = "SELECT pid, fname, DOB, sex, lname, street, city, state, postal_code, country_code, phone_contact, ss FROM patient_data WHERE pid = ?";
	
	$sql2   = "SELECT type,  provider,subscriber_street,subscriber_city,subscriber_state,subscriber_postal_code,group_number,subscriber_lname,
		    subscriber_mname,subscriber_fname,subscriber_relationship,policy_number FROM insurance_data WHERE pid = ?";
	
	$sql3   = "SELECT name FROM insurance_companies WHERE id = ? ";//id = '$provider'
	
	$pat_data   = sqlQuery($sql1,array($patient_id));
	
	$pid                        = $pat_data['pid'];
	$patient_fname              = $pat_data['fname'];
	$patient_dob                = $pat_data['DOB'];
	$patient_sex                = $pat_data['sex'];
	$patient_lname              = $pat_data['lname'];
	$patient_street_address     = $pat_data['street'];
	$patient_city               = $pat_data['city'];
	$patient_state              = $pat_data['state'];
	$patient_postal_code        = $pat_data['postal_code'];
	$patient_country            = $pat_data['country_code'];
	$patient_phone_no           = $pat_data['phone_contact'];
	$patient_ss_no              = $pat_data['ss'];
	
	$patient_internal_comments  = "";
	
	$ins_res    = sqlStatement($sql2,array($patient_id));
	
	while($ins_data = sqlFetchArray($ins_res))
	{
	    if($ins_data['type'] == "primary")
	    {
		$provider   = $ins_data['provider'];
		$comp_data  = sqlQuery($sql3,array($provider));
		
		$primary_insurance_name                     = $comp_data['name'];
		
		$primary_insurance_address                  = $ins_data['subscriber_street'];
		$primary_insurance_city                     = $ins_data['subscriber_city'];
		$primary_insurance_state                    = $ins_data['subscriber_state'];
		$primary_insurance_postal_code              = $ins_data['subscriber_postal_code'];
		$primary_insurance_person_lname             = $ins_data['subscriber_lname'];
		$primary_insurance_person_fname             = $ins_data['subscriber_fname'];
		$primary_insurance_person_relationship      = $ins_data['subscriber_relationship'];
		$primary_insurance_policy_no                = $ins_data['policy_number'];
		
		$primary_insurance_coverage_type            = "";
	    }
	    if($ins_data['type'] == "secondary")
	    {
		$provider   = $ins_data['provider'];
		$comp_data  = sqlQuery($sql3,array($provider));
		
		$secondary_insurance_name                   = $comp_data['name'];
		
		$secondary_insurance_address                = $ins_data['subscriber_street'];
		$secondary_insurance_city                   = $ins_data['subscriber_city'];
		$secondary_insurance_state                  = $ins_data['subscriber_state'];
		$secondary_insurance_postal_code            = $ins_data['subscriber_postal_code'];
		$secondary_insurance_group_no               = $ins_data['group_number'];
		$secondary_insurance_person_lname           = $ins_data['subscriber_lname'];
		$secondary_insurance_person_fname           = $ins_data['subscriber_fname'];
		$secondary_insurance_person_mname           = $ins_data['subscriber_mname'];
		$secondary_insurance_person_relationship    = $ins_data['subscriber_relationship'];
		$secondary_insurance_policy_no              = $ins_data['policy_number'];
		
		$secondary_insurance_coverage_type        = "";       
	    }
	}
	
	$primary_insurance_person_address       = "";    
	$primary_insurance_person_city          = "";    
	$primary_insurance_person_state         = "";    
	$primary_insurance_person_postal_code   = "";    
	
	$guarantor_lname                        = "";    
	$guarantor_fname                        = "";    
	$guarantor_address                      = "";    
	$guarantor_city                         = "";    
	$guarantor_state                        = "";    
	$guarantor_postal_code                  = "";    
	$guarantor_phone_no                     = "";    
	$guarantor_mname                        = "";    
	$ordering_provider_id                   = "";    
	$ordering_provider_lname                = "";    
	$ordering_provider_fname                = "";    
	$observation_request_comments           = "";
	
	$xmltag_arr = array("pid","patient_fname","patient_dob","patient_sex","patient_lname","patient_street_address","patient_city",
			    "patient_state","patient_postal_code","patient_country","patient_phone_no","patient_ss_no","patient_internal_comments",
			    "primary_insurance_name","primary_insurance_address","primary_insurance_city","primary_insurance_state",
			    "primary_insurance_postal_code","primary_insurance_person_lname","primary_insurance_person_fname",
			    "primary_insurance_person_relationship","primary_insurance_policy_no","primary_insurance_coverage_type",
			    "secondary_insurance_name","secondary_insurance_address","secondary_insurance_city","secondary_insurance_state",
			    "secondary_insurance_postal_code","secondary_insurance_group_no","secondary_insurance_person_lname",
			    "secondary_insurance_person_fname","secondary_insurance_person_relationship","secondary_insurance_policy_no",
			    "secondary_insurance_coverage_type", "primary_insurance_person_address","primary_insurance_person_city",
			    "primary_insurance_person_state","primary_insurance_person_postal_code","guarantor_lname","guarantor_fname",
			    "guarantor_address","guarantor_city","guarantor_state","guarantor_postal_code","guarantor_phone_no",
			    "secondary_insurance_person_mname","guarantor_mname","ordering_provider_id","ordering_provider_lname",
			    "ordering_provider_fname","observation_request_comments");
	
	$xmlfile = ($xmlfile <> "") ? $xmlfile : "order_".gmdate('YmdHis').".xml";
	
	$config = new Config\Config(array(), true);
	$config->Order = array();
	
	foreach($xmltag_arr as $tag)
	{
	    $tag_val = (${$tag} <> "") ? ${$tag} : "";
	    $config->Order->$tag = $tag_val;            
	}
	
	$writer = new Config\Writer\Xml();
	//echo $writer->toString($config);
	$writer->toFile("module/Lab/".$xmlfile,$config);
	
	return $xmlfile;
    }*/
    
    
    public function generateSQLSelect($pid,$lab_id,$order_id,$cofig_arr,$table)
    {
	//echo "hi".$pid." ,".$table;
	
       // $cofig_arr11  = $this->mapcolumntoxml();
       //print_r($cofig_arr);
	
	//echo "<br>...";
	//continue;
	//print_r($cofig_arr[$table]);
	
	global $type;
	global $provider;
     
	$table_name         = $table;
	    
	$col_map_arr        = $cofig_arr[$table]['column_map'];
       
	$primary_key_arr    = $cofig_arr[$table]['primary_key'];        
	$match_value_arr    = $cofig_arr[$table]['match_value'];
	
	$index  = 0;
	$condition  = "";
	foreach($primary_key_arr as $pkey)
	{
	    if($index > 0)
	    {
		$condition.=" AND ";
	    }
	    $condition.=" ".$pkey." = ? ";
	    $index++;
	}
	
	$index  = 0;
	foreach($match_value_arr as $param)
	{
	    $match_value_arr[$index] = ${$match_value_arr[$index]};
	    $index++;
	}
	
	$col_arr        = array();
	foreach($col_map_arr as $col => $tag)
	{
	    $col_arr[]  = $col;
	}
	$cols   = implode(",",$col_arr);
	
	/*echo "<br>".*/$sql    = "SELECT ".$cols." FROM ".$table_name." WHERE ".$condition;
	//echo "<br>Param :";
	//print_r($match_value_arr);
	$res   = sqlStatement($sql,$match_value_arr);
	
	return $res;    
    }
    
    public function generateOrderXml($pid,$lab_id,$xmlfile)
    {
	//echo $pid;
	global $type;
	global $provider;
	//exit;
	//XML TAGS NOT CONFIGURED YET
	$primary_insurance_coverage_type        = "";
	$secondary_insurance_coverage_type      = "";       
	$primary_insurance_person_address       = "";    
	$primary_insurance_person_city          = "";    
	$primary_insurance_person_state         = "";    
	$primary_insurance_person_postal_code   = "";    
	
	$guarantor_lname                        = "TEST";    
	$guarantor_fname                        = "TC2";    
	$guarantor_address                      = "2090 Concourse";    
	$guarantor_city                         = "St. Louis";    
	$guarantor_state                        = "MT";    
	$guarantor_postal_code                  = "63146";    
	$guarantor_phone_no                     = "314-872-3000";    
	$guarantor_mname                        = "";    
	$ordering_provider_id                   = "1122334455";    //hard coded//
	$ordering_provider_lname                = "Allan";    //hard coded//
	$ordering_provider_fname                = "Joseph";    //hard coded//
	$observation_request_comments           = "";
	
	$xmltag_arr = array("pid","patient_fname","patient_dob","patient_sex","patient_lname","patient_street_address","patient_city",
			    "patient_state","patient_postal_code","patient_country","patient_phone_no","patient_ss_no","patient_internal_comments",
			    "primary_insurance_name","primary_insurance_address","primary_insurance_city","primary_insurance_state",
			    "primary_insurance_postal_code","primary_insurance_person_lname","primary_insurance_person_fname",
			    "primary_insurance_person_relationship","primary_insurance_policy_no","primary_insurance_coverage_type",
			    "secondary_insurance_name","secondary_insurance_address","secondary_insurance_city","secondary_insurance_state",
			    "secondary_insurance_postal_code","secondary_insurance_group_no","secondary_insurance_person_lname",
			    "secondary_insurance_person_fname","secondary_insurance_person_relationship","secondary_insurance_policy_no",
			    "secondary_insurance_coverage_type", "primary_insurance_person_address","primary_insurance_person_city",
			    "primary_insurance_person_state","primary_insurance_person_postal_code","guarantor_lname","guarantor_fname",
			    "guarantor_address","guarantor_city","guarantor_state","guarantor_postal_code","guarantor_phone_no",
			    "secondary_insurance_person_mname","guarantor_mname","ordering_provider_id","ordering_provider_lname",
			    "ordering_provider_fname","observation_request_comments","send_app_id","recv_app_id","send_fac_id","recv_fac_id","DorP","bill_to");
     
	
	$cofig_arr  = $this->mapcolumntoxml();
       
	
	//GETTING VALUES OF ADDITIONAL XML TAGS
	$sql_order   	= "SELECT procedure_order_id FROM procedure_order WHERE patient_id = ? AND order_status = ? AND lab_id = ? ";
	    
	$misc_value_arr = array();
	
	$order_value_arr['patient_id']   = $pid;
	$order_value_arr['order_status'] = "pending";//hard coded//
	$order_value_arr['lab_id']       = $lab_id;
	
	$res_order   = sqlStatement($sql_order,$order_value_arr);
	
	$test_count = 0;
	$diag_count = 0;       
	
	$return_arr = array();
	$i=0;
	
	
	
	while($data_order = sqlFetchArray($res_order))
	{
	    $diagnosis  = "";
	    $test_id    = "";           
            $test_aoe   = "";
	    
	    $result_xml = "";
	    
            /* ------------------------------------GENERATING XML FROM CONFIGURATION FOR EACH ORDER----------------------------------------*/
            $sl = 0;
            foreach($cofig_arr as $table => $config)
            {
              
                //SKIP THE DATA FETCHING OF CHILD TABLE ROWS , WHICH WILL AUTOMATICALLY FETCH IF IT IS CONFIGURED, IT HAS PARENT TABLE
                if($config['parent_table'] <> "")
                {
                    continue;
                }            
                $col_map_arr    = $cofig_arr[$table]['column_map'];
		//echo "<br>".$table;
                $res            = $this->generateSQLSelect($pid,$lab_id,$data_order['procedure_order_id'],$cofig_arr,$table);
                
		
		//print_r
		
                while($data = sqlFetchArray($res))
                {
                    $global_arr  = array();                
                    $check_arr   = array();
//                    echo "<br>";
//		    print_r($col_map_arr);
		    
                    foreach($col_map_arr as $col => $tagstr)
                    {   //CHECKING FOR MAULTIPLE TAG MAPPING
                        $tag_arr   = explode(",",$tagstr);
                        
                        foreach($tag_arr as $tag)
                        {
                            if(trim($tag) <> "")
                            { 
				if(substr($tag,0,1)== "#")
				{
					$tag            = substr($tag,1,strlen($tag));
					$check_arr[]    = "$".$tag;
				}
				foreach($check_arr as $check)
				{
				    if(strstr($tag,$check))
				    {
					    $tag = str_replace($check,${ltrim($check,"$")},$tag);                               
				    }
				}
				if($cofig_arr[$table]['value_map'][$col] <> "")
				{
				    $$tag   = $cofig_arr[$table]['value_map'][$col][$data[$col]];
				}
				else
				{
				    $$tag   = $data[$col];
				}
			    }
			}
			
			//echo "<br>".$col." => $".$tag." = ".$$tag;
			//echo "<br>$"."data[".$col."] = ".$data[$col];
                    }
		    
		    
		    
    
                    if($config['child_table'] <> "")
                    {
                        $res2    = $this->generateSQLSelect($pid,$lab_id,$data_order['procedure_order_id'],$cofig_arr,$config['child_table']);
                        $fetch2_count    = 0; 
                        while($data1 = sqlFetchArray($res2))
                        {
                            $col_map_arr2        = $cofig_arr[$config['child_table']]['column_map'];
                            
                            foreach($col_map_arr2 as $col => $tagstr)
                            {   
                                //CHECKING FOR MAULTIPLE TAG MAPPING
                                $tag_arr   = explode(",",$tagstr);
                                
                                foreach($tag_arr as $tag)
                                {
                                    if(trim($tag) <> "")
                                    {
                                        foreach($check_arr as $check)
                                        {
                                                if(strstr($tag,$check))
                                                {
                                                        $tag = str_replace($check,${ltrim($check,"$")},$tag);                                       
                                                }
                                        }
                                        
                                        if(substr($tag,0,1)== "#")
                                        {
                                                $tag = substr($tag,1,strlen($tag));                        
                                        }
                                        if($cofig_arr[$table]['value_map'][$col] <> "")
                                        {
                                            $$tag   = $cofig_arr[$table]['value_map'][$col][$data1[$col]];
                                        }
                                        else
                                        {
                                            $$tag   = $data1[$col];
                                        }
                                    }
                                }
                            }
                        }                    
                    }
                }
            }
	    //exit;
            /*-----------------------------------------------------------------------------------------------------------------------------*/
            
	    $xmlfile = ($xmlfile <> "") ? $xmlfile : "order_new_".gmdate('YmdHis')."_".$data_order['procedure_order_id'].".xml";
	    $result_xml	= '<?xml version="1.0" encoding="UTF-8"?><Order>';
	    
	    foreach($xmltag_arr as $tag)
	    {
		$tag_val = (trim(${$tag}) <> "") ? ${$tag} : "";
		$config->Order->$tag    = $tag_val;
		$result_xml.= '<'.$tag.'>'.$tag_val.'</'.$tag.'>';
		
	    }
	    
	    
	    
	    /* ------------------ GETTING TEST DETAILS ------------------------*/
	    $sql_test   = "SELECT procedure_code, procedure_suffix, procedure_order_seq FROM procedure_order_code WHERE procedure_order_id = ? ";
	    
	    $test_value_arr 	= array();	    
	    $test_value_arr['procedure_order_id']   = $data_order['procedure_order_id'];
	   
	    $res_test  		= sqlStatement($sql_test,$test_value_arr);
	    while($data_test = sqlFetchArray($res_test))
            {
		if(($data_test['procedure_code'] <> "") && ($data_test['procedure_suffix'] <> ""))
		{
		    $test_id   .= $data_test['procedure_code']."#!#".$data_test['procedure_suffix']."#--#";
		}
		
		/*------------------- GETTING DIAGNOSES DETAILS -------------------*/
		if($data_order['diagnoses'] <> "")
		{
		    $diag_arr    =  explode(";",$data_order['diagnoses']);
		    
		    foreach($diag_arr as $diag)
		    {
			if(strpos($diag,":"))
			{
			    $diag_array     =  explode(":",$diag,2);
			    $diag_str	= $diag_array[1];
			}
			else
			{
			    $diag_str	= $diag;
			}
			
			$diagnosis     .= $diag_str;
			$diagnosis     .= "#@#";
		    }
		    $diagnosis  = rtrim($diagnosis,"#@#");
		    $diag_count++;
		    
		}
		$diagnosis.="#~@~#";
	    
		/*------------------- GETTING AOE DETAILS -----------------*/
		$sql_aoe        = "SELECT question_code,answer_seq,answer FROM procedure_answers
				    WHERE procedure_order_id = ? AND procedure_order_seq = ? ";
		/*$sql_aoe        = "SELECT question_code,answer_seq,answer FROM procedure_answers WHERE procedure_order_id = ?  ";*/
		$aoe_value_arr  = array();
	    
		$aoe_value_arr['procedure_order_id']    = $data_order['procedure_order_id'];
		$aoe_value_arr['procedure_order_seq']   = $data_test['procedure_order_seq'];
			   
		$res_aoe        = sqlStatement($sql_aoe,$aoe_value_arr);
		$aoe_count	= 0; 
		while($data_aoe = sqlFetchArray($res_aoe))
		{
		    //if(($data_aoe['question_code'] <> "")&&($data_aoe['answer'] <> ""))
		    if($data_aoe['question_code'] <> "")
		    {
			$aoe_count++;
			if($aoe_count > 1)
			{
			    $test_aoe   .= "!#@#!";	
			}
			$test_aoe   .= $data_aoe['question_code']."!@!".$data_aoe['answer'];
					  
		    }
		    
		}
		//if($aoe_count == 0)
		//{
		//    $test_aoe   .= "!#@#!";
		//}
		$test_aoe   .= "!-#@#-!";
            }	 
	    
	    
	    
	    /*--------------------------------------------------------------*/
	    
	    $result_xml.= '<test_id>'.$test_id.'</test_id>';
	    $result_xml.= '<test_diagnosis>'.$diagnosis.'</test_diagnosis>';
	    $result_xml.= '<test_aoe>'.$test_aoe.'</test_aoe>';
	    
	    $result_xml.= '</Order>';
	    
	    $return_arr[]   = array (
				     'order_id'     => $data_order['procedure_order_id'],
				     'xmlstring'    => $result_xml
				    );
	}
	return $return_arr;        
    }
    
    public function getClientCredentials($proc_order_id)
    {
	$sql_proc   = "SELECT lab_id FROM procedure_order WHERE procedure_order_id = ? ";
	$proc_value_arr = array();
	$proc_value_arr['procedure_order_id']   = $proc_order_id;
	$res_proc   = sqlQuery($sql_proc,$proc_value_arr);
	$sql_cred   = "SELECT  login, password FROM procedure_providers WHERE ppid = ? ";
	$res_cred   = sqlQuery($sql_cred,$res_proc);
	return $res_cred;        
    }
    
    public function changeOrderRequisitionStatus($proc_order_id,$status,$file_name)
    {
	$sql_status         = "UPDATE procedure_order SET order_status = ?, requisition_file_url = ? WHERE procedure_order_id = ? ";
        
	$status_value_arr   = array();
        
        $status_value_arr['status']               = $status;
        $status_value_arr['requisition_file_url'] = $file_name;
	$status_value_arr['procedure_order_id']   = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status;        
    }
    
    public function changeOrderResultStatus($proc_order_id,$status,$file_name)
    {
	$sql_status         = "UPDATE procedure_order SET order_status = ?, result_file_url = ? WHERE procedure_order_id = ? ";
        
	$status_value_arr   = array();
        
        $status_value_arr['status']             = $status;
        $status_value_arr['result_file_url']    = $file_name;
	$status_value_arr['procedure_order_id'] = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status;        
    }
    
    
    public function getOrderStatus($proc_order_id)
    {
	$sql_status         = "SELECT order_status FROM procedure_order WHERE procedure_order_id = ? ";
        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['order_status'];        
    }
    
    public function getOrderRequisitionFile($proc_order_id)
    {
	$sql_status         = "SELECT requisition_file_url FROM procedure_order WHERE procedure_order_id = ? ";
        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['requisition_file_url'];        
    }
    
    public function getOrderResultFile($proc_order_id)
    {
	$sql_status         = "SELECT result_file_url FROM procedure_order WHERE procedure_order_id = ? ";
        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['result_file_url'];        
    }
    
    
    public function importResultDetails($result_config_arr,$result)
    {
	//print_r($result_config_arr);
	//echo "<br>";
	//print_r($result);
	
	
    }
}
?>

