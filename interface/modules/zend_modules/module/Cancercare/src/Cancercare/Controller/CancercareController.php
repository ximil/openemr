<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2014 Z&H Consultancy Services Private Limited <sam@zhservices.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*    @author  Chandni Babu <chandnib@zhservices.com>
* +------------------------------------------------------------------------------+
*/
namespace Cancercare\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Cancercare\Form\CancercareForm;
use Application\Listener\Listener;

class CancercareController extends AbstractActionController
{
    protected $cancercareTable;
    
    protected $listenerObject;
    
    protected $date_format;
    
    public function __construct()
    {
      $this->listenerObject	= new Listener;
      $this->date_format = \Application\Model\ApplicationTable::dateFormat();
    }
    
     public function getCancercareTable()
    {
        if (!$this->cancercareTable){
            $sm = $this->getServiceLocator();
            $this->cancercareTable = $sm->get('Cancercare\Model\CancercareTable');
        }
        return $this->cancercareTable;
    }
    
    /**
     * Index Page
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
      $request        = $this->getRequest(); 
      $fromDate       = $request->getPost('form_date_from', null);
      $fromDate       = $this->CommonPlugin()->date_format($fromDate, 'yyyy-mm-dd', $GLOBALS['date_display_format']);
      $toDate         = $request->getPost('form_date_to', null);
      $toDate         = $this->CommonPlugin()->date_format($toDate, 'yyyy-mm-dd', $GLOBALS['date_display_format']);
      $pid            = $request->getPost('form_pid', null);
      $encounter      = $request->getPost('form_encounter', null);
      $status         = $request->getPost('form_status', null);

      if(!$pid && !$encounter && !$status){
        $fromDate   = $request->getPost('form_date_from', null) ? $this->CommonPlugin()->date_format($request->getPost('form_date_from', null), 'yyyy-mm-dd', $GLOBALS['date_display_format']) : date('Y-m-d',strtotime(date('Ymd')) - (86400*7));
        $toDate     = $request->getPost('form_date_to', null) ? $this->CommonPlugin()->date_format($request->getPost('form_date_to', null), 'yyyy-mm-dd', $GLOBALS['date_display_format']) : date('Y-m-d');
      }
        
      $results        = $request->getPost('form_results', 100);
      $results        = ($results > 0) ? $results : 100;
      $current_page   = $request->getPost('form_current_page', 1);
      $expand_all     = $request->getPost('form_expand_all', 0);
      $select_all     = $request->getPost('form_select_all', 0);
      $end            = $current_page*$results; 
      $start          = ($end - $results);
      $new_search     = $request->getPost('form_new_search',null);
      $form_sl_no     = $request->getPost('form_sl_no', 0);
      
      $params     = array(
                    'from_date'     => $fromDate,
                    'to_date'       => $toDate,
                    'pid'           => $pid,
                    'encounter'     => $encounter,
                    'status'        => $status,
                    'results'       => $results,
                    'current_page'  => $current_page,
                    'limit_start'   => $start,
                    'limit_end'     => $end,
                    'sl_no'         => $form_sl_no,
                    'select_all'    => $select_all,
                    'expand_all'    => $expand_all
                );
      
      if($new_search) {
          $count  = $this->getCancercareTable()->getSearchResults($params,1);
      } else {
          $count  = $request->getPost('form_count',$this->getCancercareTable()->getSearchResults($params,1));
      }
      $totalpages             = ceil($count/$results);

      $details                = $this->getCancercareTable()->getSearchResults($params);
      $status_details         = $this->getCancercareTable()->getStatus($this->getCancercareTable()->getSearchResults($params));
      
      $params['res_count']   = $count;
      $params['total_pages'] = $totalpages;
      
      $index = new ViewModel(array(
          'details'       => $details,
          'form_data'     => $params,
          'table_obj'     => $this->getCancercareTable(),
          'status_details'=> $status_details,
          'commonplugin'  => $this->CommonPlugin(),
          'listenerObject'=> $this->listenerObject
      ));
      return $index;
    }
}