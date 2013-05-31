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

class UnassociatedTable extends AbstractTableGateway
{
    public $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
      $this->tableGateway = $tableGateway;
    }
    
    public function listPdf()
    {
      $sql = "SELECT * FROM procedure_result_unassociated WHERE attached = 0";
      $result = sqlStatement($sql);
      $arr = array();
      while ($row = sqlFetchArray($result)) {
          $arr[] = $row;
      }
      return $arr;
    }
    
    public function listResolvedPdf()
    {
      $sql = "SELECT * FROM procedure_result_unassociated WHERE attached = 1";
      $result = sqlStatement($sql);
      $arr = array();
      while ($row = sqlFetchArray($result)) {
          $arr[] = $row;
      }
      return $arr;
    }
		
    public function attachUnassociatedDetails($request)
    {
      $sqlupd = "UPDATE procedure_result_unassociated SET attached = 1, comment = ? WHERE id = ?";
      $param = array();
      array_push($param,'');
      array_push($param,$request->file_id);
      $result = sqlQuery($sqlupd,$param);
    }
}
?>

