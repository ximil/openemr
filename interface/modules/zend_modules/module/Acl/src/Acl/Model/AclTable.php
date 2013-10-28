<?php

namespace Acl\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use \Application\Model\ApplicationTable ;

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
    
    /** Log testing */
    public function testing()
    {
        $parameter = array(
                    'tableName' => 'tableName',
                    'fields'    => 'fields',
                    'join'      => 'join',
                    'where'     => 'where',
                 );
        $obj = new ApplicationTable;
        $obj->log($parameter);   
    }
}
