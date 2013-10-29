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

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $applicationTable;
    
    public function indexAction()
    {
        /**
         * function sqlQuery
         * Executeing SQL Statments 
         * 
         * @param string $sql SQL Statments
         * @param $array $params parameters
         */
        
        $sql = "SELECT * FROM log WHERE user=?";
        $params = array('admin');
        $result = $this->getApplicationTable()->sqlQuery($sql, $params);
        foreach ($result as $row) {
            echo '<pre>'; print_r($row); echo '</pre>';
        }
        
        /**
         * SQL Query Examples
         */
        
        $listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
        
        /**
         * SELECT Query
         * @param array $parameter All table parameters
         * @param string $tableName Table Name
         * @param string $fields All field names seperated by comma
         * @param string $where where clause and arguments
         * @param array $join table name, ON parameters, join type (left/right..)
         * @param string $order order clause
         * @param string $group group clause
         * @param string $limit limit clasue
         */
        $tableName  = "procedure_order";
        $fields     = "*";
        $join       = array(
                                array(
                                    'table' => array(
                                                    't1'=>'procedure_order_code',
                                                 ), 
                                    'on'    => 't1.procedure_order_id = procedure_order.procedure_order_id', 
                                    'fields'=> array (
                                                    'foo'=>'procedure_name',
                                                    'bar'=>'procedure_suffix',
                                                 ), 
                                    'type'  => 'left',
                                ),
                                array(
                                    'table' => array(
                                                    't2'=>'procedure_answers',
                                                 ), 
                                    'on'    => 't2.procedure_order_id = t1.procedure_order_id', 
                                    'fields'=> array (
                                                    'qCode'=>'question_code',
                                                    'qAns'=>'answer',
                                                 ), 
                                    'type'  => 'left',
                                ),
                            );

        $where      = "procedure_order.procedure_order_id=2";
        $parameter  = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                        'join'      => $join,
                        'where'     => $where,
                     ); 
        //$data = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
        /**
        * INSERT Query
        * @param array $parameter All table parameters
        * @param string $tableName Table Name
        * @param array $fields all field names and values (array key is the field name)
        * @param string $where where clause and arguments
        * @param int $data last insert id
        */
       
        $tableName  = "test";
        $fields     = array(
                                'name'      => 'Jak',
                                'age'       => 30
                            );
        $parameter = array(
                                'tableName' => $tableName,
                                'fields'    => $fields,
                            );
        
        //$data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);

        /**
        * Update Query
        * @param array $parameter All table parameters
        * @param string $tableName Table Name
        * @param array $fields all field names and values (array key is the field name)
        * @param string $where where clause and arguments
        */
       
        $tableName  = "test";
        $fields     = array(
            'name'  => 'Jak',
            'age'   => 32
        );
        $where     = "id=5";
        $parameter = array(
            'tableName' => $tableName,
            'fields'    => $fields,
            'where'    => $where,
         );
        
        //$data = $this->getEventManager()->trigger('updateEvent', $this, $parameter);
        
         /**
        * Delete Query
        * @param array $parameter All table parameters
        * @param string $tableName Table Name
        * @param string $where where clause and arguments
        */
       
        $tableName = "test";
        $where     = "id=3";
        $parameter = array(
            'tableName' => $tableName,
            'where'    => $where,
         );
        
        //$data = $this->getEventManager()->trigger('deleteEvent', $this, $parameter);

        /**
        * Count
        * @param array $parameter All table parameters
        * @param string $tableName Table Name
        * @param array $fields All field names and values
        */
       
        $tableName 	= "test";
        $where          = "id=2";
        $parameter = array(
            'tableName' => $tableName,
            'where'    => $where,
         );
        
        //$data = $this->getEventManager()->trigger('countEvent', $this, $parameter);
        //echo 'Count is ' . $data[0]; exit;
        
        /** End Examples */
        
        return new ViewModel();
    }
    
    /**
     * Table Gateway
     * 
     * @return type
     */
    public function getApplicationTable()
    {	
        if (!$this->applicationTable) {
            $sm = $this->getServiceLocator();
            $this->applicationTable = $sm->get('Application\Model\ApplicationTable');
        }
        return $this->applicationTable;
    }
}
