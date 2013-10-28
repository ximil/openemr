<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;


class ApplicationTable extends AbstractTableGateway
{
    protected $table = 'application';
    protected $adapter;
    /**
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct()
    {
      $adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
      $this->adapter = $adapter;
      $this->resultSetPrototype = new ResultSet();
      $this->resultSetPrototype->setArrayObjectPrototype(new Application());
      $this->initialize();
    }
     
    public function sqlQuery($sql, $params = '')
    {
      $statement = $this->adapter->query($sql);
      $return = $statement->execute($params);
      $count = count($params);
      $arr = array();
      foreach($params as $val) {
        array_push($arr, "'" . $val . "'");
      }
      $logSQL = preg_replace(array_fill(0, $count, "/\?/"), $arr, $sql, 1);
      $this->log($logSQL);
      return $return;
    }
    
    /**
     * Log all DB Transactions
     * Usege in other model
     * @example use \Application\Model\ApplicationTable 
     * @example $obj = new ApplicationTable() create an object
     * @example $obj->log($params) call log function
     * @param arry $params
     */
    public function log($logSQL)
    {
      $sql        = "INSERT INTO log SET date = ? ,user = ?, groupname = ?, comments = ?";
      $dt         = date('Y-m-d  H:i:s');
      $user       = $_SESSION['authUser'];
      $group      = $_SESSION['authGroup'];
      $params     = array($dt, $user, $group, $logSQL);
      $statement  = $this->adapter->query($sql);
      $return     = $statement->execute($params);
      return true;
    }
    
}
