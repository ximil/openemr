<?php
namespace Lab\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

class LabTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
	
	public function listLabResult($data)
	{	
		$flagSearch = 0;
		if (isset($data['status']) && $data['status'] != '--Select--') { 
			$stats = $data['status'];
			$flagSearch = 1;
		}
		if (isset($data['dtFrom'])) { 
			$dtFrom = $data['dtFrom'];
			$flagSearch = 1;
		}
		if (isset($data['dtTo'])) { 
			$dtTo = $data['dtTo'];
			$flagSearch = 1;
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
		$pid = 1;
		$selects =
			"po.procedure_order_id, po.date_ordered, pc.procedure_order_seq, " .
			"pt1.procedure_type_id AS order_type_id, pc.procedure_name, " .
			"pr.procedure_report_id, pr.date_report, pr.date_collected, pr.specimen_num, " .
			"pr.report_status, pr.review_status";

		$joins =
			"JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
			"LEFT JOIN procedure_type AS pt1 ON pt1.lab_id = po.lab_id AND pt1.procedure_code = pc.procedure_code " .
			"LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
			"pr.procedure_order_seq = pc.procedure_order_seq";
		$groupby = '';
		if ($flagSearch == 1) {
			$groupby = "GROUP By po.procedure_order_id";
		}
		
		$orderby =
			"po.date_ordered, po.procedure_order_id, " .
			"pc.procedure_order_seq, pr.procedure_report_id";

		$where = "1 = 1";
		if($stats) {
			$where .= " AND pr.report_status='$stats'";
		}
		if ($dtFrom) {
			$where .= " AND DATE(po.date_ordered) BETWEEN '$dtFrom' AND '$dtTo'";
		}

		$sql = "SELECT DISTINCT $selects " .
					  "FROM procedure_order AS po " .
					  "$joins " .
					  "WHERE po.patient_id = '$pid' AND $where " .
					  "$groupby ORDER BY $orderby";

      	$result = sqlStatement($sql);
		$arr1 = array();
		$i = 0;
		while($row = sqlFetchArray($result)) {
			$order_type_id  = empty($row['order_type_id'      ]) ? 0 : ($row['order_type_id' ] + 0);
			$order_id       = empty($row['procedure_order_id' ]) ? 0 : ($row['procedure_order_id' ] + 0);
			$order_seq      = empty($row['procedure_order_seq']) ? 0 : ($row['procedure_order_seq'] + 0);
			$report_id      = empty($row['procedure_report_id']) ? 0 : ($row['procedure_report_id'] + 0);
			$date_report    = empty($row['date_report'     ]) ? '' : $row['date_report'];
			$date_collected = empty($row['date_collected'  ]) ? '' : substr($row['date_collected'], 0, 16);
			$specimen_num   = empty($row['specimen_num'    ]) ? '' : $row['specimen_num'];
			$report_status  = empty($row['report_status'   ]) ? '' : $row['report_status']; 
			$review_status  = empty($row['review_status'   ]) ? 'received' : $row['review_status'];
		
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
			$pt2cond = "pt2.parent = $order_type_id AND " .
				"(pt2.procedure_type LIKE 'res%' OR pt2.procedure_type LIKE 'rec%')";
			$pscond = "ps.procedure_report_id = $report_id";

			$joincond = "ps.result_code = pt2.procedure_code";

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
				
				if ($arr1[$i - 1]['date_ordered'] != $row['date_ordered']) {
					$arr1[$i]['date_ordered'] = $row['date_ordered'];
				}
				if ($arr1[$i - 1]['procedure_name'] != $row['procedure_name']) {
					$arr1[$i]['procedure_name'] = $row['procedure_name'];
					$arr1[$i]['date_report'] = $date_report ? $date_report: '';
					$arr1[$i]['date_collected'] = $date_collected ? $date_collected: '';
					$arr1[$i]['specimen_num'] = $specimen_num ? $specimen_num: '';
					$arr1[$i]['report_status'] = $report_status ? $report_status: '';
				}
				$arr1[$i]['order_type_id'] = $order_type_id ? $order_type_id: '';
				$arr1[$i]['procedure_order_id'] = $order_id ? $order_id: '';
				$arr1[$i]['procedure_order_seq'] = $order_seq ? $order_seq: '';
				$arr1[$i]['procedure_report_id'] = $report_id ? $report_id: '';
				$arr1[$i]['review_status'] = $review_status ? $review_status: '';
				$arr1[$i]['procedure_code'] = $restyp_code;
				$arr1[$i]['procedure_type'] = $restyp_type;
				$arr1[$i]['name'] = $restyp_name;
				$arr1[$i]['pt2_units'] = $restyp_units;
				$arr1[$i]['pt2_range'] = $restyp_range;
				$arr1[$i]['procedure_result_id'] = $result_id;
				$arr1[$i]['result_code'] = $result_code;
				$arr1[$i]['result_text'] = $result_text;
				$arr1[$i]['abnormal'] = $result_abnormal;
				$arr1[$i]['result'] = $result_result;
				$arr1[$i]['units'] = $result_units;
				$arr1[$i]['facility'] = $facility;
				$arr1[$i]['comments'] = $result_comments;
				$arr1[$i]['range'] = $result_range;
				$arr1[$i]['result_status'] = $result_status;
				$i++;
			}
			
		}
		//$arr = array_merge($arr1, $arr2);
		//$arr = array_merge_recursive($arr1, $arr2);
		//print_r($arr1);//print_r($arr2);
		return $arr1;
	}
	
	public function listSearchLabResult($data)
	{	
		$stats = $data['status'];
		
		$form_review = 1; // review mode
		$lastpoid = -1;
		$lastpcid = -1;
		$lastprid = -1;
		$encount = 0;
		$lino = 0;
		$extra_html = '';
		$lastrcn = '';
		$facilities = array();
		$pid = 1;
		$selects =
			"po.procedure_order_id, po.date_ordered, pc.procedure_order_seq, " .
			"pt1.procedure_type_id AS order_type_id, pc.procedure_name, " .
			"pr.procedure_report_id, pr.date_report, pr.date_collected, pr.specimen_num, " .
			"pr.report_status, pr.review_status";

		$joins =
			"JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
			"LEFT JOIN procedure_type AS pt1 ON pt1.lab_id = po.lab_id AND pt1.procedure_code = pc.procedure_code " .
			"LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
			"pr.procedure_order_seq = pc.procedure_order_seq";

		$orderby =
			"po.date_ordered, po.procedure_order_id, " .
			"pc.procedure_order_seq, pr.procedure_report_id";
		if($stats)
		$where = "1 = 1 AND pr.report_status='$stats'";
		else
		$where = "1 = 1";
		$sql = "SELECT $selects " .
					  "FROM procedure_order AS po " .
					  "$joins " .
					  "WHERE po.patient_id = '$pid' AND $where " .
					  "ORDER BY $orderby";

      	$result = sqlStatement($sql);
		$arr1 = array();
		$i = 0;
		while($row = sqlFetchArray($result)) {
			$order_type_id  = empty($row['order_type_id'      ]) ? 0 : ($row['order_type_id' ] + 0);
			$order_id       = empty($row['procedure_order_id' ]) ? 0 : ($row['procedure_order_id' ] + 0);
			$order_seq      = empty($row['procedure_order_seq']) ? 0 : ($row['procedure_order_seq'] + 0);
			$report_id      = empty($row['procedure_report_id']) ? 0 : ($row['procedure_report_id'] + 0);
			$date_report    = empty($row['date_report'     ]) ? '' : $row['date_report'];
			$date_collected = empty($row['date_collected'  ]) ? '' : substr($row['date_collected'], 0, 16);
			$specimen_num   = empty($row['specimen_num'    ]) ? '' : $row['specimen_num'];
			$report_status  = empty($row['report_status'   ]) ? '' : $row['report_status']; 
			$review_status  = empty($row['review_status'   ]) ? 'received' : $row['review_status'];
		//echo 'here1 <br>';
			if ($form_review) {
				//if ($review_status == "reviewed") continue;
			} else {
				//if ($review_status == "received") continue;
			}
		//echo 'here2 <br>';	
			$selects = "pt2.procedure_type, pt2.procedure_code, pt2.units AS pt2_units, " .
				"pt2.range AS pt2_range, pt2.procedure_type_id AS procedure_type_id, " .
				"pt2.name AS name, pt2.description, pt2.seq AS seq, " .
				"ps.procedure_result_id, ps.result_code AS result_code, ps.result_text, ps.abnormal, ps.result, " .
				"ps.range, ps.result_status, ps.facility, ps.comments, ps.units, ps.comments";
			$pt2cond = "pt2.parent = $order_type_id AND " .
				"(pt2.procedure_type LIKE 'res%' OR pt2.procedure_type LIKE 'rec%')";
			$pscond = "ps.procedure_report_id = $report_id";

			$joincond = "ps.result_code = pt2.procedure_code";

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
				
				if ($arr1[$i - 1]['date_ordered'] != $row['date_ordered']) {
					$arr1[$i]['date_ordered'] = $row['date_ordered'];
				}
				if ($arr1[$i - 1]['procedure_name'] != $row['procedure_name']) {
					$arr1[$i]['procedure_name'] = $row['procedure_name'];
					$arr1[$i]['date_report'] = $date_report ? $date_report: '';
					$arr1[$i]['date_collected'] = $date_collected ? $date_collected: '';
					$arr1[$i]['specimen_num'] = $specimen_num ? $specimen_num: '';
					$arr1[$i]['report_status'] = $report_status ? $report_status: '';
				}
				$arr1[$i]['order_type_id'] = $order_type_id ? $order_type_id: '';
				$arr1[$i]['procedure_order_id'] = $order_id ? $order_id: '';
				$arr1[$i]['procedure_order_seq'] = $order_seq ? $order_seq: '';
				$arr1[$i]['procedure_report_id'] = $report_id ? $report_id: '';
				$arr1[$i]['review_status'] = $review_status ? $review_status: '';
				$arr1[$i]['procedure_code'] = $restyp_code;
				$arr1[$i]['procedure_type'] = $restyp_type;
				$arr1[$i]['name'] = $restyp_name;
				$arr1[$i]['pt2_units'] = $restyp_units;
				$arr1[$i]['pt2_range'] = $restyp_range;
				$arr1[$i]['procedure_result_id'] = $result_id;
				$arr1[$i]['result_code'] = $result_code;
				$arr1[$i]['result_text'] = $result_text;
				$arr1[$i]['abnormal'] = $result_abnormal;
				$arr1[$i]['result'] = $result_result;
				$arr1[$i]['units'] = $result_units;
				$arr1[$i]['facility'] = $facility;
				$arr1[$i]['comments'] = $result_comments;
				$arr1[$i]['range'] = $result_range;
				$arr1[$i]['result_status'] = $result_status;
				$i++;
			}
			
		}
		//$arr = array_merge($arr1, $arr2);
		//$arr = array_merge_recursive($arr1, $arr2);
		//print_r($arr1);//print_r($arr2);
		return $arr1;
	}
	
	public function saveResult($data)
	{	//$result_id, $report_id, $specimen_num
		//$sql = "SELECT procedure_report_id FROM procedure_report WHERE procedure_report_id='$report_id'";
		//$result = sqlStatement($query);
		$report_id		= $data['procedure_report_id'];
		$order_id 		= $data['procedure_order_id'];
		$result_id 		= $data['procedure_result_id'];
		$specimen_num 	= $data['specimen_num'];
		$report_status 	= $data['report_status'];
		$order_seq 		= $data['procedure_order_seq'];
		$date_report 	= $data['date_report'];
		$date_collected = $data['date_collected'];
		
		$result_code			= $data['$result_code'];
		$procedure_report_id	= $data['procedure_report_id'];
		$result_text			= $data['result_text'];
		$abnormal				= $data['abnormal'];
		$result					= $data['result'];
		$range					= $data['range'];
		$units					= $data['units'];
		$result_status			= $data['result_status'];
		 
		/* $sets =
        "procedure_order_id = '$order_id', " .
        "procedure_order_seq = '$order_seq', " .
        "date_report = '$date_report', " .
        "date_collected = " . QuotedOrNull(oresData("form_date_collected", $lino)) . ", " .
        "specimen_num = '" . oresData("form_specimen_num", $lino) . "', " .
        "report_status = '" . oresData("form_report_status", $lino) . "'"; */
		if (!empty($date_report)) {
			if ($report_id > 0) {
				$sql = "UPDATE procedure_report 
									SET procedure_order_id='$order_id', 
									specimen_num='$specimen_num', 
									report_status='$report_status', 
									procedure_order_seq='$order_seq', 
									date_report='$date_report', 
									date_collected='$date_collected', 
									review_status = 'reviewed' 								
									WHERE procedure_report_id = '$report_id'";
				sqlStatement($sql);
			} else {
				$sql = "INSERT INTO procedure_report 
									SET procedure_order_id='$order_id', 
									specimen_num='$specimen_num', 
									report_status='$report_status',
									procedure_order_seq='$order_seq', 
									date_report='$date_report', 
									review_status = 'reviewed', 
									date_collected='$date_collected'";
				$report_id = sqlInsert($sql);
			}
		}
		/* $sets =
        "procedure_report_id = '$current_report_id', " .
        "result_code = '" . oresData("form_result_code", $lino) . "', " .
        "result_text = '" . oresData("form_result_text", $lino) . "', " .
        "abnormal = '" . oresData("form_result_abnormal", $lino) . "', " .
        "result = '" . oresData("form_result_result", $lino) . "', " .
        "`range` = '" . oresData("form_result_range", $lino) . "', " .
        "units = '" . oresData("form_result_units", $lino) . "', " .
        "facility = '" . oresData("form_facility", $lino) . "', " .
        "comments = '" . add_escape_custom($form_comments) . "', " .
        "result_status = '" . oresData("form_result_status", $lino) . "'"; */
		if (!empty($date_report)) {
			if ($result_id > 0) {
				$sql = "UPDATE procedure_result 
							SET procedure_report_id='$report_id', 
								result_code='$result_code', 
								result_text='$result_text', 
								abnormal='$abnormal', 
								result='$result', 
								range='$range', 
								units='$units', 
								result_status='$result_status' 
								WHERE procedure_result_id = '$result_id'";
				sqlStatement($sql);
			} else {
				$sql = "INSERT INTO procedure_result 
							SET procedure_report_id='$report_id', 
								result_code='$result_code', 
								result_text='$result_text', 
								abnormal='$abnormal', 
								result='$result', 
								`range`='$range', 
								units='$units', 
								result_status='$result_status'";
				sqlInsert($sql);
			}
		}
	}
    public function saveLab(Lab $lab)
    {
        $procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,order_status,
		  diagnoses,patient_instructions,lab_id) VALUES(?,?,?,?,?,?,?,?,?,?)",
		  array($lab->provider,$lab->pid,$lab->encounter,$lab->timecollected,$lab->orderdate,$lab->priority,$lab->status,
			$lab->diagnoses,$lab->patient_instructions,$lab->lab_id));
	sqlStatement("INSERT INTO procedure_order_code (procedure_order_id,procedure_order_seq,procedure_code,procedure_name,procedure_suffix)
		     VALUES (?,?,?,?,?)",array($procedure_type_id,1,$lab->procedurecode,$lab->procedures,$lab->proceduresuffix));
	return true;
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
	$sql = "SELECT * FROM procedure_type AS pt WHERE pt.lab_id=? AND NAME LIKE ? AND pt.activity=1";
	$result = sqlStatement($sql,array($labId,$inputString."%"));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[$tmp['procedure_type_id']] = $tmp['name'] . '-' . $tmp['procedure_code']. '-' . $tmp['suffix'];
	}
	return $arr;
    }
    
    public function listAOE($procedureCode,$labId){
	$sql = "SELECT * FROM procedure_questions WHERE lab_id=? AND procedure_code=? AND activity=1";
	$result = sqlStatement($sql,array($labId,$procedureCode));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[] = $tmp['question_text'];
	}
	return $arr;
    }
}
?>

