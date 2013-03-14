<?php
namespace Lab\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

class ResultTable extends AbstractTableGateway
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
    {
        global $pid;
        $flagSearch = 0;
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
        
        $form_review    = 1; // review mode
        $lastpoid       = -1;
        $lastpcid       = -1;
        $lastprid       = -1;
        $encount        = 0;
        $lino           = 0;
        $extra_html     = '';
        $lastrcn        = '';
        $facilities     = array();
        //$pid          = 1;
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
            $groupby = "GROUP By po.procedure_order_id";
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
            $remoteHost	    = empty($row['remote_host'      ]) ? '' : $row['remote_host' ];
            $remoteUser	    = empty($row['login']) ? '' : $row['login' ];
            $remotePass	    = empty($row['password']) ? '' : $row['password' ];
            
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
        $report_id	= $data['procedure_report_id'];
        $order_id 	= $data['procedure_order_id'];
        $result_id 	= $data['procedure_result_id'];
        $specimen_num 	= $data['specimen_num'];
        $report_status 	= $data['report_status'];
        $order_seq 	= $data['procedure_order_seq'];
        $date_report 	= $data['date_report'];
        $date_collected = $data['date_collected'];
        
        $result_code	        = $data['result_code'];
        $procedure_report_id    = $data['procedure_report_id'];
        $result_text		= $data['result_text'];
        $abnormal		= $data['abnormal'];
        $result			= $data['result'];
        $range			= $data['range'];
        $units			= $data['units'];
        $result_status		= $data['result_status'];
        $facility		= $data['facility'];
        $comments		= $data['comments'];
         
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
}