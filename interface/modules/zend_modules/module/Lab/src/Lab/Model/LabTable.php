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
    public function saveLab(Lab $lab)
    {
        $data = array(
            'artist' 	=> $lab->artist,
            'title'  	=> $lab->title,
            'category'  => $lab->category,
        );

        $id = (int)$lab->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getLab($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
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
    public function listProcedures($inputString='s',$labId='2')
    {
	$sql = "SELECT * FROM procedure_type AS pt LEFT OUTER JOIN procedure_questions AS pq ON pt.procedure_code=pq.procedure_code
		WHERE pt.lab_id=? AND NAME LIKE ? AND pt.activity=1";
	$result = sqlStatement($sql,array($labId,$inputString."%"));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[$tmp['procedure_type_id']] = $tmp['name'] . '-' . $tmp['procedure_type_id'] . '-' . $tmp['procedure_code'];
	}
	//$fh = fopen("D:/test11111.txt","a");
	//fwrite($fh,print_r($arr,1));
	return $arr;
    }
}
?>

