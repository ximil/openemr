<?php

namespace Acl\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use \Application\Model\ApplicationTable;

class AclTable extends AbstractTableGateway
{
    protected $table = 'acl';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Acl());
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    
    /** Example for Model use Application Model */
    public function sqlTest()
    {
        $sql    = "SELECT * FROM log WHERE user=?";
        $params = array('admin');
        $obj    = new ApplicationTable;
        $result = $obj->sqlQuery($sql, $params);
        return $result;
    }
}