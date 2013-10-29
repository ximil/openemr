<?php
//    +-----------------------------------------------------------------------------+ 
//    OpenEMR - Open Source Electronic Medical Record
//    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU Affero General Public License as
//    published by the Free Software Foundation, either version 3 of the
//    License, or (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU Affero General Public License for more details.
//
//    You should have received a copy of the GNU Affero General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
//    Author:   Remesh Babu S <remesh@zhservices.com>
//
// +------------------------------------------------------------------------------+
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
