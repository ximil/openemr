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
    
    /**
     * 
     * @return object
     */
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    
    /**
     * Select Query
     * @param array $params
     * @return array
     */
    public function selectTable($params)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        if ($params['fields'] != '*') {
            $fields = $params['fields'];
        }
        $select = $sql->select($fields);
        $select->from($params['tableName']);
        
        foreach ($params as $key => $value) { 
            foreach ($value as $k => $v) {//echo '<pre>'; print_r($value); echo '</pre>';
                if ($key == 'join') {
                    $table = $v['table'];
                    $on = $v['on'];
                    $type = $v['type'];
                    if ($v['fields']) {
                            $select->join($table,$on,$v['fields'],$type);
                    }
                }
            }
            if ($key == 'where') 
               $select->where($value);
        }
       // echo $select->__toString(); //exit;
        $selectString = $sql->getSqlStringForSqlObject($select);
        $this->log($selectString);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
     }
    
    /**
     * Insert Query
     * @param array $params
     * @return int Last insert id
     */
    public function insertTable($params)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $insert = $sql->insert($params['tableName']);
        $insert->values($params['fields']);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $this->log($selectString);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $lastId = $adapter->getDriver()->getLastGeneratedValue();
        return $lastId;
    }
    
    /**
     * Update Query
     * @param array $params
     * @return array
     */
    public function updateTable($params)
    { 
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $update = $sql->update($params['tableName']);
        $update->set($params['fields']);
        $update->where($params['where']);
        $selectString = $sql->getSqlStringForSqlObject($update);
        $this->log($selectString);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
    /**
     * Delete Query
     * @param array $params
     * @return array
     */
    public function deleteTable($params)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $delete = $sql->delete();
        $delete->from($params['tableName']);
        $delete->where($params['where']);
        $selectString = $sql->getSqlStringForSqlObject($delete);
        $this->log($selectString);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
    /**
     * Count record
     * @return int record count
     */
    public function rowCount($params)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($params['tableName']);
        $select->columns(array('total' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        
        foreach ($params as $key => $value) { 
            if ($key == 'where') 
                $select->where($value);
        }

        $selectString = $sql->getSqlStringForSqlObject($select);
        $this->log($selectString);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $resultSet = new ResultSet();
        $resultSet->initialize($results);

        $tmp = $resultSet->toArray();
        $result = $tmp[0]['total'];
        return $result;
     }
    
    /**
     * Log all DB Transactions
     * Usege in other model
     * @example use \Application\Model\ApplicationTable 
     * @example $obj = new ApplicationTable() create an object
     * @example $obj->log($params) call log function
     * @param arry $params
     */
    public function log($params)
    {
        $fp = fopen(sys_get_temp_dir()."//test.txt", "a");
	fwrite($fp, "Log all evets \n" . print_r($params, 1) . "\n");
    }
    
}
