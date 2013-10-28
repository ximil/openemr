<?php
/**
 * Z&H Consultancy Services Zend Framework 2 Modules
 *
 * @link      
 * @copyright Copyright (c) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @license 
 * A copy of the GNU General Public License is included along with this program:
 * openemr/interface/login/GnuGPL.html
 * For more information write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * @author Remesh Babu S <remesh@zhservices.com>  
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
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
}
