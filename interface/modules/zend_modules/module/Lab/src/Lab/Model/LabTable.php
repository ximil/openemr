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
	
	public function listPatients()
	{	
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
		
			if ($form_review) {
				if ($review_status == "reviewed") continue;
			} else {
				if ($review_status == "received") continue;
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

				//}
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
		$order_id 		= $data['procedure_order_id'];
		$result_id 		= $data['procedure_result_id'];
		$specimen_num 	= $data['specimen_num'];
		$report_status 	= $data['report_status'];
		$order_seq 		= $data['procedure_order_seq'];
		$date_report 	= $data['date_report'];
		$date_collected = $data['report_status'];
		
		/* $sets =
        "procedure_order_id = '$order_id', " .
        "procedure_order_seq = '$order_seq', " .
        "date_report = '$date_report', " .
        "date_collected = " . QuotedOrNull(oresData("form_date_collected", $lino)) . ", " .
        "specimen_num = '" . oresData("form_specimen_num", $lino) . "', " .
        "report_status = '" . oresData("form_report_status", $lino) . "'"; */

		$sql = "INSERT INTO procedure_report 
							SET procedure_order_id='$order_id', 
							specimen_num='$specimen_num', 
							report_status='$report_status'";
		sqlInsert($sql);	
	}
}
?>
