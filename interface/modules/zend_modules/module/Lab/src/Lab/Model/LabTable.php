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

class LabTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
	
	public function listLabResult($data)
	{	global $pid;
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
		//$pid = 1;
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
//echo $sql;
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
			"ICD9:".$lab->diagnoses,$lab->patient_instructions,$lab->lab_id));
	sqlStatement("INSERT INTO procedure_order_code (procedure_order_id,procedure_order_seq,procedure_code,procedure_name,procedure_suffix)
		     VALUES (?,?,?,?,?)",array($procedure_type_id,1,$lab->procedurecode,$lab->procedures,$lab->proceduresuffix));
	return $procedure_type_id;
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
	
	/**
	* Vipin
	*/
	public function getColumns($result)
    {
            //print_r($result);
            
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
        //print_r($result_columns);
        //print_r($column_map);		
        
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
    }

    function importDataCheck($result,$column_map)//CHECK DATA IF ALREADY EXISTS
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
                    $value_arr[$column] =  ${$servercol};
                }
                
                $fields		= implode(",",$columns);
                $col_count	= count($columns);
                $field_vars	= "$".implode(",$",$columns);
                $params		= rtrim(str_repeat("?,",$col_count),",");
                
                
                /*
                 $column_map['contraints']   = array('procedure_type' => array(
                                                                        'primary_key' => array(
                                                                                                '0'     => "lab_id",
                                                                                                '1'     => "procedure_code"))); 
                */
                //$sql_check  = "SELECT COUNT(*) FROM ".$table." WHERE  ";
               //print_r($constraint_arr);
                $primary_key_arr = $column_map['contraints'][$table]['primary_key'];
                if(count($primary_key_arr) > 0)
                {
                    //print_r($primary_key_arr);
                    $index      = 0;
                    $condition  = "";
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
                    //echo "<br>PK Array :";
                    //print_r($primary_key_arr);
                    //echo "<br>Update Array :";
                    //print_r($update_arr);
                    //echo "<br>Check Array :";
                    //print_r($check_value_arr);
                    
                    $update_combined_arr    = array_merge($update_arr,$check_value_arr);
                    //echo "<br>Merged Array :";
                    //print_r($update_combined_arr);
                    //echo "------------------------------------------------------------------------------------------------";
                    $index      = 0;
                    //$condition  = "";
                    $update_key_arr    = array();
                    
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
                    }
                }
                
                
                //print_r($value_arr);
            }
            //echo "<br>";
            $count++;
            
            //if($count > 5)
            //{
            //    break;
            //}
        }
    }

    //$constraint_arr
    
    public function pullCompandianTestConfig()
    {
        
            
        
        
        /*$column_map['test_id'] 		        = array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "procedure_type_id",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));*/
        
	$column_map['test_lab_id']	        = array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "lab_id",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_lab_entity'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "seccol",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "1"));
	$column_map['test_code'] 		= array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "procedure_code",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_specimen_state'] 	= array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "specimen",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_unit_code'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_status_indicator'] 	= array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "activity",
                                                                            'value_map' => "1",
                                                                            'insert_id' => "0"),
                                                        'valconfig' => array(
                                                                            'A'         => "1",
                                                                            'I'         => "0"));
        
	$column_map['test_insert_datetime'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_description'] 	= array('colconfig' => array(
                                                                            'table'     => "procedure_type",
                                                                            'column'    => "description",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_specimen_type'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_service_code'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_lab_site'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_update_datetime'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_update_user'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_code_suffix'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_is_profile'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_is_select'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_performing_site'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_flag'] 		= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_is_not_billed'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_is_billed_only'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_reflex_count'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_conforming_indicator']= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_alternate_temp'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_pap_indicator'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	$column_map['test_last_updatetime'] 	= array('colconfig' => array(
                                                                            'table'     => "",
                                                                            'column'    => "",
                                                                            'value_map' => "0",
                                                                            'insert_id' => "0"));
	
	
        $column_map['contraints']   = array('procedure_type' => array(
                                                                        'primary_key' => array(
                                                                                                '0'     => "lab_id",
                                                                                                '1'     => "procedure_code"))); 
        
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
    
    
    public function mapcolumntoxml()
    {
        
        
        
        $xmlconfig['patient_data']    = array(                      
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
        
        
        $xmlconfig['insurance_data']    = array(
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
        
        $xmlconfig['insurance_companies']    = array(
                                                'column_map'    => array(
                                                                        'name'  => '$type_insurance_name'                                                
                                                                        ),
                                                'primary_key'   => array('id'),
                                                'match_value'   => array('provider'),
                                                'parent_table'  => 'insurance_data'
                                                );
        
        
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
    
    
    public function generateSQL($pid,$cofig_arr,$table)
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
        
        $guarantor_lname                        = "";    
        $guarantor_fname                        = "";    
        $guarantor_address                      = "";    
        $guarantor_city                         = "";    
        $guarantor_state                        = "";    
        $guarantor_postal_code                  = "";    
        $guarantor_phone_no                     = "";    
        $guarantor_mname                        = "";    
        $ordering_provider_id                   = "1122334455";    
        $ordering_provider_lname                = "Allan";    
        $ordering_provider_fname                = "Joseph";    
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
        
        $cofig_arr  = $this->mapcolumntoxml();
       
        $sl = 0;
        foreach($cofig_arr as $table => $config)
        {
            
            //print_r($cofig_arr);
            
            //SKIP THE DATA FETCHING OF CHILD TABLE ROWS , WHICH WILL AUTOMATICALLY FETCH IF IT IS CONFIGURED, IT HAS PARENT TABLE
            if($config['parent_table'] <> "")
            {
                continue;
            }
            
            
            $col_map_arr        = $cofig_arr[$table]['column_map'];
            //print_r($col_map_arr);
            
            $res    = $this->generateSQL($pid,$cofig_arr,$table);
            
            while($data = sqlFetchArray($res))
            {
                $global_arr  = array();
                
                $check_arr   = array();
                
                foreach($col_map_arr as $col => $tag)
                {   
                    //if($data[$col] <> "")
                    //{
                        if(substr($tag,0,1)== "#")
                        {
                            $tag            = substr($tag,1,strlen($tag));
                            $check_arr[]    = "$".$tag;
                        }
                        
                        //print_r($check_arr);
                        foreach($check_arr as $check)
                        {
                            if(strstr($tag,$check))
                            {
                                $tag = str_replace($check,${ltrim($check,"$")},$tag);
                               
                            }
                           //echo $substr[0];
                        }
                        
                        $$tag   = $data[$col];
                        //echo '<br>$'.$tag." = ".$$tag;                        
                    //}
                }
                
                //print_r($data);
                if($config['child_table'] <> "")
                {
                    //print_r($cofig_arr[$config['child_table']]);
                    $res2    = $this->generateSQL($pid,$cofig_arr,$config['child_table']);
                    
                    $fetch2_count    = 0; 
                    while($data1 = sqlFetchArray($res2))
                    {
                        //print_r($data1);
                        $col_map_arr2        = $cofig_arr[$config['child_table']]['column_map'];
                        
                        ////print_r($col_map_arr);
                        foreach($col_map_arr2 as $col => $tag)
                        {   
                            //if($data1[$col] <> "")
                            //{
                                //echo "<br>cols :".$tag;
                                //print_r($check_arr);
                                foreach($check_arr as $check)
                                {
                                    if(strstr($tag,$check))
                                    {
                                        //echo $check." => ".${ltrim($check,"$")};
                                        $tag = str_replace($check,${ltrim($check,"$")},$tag);
                                       
                                    }
                                    //echo $substr[0];
                                }
                                
                                if(substr($tag,0,1)== "#")
                                {
                                    $tag = substr($tag,1,strlen($tag));                        
                                }
                                
                                $$tag   = $data1[$col];
                                
                                //echo '<br>$'.$tag." = ".$$tag;
                                //echo "Provider :".$provider;
                                //$provider   = $ins_data['provider'];
                                //$comp_data  = sqlQuery($sql3,array($provider));
                            //}
                        }
                        //echo "<br> Row ".$fetch_count." has child ";
                    }
                    
                }
                //echo "<br> Row ".$fetch_count." over ";
            }
            //
            //echo "<br>";
            //print_r($data);
            //exit;
            
        }
        $xmlfile = ($xmlfile <> "") ? $xmlfile : "order_new_".gmdate('YmdHis').".xml";
        
        $config = new Config\Config(array(), true);
        $config->Order = array();
        
        foreach($xmltag_arr as $tag)
        {
            $tag_val = (trim(${$tag}) <> "") ? ${$tag} : "";
            $config->Order->$tag    = $tag_val;            
        }
        
        $sql_misc   = "SELECT  tb1.procedure_order_id, diagnoses, procedure_code,  procedure_suffix
                            FROM procedure_order tb1 LEFT JOIN procedure_order_code tb2 ON tb1.procedure_order_id = tb2.procedure_order_id 
                        WHERE patient_id = ? AND order_status = ? AND lab_id = ? ";
          
        $misc_value_arr = array();
        
        $misc_value_arr['patient_id']   = $pid;
        $misc_value_arr['order_status'] = "pending";
        $misc_value_arr['lab_id']       = $lab_id;
        
        $res_misc   = sqlStatement($sql_misc,$misc_value_arr);
        
        $test_count = 0;
        $diag_count = 0;
        
        $test_id    = "";
        $diagnosis  = "";
        
        while($data_misc = sqlFetchArray($res_misc))
        {
            if(($data_misc['procedure_code'] <> "") && ($data_misc['procedure_suffix'] <> ""))
            {
                $test_id    .= $data_misc['procedure_code']."#!#".$data_misc['procedure_suffix'];
                                
                $test_count++;
            }
            
            $test_id.="#--#";
            
            if($data_misc['diagnoses'] <> "")
            {
                $diag_arr    =  explode(";",$data_misc['diagnoses']);
                
                foreach($diag_arr as $diag)
                {
                    $diag_array     =  explode(":",$diag,2);
                    $diagnosis     .= $diag_array[1];
                    $diagnosis     .= "#@#";
                }
                
                $diag_count++;
            }
            $diagnosis.="#~@~#";
            
            
        }
        
        $config->Order->test_id             = $test_id;
        $config->Order->test_diagnosis      = $diagnosis;
        $config->Order->test_aoe            = $test_aoe;        

        $writer = new Config\Writer\Xml();
        //echo $writer->toString($config);
        $writer->toFile("module/Lab/".$xmlfile,$config);
        
        return $xmlfile;
        
    }
}
?>

