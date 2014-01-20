<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
*    @author  Remesh Babu S <remesh@zhservices.com>
* +------------------------------------------------------------------------------+
*/

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
     * @param String  $sql      SQL Query Statment
     * @param array   $params   SQL Parameters
     * @param boolean $log      Logging Yes /  No
     * @param boolean $error    Error Display Yes / No
     * @return type
     */
    public function zQuery($sql, $params = '', $log = TRUE, $error = TRUE)
    {
      $return = false;
      $result = false;
      $id     = 0;
      
      try {
        $statement  = $this->adapter->query($sql); 
        $return     = $statement->execute($params);
        $id         = $return->getGeneratedValue(); 
        $result     = true;
      } catch (\Zend\Db\Adapter\ExceptionInterface $e) {
        if ($error) {
          $message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
          print  \Application\Listener\Listener::z_xl("ERROR " . $message) . "<br>" . $sql . "<br/>";
        }
      } catch (\Exception $e) {
        if ($error) {
          $code = $e->getCode();
          $file = $e->getFile();
          $line = $e->getLine();
          print  \Application\Listener\Listener::z_xl("ERROR " . $e->getMessage()) . "<br>" . $sql . "<br/>";
        }
      }
      /**
       * Logging Mechanism
       * using OpenEMR log function (auditSQLEvent)
       * log is true
       */
      if ($log) {
        auditSQLEvent($sql, $result, $params);
      }
      return $return;
    }
            
    /**
     * Function escapeTableName
     * Check the Table is exist in the DB
     * 
     * @param string $tableName 
     * @return type
     */
    public function escapeTableName($tableName)
    {
      $sql        = "SHOW TABLES";
      $statement  = $this->adapter->query($sql);
      $return     = $statement->execute();
      $arrTable   = array();
      foreach ($return as $key => $value) {
        $k = array_keys($value);
        $arrTable[] = $value[$k[0]];
      }
      /** Now can escape(via whitelisting) the sql table name */
      return $this->escapeIdentifier($tableName, $arrTable, TRUE, FALSE);
    }
    
    /**
     * Function escapeColumnName
     * Filter Database Column Name
     * 
     * @param string  $columnName  Column Name for Check
     * @param array   $arrTables   All Table Names in the Db
     * @param boolean $long        If Column with Table Name 
     * @return type
     */
    public function escapeColumnName($columnName, $arrTables, $long = FALSE)
    {
      /** If the $tables is empty, then process them all */
      if (empty($arrTables)) {
        $statement  = $this->adapter->query("SHOW TABLES");
        $return     = $statement->execute();
        $arrTable   = array();
        foreach ($return as $key => $value) {
          $k          = array_keys($value);
          $arrTable[] = $value[$k[0]];
        }
      }

      /** First need to escape the $tables */
      $tablesEscaped = array();
      foreach ($arrTables as $table) {
        $tablesEscaped[] = $this->escapeTableName($table);
      }

      /** Collect all the possible sql columns from the tables */
      $arrColumns = array(); 
      foreach ($tablesEscaped as $tableEscaped) {
        $statement = $this->adapter->query("SHOW COLUMNS FROM " . $tableEscaped);
        $return    = $statement->execute();
        foreach ($return as $key => $value) {
          if ($long) {
            $arrColumns[] = $tableEscaped . "." . $value['Field'];
          } else {
            $arrColumns[] = $value['Field'];
          }
        }
      }
      
      /** Now can escape(via whitelisting) the sql column name */
      return $this->escapeIdentifier($columnName, $arrColumns, TRUE);
    }
    
    /**
     * Function escapeIdentifier
     * Check if the given Column / Table Name existed in the DB
     * 
     * @param string  $name           Table Name or Column Name
     * @param array   $arrTable       Existing Tables in the DB
     * @param boolean $dieNoMatch 
     * @param boolean $caseSensMatch  
     * @return type
     */
    public function escapeIdentifier($name, $arrList, $dieNoMatch = FALSE, $caseSensMatch = TRUE)
    {
      if (is_array($arrList)) {
        /** Only return an item within the whitelist_items */
        $arr = $arrList;
        /** First, search for case sensitive match */
        $key = array_search($name, $arr);
        if ($key === FALSE) {
          /** No match */
          if (!$caseSensMatch) {
            /** Attempt a case insensitive match */
            $arrUpper = array_map("strtoupper", $arr);
            $key      = array_search(strtoupper($name), $arrUpper);
          };
          if ($key === FALSE) {
            /** Still no match */
            if ($dieNoMatch) {
              $escaper = new \Zend\Escaper\Escaper('utf-8');
              /**  No match and $die_if_no_match is set, so die() and send error messages to screen and log */
              error_log("ERROR: OpenEMR SQL Escaping ERROR of the following string: " . $name, 0);
              die("<br><span style='color:red;font-weight:bold;'>" 
                      . \Application\Listener\Listener::z_xl("There was an OpenEMR SQL Escaping ERROR of the following string") 
                      . " " . $escaper->escapeHtmlAttr($name)."</span><br>");
            } else {
              /** Return first token since no match */
              $key = 0;              
            }
          }
        }
        return $arr[$key];
      } else {
        /** 
         * Return an item that has been "cleaned" up 
         * (this is currently experimental and goal is to avoid using this) 
         */
        return preg_replace('/[^a-zA-Z0-9_.]/', '', $name);
      }
    }
    
    /**
     * Function quoteValue
     * Escape Quote in the value
     * 
     * @param type $value
     * @return type
     */
    public function quoteValue($value)
    {
      return $this->adapter->platform->quoteValue($value);
    }

    /**
     * Checks ACL
     * @param String $user_id Auth user Id
     * $param String $section_identifier ACL Section id
     * @return boolean
     */
    public function aclcheck($user_id,$section_identifier){
        $sql_user_acl   = " SELECT 
                                COUNT(allowed) AS count 
                            FROM
                                module_acl_user_settings AS usr_settings 
                                LEFT JOIN module_acl_sections AS acl_sections 
                                    ON usr_settings.section_id = acl_sections.`section_id` 
                            WHERE 
                                acl_sections.section_identifier = ? AND usr_settings.user_id = ? AND usr_settings.allowed = ?";
        $sql_group_acl  = " SELECT 
                                COUNT(allowed) AS count 
                            FROM
                                module_acl_group_settings AS group_settings 
                                LEFT JOIN module_acl_sections AS  acl_sections
                                  ON group_settings.section_id = acl_sections.section_id
                            WHERE
                                acl_sections.`section_identifier` = ? AND group_settings.group_id IN (?) AND group_settings.allowed = ?";
        $sql_user_group = " SELECT 
                                gagp.id AS group_id
                            FROM
                                gacl_aro AS garo 
                                LEFT JOIN `gacl_groups_aro_map` AS gamp 
                                    ON garo.id = gamp.aro_id 
                                LEFT JOIN `gacl_aro_groups` AS gagp
                                    ON gagp.id = gamp.group_id
                                RIGHT JOIN `users_secure` usr 
                                    ON usr. username =  garo.value
                            WHERE
                                garo.section_value = ? AND usr. id = ?";
                                
        $res_groups     = $this->zQuery($sql_user_group,array('users',$user_id));
        $groups = array();
        foreach($res_groups as $row){
          array_push($groups,$row['group_id']);
        }
        $groups_str = implode(",",$groups);
        
        $count_user_denied      = 0;
        $count_user_allowed     = 0;
        $count_group_denied     = 0;
        $count_group_allowed    = 0;
        
        $res_user_denied    = $this->zQuery($sql_user_acl,array($section_identifier,$user_id,0));
        foreach($res_user_denied as $row){
            $count_user_denied  = $row['count'];
        }
        
        $res_user_allowed   = $this->zQuery($sql_user_acl,array($section_identifier,$user_id,1));
        foreach($res_user_allowed as $row){
            $count_user_allowed  = $row['count'];
        }
        
        $res_group_denied   = $this->zQuery($sql_group_acl,array($section_identifier,$groups_str,0));
        foreach($res_group_denied as $row){
            $count_group_denied  = $row['count'];
        }
        
        $res_group_allowed  = $this->zQuery($sql_group_acl,array($section_identifier,$groups_str,1));
        foreach($res_group_allowed as $row){
            $count_group_allowed  = $row['count'];
        }

        if($count_user_denied > 0)
            return false;
        elseif($count_user_allowed > 0)
            return true;
        elseif($count_group_denied > 0)
            return false;
        elseif($count_group_allowed > 0)
            return true;
        else
            return false;
    }
    
}
