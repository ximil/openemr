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
namespace Cancercare\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use \Application\Model\ApplicationTable;
class CancercareTable extends AbstractTableGateway
{
    public $tableGateway;
    protected $applicationTable;

     public function __construct(TableGateway $tableGateway)
    {
      $this->tableGateway 		= 	$tableGateway;
      $adapter 			= 	\Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
      $this->adapter              	= 	$adapter;
      $this->resultSetPrototype   	= 	new ResultSet();
      $this->applicationTable	    	= 	new ApplicationTable;
    } 
    
    public function getSearchResults($data,$getCount=null)
    {
      $query_data = array();
      $query  = 	"SELECT pd.fname, pd.lname, pd.mname, date(fe.date) AS date, fe.pid, fe.encounter,
                   u.fname AS doc_fname, u.mname AS doc_mname, u.lname AS doc_lname, 
                   (SELECT count(encounter) FROM form_encounter WHERE pid=fe.pid) AS enc_count,
                   (SELECT DATE(date) FROM form_encounter WHERE pid=fe.pid ORDER BY date DESC LIMIT 1) 
                   AS last_visit_date,
                   (SELECT count(*) FROM ccda WHERE pid=fe.pid AND transfer=1 AND type='cancer_care') as ccda_transfer_count,
                   (SELECT count(*) FROM ccda WHERE pid=fe.pid AND transfer=1 AND status=1 AND type='cancer_care') as ccda_successfull_transfer_count,
                   pd.ss, pd.`street`, pd.city, pd.`state`, pd.`country_code`, pd.`postal_code`, 
                   pd.`phone_biz`, pd.`phone_home`, pd.`sex`,
                   pd.`DOB`, pd.`status`, pd.`religion`, pd.`race`, pd.`ethnicity`, pd.`language`
                   FROM form_encounter AS fe
                   JOIN patient_data AS pd ON pd.pid=fe.pid
                   LEFT JOIN users AS u ON u.id=fe.provider_id ";
      if($data['status']) {
        $query  .= " LEFT JOIN combination_form AS cf ON cf.encounter = fe.encounter ";
      }

      $query    .= " WHERE 1=1 ";

      if($data['status'] == "signed") {
        $query  .= " AND cf.encounter IS NOT NULL AND cf.encounter !=''";
      }

      if($data['status'] == "unsigned") {
        $query  .= " AND (cf.encounter IS  NULL OR cf.encounter ='')";
      }

      if($data['from_date'] && $data['to_date']) {
        $query       .= " AND fe.date BETWEEN ? AND ? ";
        $query_data[] = $data['from_date'];
        $query_data[] = $data['to_date'];
      }

      if($data['pid']) {
        $query       .= " AND (fe.pid = ? OR pd.fname LIKE ? OR pd.mname LIKE ? OR pd.lname LIKE ? OR CONCAT_WS(' ',pd.fname,pd.lname) LIKE ?) ";
        $query_data[] = $data['pid'];
        $query_data[] = "%".$data['pid']."%";
        $query_data[] = "%".$data['pid']."%";
        $query_data[] = "%".$data['pid']."%";
        $query_data[] = "%".$data['pid']."%";
      }

      if($data['encounter']) {
        $query       .= " AND fe.encounter = ? ";
        $query_data[] = $data['encounter'];
      }

      $query   .= " GROUP BY fe.pid ";

      $query   .= " ORDER BY fe.pid, fe.date ";

      $appTable = new ApplicationTable();

      if($getCount){
        $res        = $appTable->zQuery($query, $query_data);
        $resCount 	= $res->count();
        return $resCount;
      }

      $query 		 .= " LIMIT ".$data['limit_start'].",".$data['results'];
      $resDetails = $appTable->zQuery($query, $query_data);
      $records = array();
      foreach($resDetails as $row){
        $records[] = $row;
      }
      return $records;
    }
    
    public function getStatus($data)
    {
      $pid 		= "''";
      foreach($data as $row){
        if($pid) $pid .= ',';
        $pid          .= $row['pid'];
      }
      $query 	    = "SELECT cc.*, DATE(fe.date) AS dos, CONCAT_WS(' ',u.fname, u.mname, u.lname) 
                     AS user_name FROM ccda AS cc
                     LEFT JOIN form_encounter AS fe ON fe. pid = cc.pid AND fe.encounter = cc.encounter
                     LEFT JOIN users AS u ON u.id = cc.user_id
                     WHERE cc.pid in ($pid) AND cc.type=? ORDER BY cc.pid, cc.time desc";
      $appTable   = new ApplicationTable();
      $result     = $appTable->zQuery($query,array('cancer_care'));
      return $result;
    }
}


