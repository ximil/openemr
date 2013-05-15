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

class SpecimenTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function listOrders($pat_id,$from_dt,$to_dt,$search_status)
    {
				$sql = "SELECT *,poc.procedure_order_id AS poid, poc.procedure_order_seq AS poseq, CONCAT(pd.lname,' ',pd.fname) AS pname FROM procedure_order po JOIN procedure_order_code poc ON poc.procedure_order_id = po.procedure_order_id AND po.order_status = 'pending' AND po.psc_hold = 'onsite' AND po.activity = 1 LEFT JOIN patient_data pd ON pd.pid = po.patient_id LEFT JOIN procedure_report pr ON pr.procedure_order_id = poc.procedure_order_id AND pr.procedure_order_seq = poc.procedure_order_seq";
				if($pat_id || $from_dt || $to_dt || ($search_status != 'all')){
						$sql .= " WHERE";
						$cond = 0;
						$param = array();
						if($pat_id){
								$sql .= " po.patient_id = ?";
								array_push($param,$pat_id);
								$cond = 1;
						}
						if($from_dt && $to_dt){
								if($cond){
										$sql .= " AND po.date_ordered BETWEEN ? AND ?";
								}else{
										$sql .= " po.date_ordered BETWEEN ? AND ?";
										$cond = 1;
								}
								array_push($param,$from_dt,$to_dt);
						}elseif($from_dt){
								if($cond){
										$sql .= " AND po.date_ordered > ?";
								}else{
										$sql .= " po.date_ordered > ?";
										$cond = 1;
								}
								array_push($param,$from_dt);
						}elseif($to_dt){
								if($cond){
										$sql .= " AND po.date_ordered < ?";
								}else{
										$sql .= " po.date_ordered < ?";
										$cond = 1;
								}
								array_push($param,$to_dt);
						}
						if($search_status != 'all'){
								if($cond){
										$sql .= " AND pr.report_status = ?";
								}else{
										$sql .= " pr.report_status = ?";
										$cond = 1;
								}
								array_push($param,$search_status);
						}
						$sql .= " ORDER BY po.procedure_order_id DESC";
						$result = sqlStatement($sql,$param);
				}else{
						$sql .= " ORDER BY po.procedure_order_id DESC";
						$result = sqlStatement($sql);
				}
				$arr = array();
				while ($row = sqlFetchArray($result)) {
						$arr[] = $row;
				}
				return $arr;
    }
		
		public function getPatientName($pat_id)
    {
				$sql = "SELECT CONCAT(lname,' ',fname) AS pname FROM patient_data WHERE pid = ?";
				$param = array($pat_id);
				$pres = sqlQuery($sql,$param);
				return $pres['pname'];
    }
		
		public function listPatients($inputString)
    {
				$sql = "SELECT CONCAT(pd.lname,' ',pd.fname) as pname, pid FROM patient_data AS pd WHERE pd.lname LIKE ? OR pd.fname LIKE ? OR pd.pid LIKE ?";
				$result = sqlStatement($sql,array($inputString."%",$inputString."%",$inputString."%"));
				$arr = array();
				$i = 0;
				while($tmp = sqlFetchArray($result)) {
						$arr[] =  htmlspecialchars($tmp['pname'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['pid'],ENT_QUOTES);
				}
				return $arr;
    }
		
		public function saveSpecimenDetails($request)
    {
				$existing_query = "SELECT * FROM procedure_report WHERE procedure_order_id = ? AND procedure_order_seq = ?";
				$sqlins = "INSERT INTO procedure_report SET specimen_num = ?, date_collected = ?, report_status = ?, procedure_order_id = ?, procedure_order_seq = ?";
				$sqlupd = "UPDATE procedure_report SET specimen_num = ?, date_collected = ?, report_status = ? WHERE procedure_order_id = ? AND procedure_order_seq = ?";
				for($i=0;$i<count($request->procedure_order_id);$i++){
						$param = array();
						array_push($param,$request->specimen[$i]);
						array_push($param,$request->specimen_collected_time[$i]);
						array_push($param,$request->specimen_status[$i]);
						array_push($param,$request->procedure_order_id[$i]);
						array_push($param,$request->procedure_order_seq[$i]);
						$existing_res = sqlStatement($existing_query,array($request->procedure_order_id[$i],$request->procedure_order_seq[$i]));
						if(sqlNumRows($existing_res) > 0){
								$result = sqlQuery($sqlupd,$param);
						}else{
								if($request->specimen[$i] || $request->specimen_collected_time[$i]){
										$result = sqlQuery($sqlins,$param);
								}
						}
				}
    }
}
?>

