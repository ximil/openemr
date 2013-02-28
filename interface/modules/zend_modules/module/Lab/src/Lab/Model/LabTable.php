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
}
?>

