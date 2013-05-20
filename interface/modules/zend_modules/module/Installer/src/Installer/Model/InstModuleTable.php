<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Jacob T.Paul <jacob@zhservices.com>
//           Shalini Balakrishnan  <shalini@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Installer\Model;

use Zend\Db\TableGateway\TableGateway;

class InstModuleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }
	
    /**
     * Get the list of modules as per the the params passed
     * @param string 	$state 	1/0	Installation status
     * @param int 		$limit	Limit
     * @param int 		$offset Offset
     * @return boolean|Ambigous <boolean, multitype:>
     */
    public function fetchAll($state="0", $limit="unlimited", $offset="0"){
    	$all = array();
    	$stateMod = "";
    	if($state != "")
    		$stateMod = " where mod_active like \"$state\"";
    	$sql = "select * from modules $stateMod  order by mod_ui_order asc";
    	if ($limit != "unlimited")
    		$sql .= " limit $limit, $offset";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
    			$mod = new InstModule();
    			$mod -> exchangeArray($m);
        		array_push($all,$mod);
    		}
    	}
    	
    	return $all;   	
        
    }
    
    /**
     * this will be used to register a module 
     * @param unknown_type $directory
     * @param unknown_type $rel_path
     * @param unknown_type $state
     * @param unknown_type $base
     * @return boolean
     */
    public function register($directory,$rel_path,$state=0, $base = "custom_modules" ){
    	$check = sqlQuery("select mod_active from modules where mod_directory=?",array($directory));
		if ($check == false)
    	{
    		$added = "";
    		$typeSet = "";
    		if($base != "custom_modules"){
    			$added = "module/";
    			$typeSet = "type=1,";
    		}
    		$lines = @file($GLOBALS['srcdir']."/../interface/modules/$base/$added$directory/info.txt");
    		if ($lines){
    			$name = $lines[0];
    		}else
    			$name = $directory;
    		$uiname = ucwords(strtolower($directory));
    		$moduleInsertId = sqlInsert("INSERT INTO modules SET
    				mod_name=?,
    				mod_active=?,
    				mod_ui_name=?,
    				mod_relative_link=?,".$typeSet."
            mod_directory=?,
            date=NOW()",array($name,$state,$uiname,strtolower($rel_path),mysql_escape_string($directory)));
		 
      if(file_exists($GLOBALS['srcdir']."/../interface/modules/$base/$added$directory/moduleSettings.php")){
          $ModuleObject = 'modules_'.strtolower($directory);
          $ModuleObjectTitle = 'Module '.ucwords($directory);
          global $MODULESETTINGS;
          include_once($GLOBALS['srcdir']."/../interface/modules/$base/$added$directory/moduleSettings.php");
          foreach($MODULESETTINGS as $Settings=>$SettingsArray){
            if($Settings=='ACL')
            $SettingsVal =1;
            elseif($Settings=='preferences')
            $SettingsVal =2;
            else
            $SettingsVal =3;
            $i = 0;
            foreach($SettingsArray as $k=>$v){
                if($SettingsVal==1){
                  ob_start();
                  if($i==0)
                  addObjectSectionAcl($ModuleObject, $ModuleObjectTitle);
                  addObjectAcl($ModuleObject, $ModuleObjectTitle, $k, $v['menu_name']);
                  ob_clean();
                  $i++;
                }
                sqlStatement("INSERT INTO modules_settings VALUES (?,?,?,?,?)",array($moduleInsertId,$SettingsVal,$k,$v['menu_name'],$v['path']));
            }
          }
      }
      return $moduleInsertId;
    	}
    	return false;
    	
    }
    
    /**
     * get the list of all modules
     * @return multitype:
     */
    public function allModules(){
    	$all = array();
    	$sql = "SELECT * FROM modules ORDER BY MOD_UI_ORDER ASC";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
    			$mod = new InstModule();
    			$mod -> exchangeArray($m);
    			array_push($all,$mod);
    		}
    	}
    	return $all;
    	
    }
    
    
    /**
     * @param int $id
     * @param string $cols
     * @return Ambigous <boolean, unknown>
     */
    function getRegistryEntry ( $id, $cols = "*" )
    {
    	$sql = "SELECT $cols FROM modules WHERE MOD_ID=?";    	
    	$rslt =  sqlQuery($sql,array($id));
    	$mod = new InstModule();
    	$mod -> exchangeArray($rslt);   
    	
    	return $mod;
    }
	
    
    /**
     * Function to enable/disable a module
     * @param int 		$id		Module PK
     * @param string 	$mod	Status
     */
    function updateRegistered ( $id, $mod )
    {        	
    	return sqlInsert("UPDATE modules SET $mod,date=NOW() WHERE mod_id=?",array($id));    
    }
    
    /**
     * Function to get ACL objects for module
     * @param int 		$mod_id		Module PK
     */
    public function getSettings($type,$mod_id){
      if($type=='ACL')
      $type = 1;
      elseif($type=='Hooks')
      $type = 3;
      else
      $type = 2;
      $all = array();
    	$sql = "SELECT ms.*,mod_directory FROM modules_settings AS ms LEFT OUTER JOIN modules AS m ON ms.mod_id=m.mod_id WHERE m.mod_id=? AND fld_type=?";
    	$res = sqlStatement($sql,array($mod_id,$type));
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $mod = new InstModule();
		    $mod -> exchangeArray($m);
		    array_push($all,$mod);
    		}
    	}
    	return $all;
    }
    /**
     * Function to get Oemr User Group
     */
    public function getOemrUserGroup(){
      $all = array();
    	$sql = "SELECT * FROM gacl_aro_groups AS gag LEFT OUTER JOIN gacl_groups_aro_map AS ggam ON gag.id=ggam.group_id
      WHERE parent_id<>0 AND group_id IS NOT NULL GROUP BY id ";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $mod = new InstModule();
		    $mod -> exchangeArray($m);
		    array_push($all,$mod);
    		}
    	}
    	return $all;
    }
    /**
     * Function to get Oemr User Group and Aro Map
     */
    public function getOemrUserGroupAroMap(){
      $all = array();
    	$sql = "SELECT group_id,u.id AS id,CONCAT_WS(' ',CONCAT_WS(',',u.lname,u.fname),u.mname) AS user,u.username FROM gacl_aro_groups gag
      LEFT OUTER JOIN gacl_groups_aro_map AS ggam ON gag.id=ggam.group_id LEFT OUTER JOIN gacl_aro AS ga ON ggam.aro_id=ga.id
      LEFT OUTER JOIN users AS u ON u.username=ga.value WHERE group_id IS NOT NULL ORDER BY gag.id";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $all[$m['group_id']][$m['id']] = $m['user'];
    		}
    	}
    	return $all;
    }
    /**
     * Function to get Active Users
     */
    public function getActiveUsers(){
      $all = array();
    	$sql = "SELECT id,username,CONCAT_WS(' ',fname,mname,lname) AS USER FROM users WHERE active=1 AND username IS NOT NULL AND username<>''";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $all[$m['username']] = $m['USER'];
    		}
    	}
    	return $all;
    }
    public function getTabSettings($mod_id){
      $all = array();
    	$sql = "SELECT fld_type,COUNT(*) AS cnt  FROM modules_settings WHERE mod_id=? GROUP BY fld_type ORDER BY fld_type ";
    	$res = sqlStatement($sql,array($mod_id));
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $all[$m['fld_type']] = $m['cnt'];
    		}
    	}
    	return $all;
    }
    /**
     *Function To Get Active ACL for this Module
     */
    public function getActiveACL($mod_id){
      $arr = array();
      $Section = sqlQuery("SELECT mod_directory FROM modules WHERE mod_id=?",array($mod_id));
      $aco = "modules_".$Section['mod_directory'];
      $MapRes = sqlStatement("SELECT * FROM gacl_aco_map WHERE section_value=?",array($aco));
      while($MapRow = sqlFetchArray($MapRes)){
        $aroRes = sqlStatement("SELECT acl_id,value,CONCAT_WS(' ',fname,mname,lname) AS user FROM gacl_aro_map LEFT OUTER JOIN users ON
                               value=username WHERE active=1 AND acl_id=?",array($MapRow['acl_id']));
        $i=0;
        while($aroRow = sqlFetchArray($aroRes)){
          $arr[$MapRow['value']][$i]['acl_id']  = $aroRow['acl_id'];
          $arr[$MapRow['value']][$i]['value']   = $aroRow['value'];
          $arr[$MapRow['value']][$i]['user']    = $aroRow['user'];
          $i++;
        }
      }
      return $arr;
    }
    /**
     *Function To Get Saved Hooks For this Module
     */
    public function getActiveHooks($mod_id){
      $all = array();
      $HooksRes = sqlStatement("SELECT msh.*,ms.menu_name FROM modules_hooks_settings AS msh LEFT OUTER JOIN modules_settings AS ms ON
                               obj_name=enabled_hooks AND ms.mod_id=msh.mod_id LEFT OUTER JOIN modules AS m ON msh.mod_id=m.mod_id 
                               WHERE fld_type=3 AND mod_active=1 AND msh.mod_id=?",array($mod_id));
      while($HooksRow = sqlFetchArray($HooksRes)){
        $mod = new InstModule();
		    $mod -> exchangeArray($HooksRow);
		    array_push($all,$mod);        
      }
      return $all;
    }
    /**
     *Function to get module data
     */
    public function getModulesRow($mod_id){
      $all = array();
      $ModulesRowRes = sqlStatement("SELECT * FROM modules WHERE mod_id=?",array($mod_id));
      while($ModulesRowRow = sqlFetchArray($ModulesRowRes)){
        $mod = new InstModule();
		    $mod -> exchangeArray($ModulesRowRow);
		    array_push($all,$mod);        
      }
      return $all;
    }
    /**
     *Function for saving module settings
     */
    public function SaveSettings($post){
      sqlStatement("UPDATE modules SET mod_nick_name=? WHERE mod_id=?",array($post['nickname'],$post['mod_id']));
    }
    /**
     * Function to Save Configurations
     */
    public function SaveConfigurations($post){
      foreach($post as $aco=>$acoArray){
        $Arr = explode("_-_-_",$aco);
        $acoSection = $Arr[0];
        $acoValue = $Arr[1];
        foreach($acoArray as $aroKey=>$aro){
        $ACLARR = sqlQuery("SELECT acl_id FROM gacl_aco_map WHERE section_value=? AND value=?",array($acoSection,$acoValue));
        if($ACLARR['acl_id']){
          $aclSeq = $ACLARR['acl_id'];
        }
        else{
          sqlStatement("UPDATE gacl_acl_seq SET id=LAST_INSERT_ID(id+1)");
          $aclSeqArr = sqlQuery("SELECT id FROM gacl_acl_seq");
          $aclSeq = $aclSeqArr['id'];
          sqlStatement("INSERT INTO gacl_acl (id,section_value,allow,enabled,return_value,note)
                 VALUES(?,?,1,1,?,?)",array($aclSeq,'user','',''));
          sqlStatement("INSERT INTO gacl_aco_map (acl_id,section_value,value) VALUES (?,?,?)",array($aclSeq,$acoSection,$acoValue));
        }
        sqlStatement("INSERT INTO gacl_aro_map (acl_id,section_value,value) VALUES (?,?,?)",array($aclSeq,'users',$aro));
        }
      }
    }
    /**
     * Function to Save Hooks
     */
    public function SaveHooks($post){
      sqlStatement("INSERT INTO modules_hooks_settings (mod_id,enabled_hooks,attached_to) VALUES(?,?,?)",
                   array($post['mod_id'],$post['Hooks'],$post['AttachedTo']));
    }
    /**
     * Function to Delete ACL
     */
    public function DeleteAcl($post){
      if($post['aclID'] && $post['user']){
        sqlStatement("DELETE FROM gacl_aro_map WHERE acl_id=? AND value=?",array($post['aclID'],$post['user']));
      }
    }
    /**
     * Function to Delete Hooks
     */
    public function DeleteHooks($post){
      if($post['hooksID']){
        sqlStatement("DELETE FROM modules_hooks_settings WHERE id=?",array($post['hooksID']));
      }
    }
    
  public function tableExists($tblname) {
    $row = sqlQuery("SHOW TABLES LIKE '$tblname'");
    if (empty($row)) return false;
    return true;
  }
  
  public function columnExists($tblname, $colname) {
    $row = sqlQuery("SHOW COLUMNS FROM $tblname LIKE '$colname'");
    if (empty($row)) return false;
    return true;
  }
  
  public function columnHasType($tblname, $colname, $coltype) {
    $row = sqlQuery("SHOW COLUMNS FROM $tblname LIKE '$colname'");
    if (empty($row)) return true;
    return (strcasecmp($row['Type'], $coltype) == 0);
  }
  
  public function tableHasRow($tblname, $colname, $value) {
    $row = sqlQuery("SELECT COUNT(*) AS count FROM $tblname WHERE " .
      "$colname LIKE '$value'");
    return $row['count'] ? true : false;
  }
  
  public function tableHasRow2D($tblname, $colname, $value, $colname2, $value2) {
    $row = sqlQuery("SELECT COUNT(*) AS count FROM $tblname WHERE " .
      "$colname LIKE '$value' AND $colname2 LIKE '$value2'");
    return $row['count'] ? true : false;
  }
  
  public function tableHasRow3D($tblname, $colname, $value, $colname2, $value2, $colname3, $value3) {
    $row = sqlQuery("SELECT COUNT(*) AS count FROM $tblname WHERE " .
      "$colname LIKE '$value' AND $colname2 LIKE '$value2' AND $colname3 LIKE '$value3'");
    return $row['count'] ? true : false;
  }
  
  public function tableHasRow4D($tblname, $colname, $value, $colname2, $value2, $colname3, $value3, $colname4, $value4) {
    $row = sqlQuery("SELECT COUNT(*) AS count FROM $tblname WHERE " .
      "$colname LIKE '$value' AND $colname2 LIKE '$value2' AND $colname3 LIKE '$value3' AND $colname4 LIKE '$value4'");
    return $row['count'] ? true : false;
  }
  
  public function tableHasIndex($tblname, $colname) {
    $row = sqlQuery("SHOW INDEX FROM `$tblname` WHERE `Key_name` = '$colname'");
    return (empty($row)) ? false : true;
  }

  public function upgradeFromSqlFile($filename) {
    $fd = fopen($filename, 'r');
    $query = "";
    $line = "";
    $skipping = false;
    $i=0;
    if($fd)
    while (!feof ($fd)){
      $line = fgets($fd, 2048);
      $line = rtrim($line);
  
      if (preg_match('/^\s*--/', $line)) continue;
      if ($line == "") continue;
  
      if (preg_match('/^#IfNotTable\s+(\S+)/', $line, $matches)) {
        $skipping = $this->tableExists($matches[1]);
      }
      else if (preg_match('/^#IfTable\s+(\S+)/', $line, $matches)) {
        $skipping = ! $this->tableExists($matches[1]);
      }
      else if (preg_match('/^#IfMissingColumn\s+(\S+)\s+(\S+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->columnExists($matches[1], $matches[2]);
        }
        else {
          // If no such table then the column is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotColumnType\s+(\S+)\s+(\S+)\s+(\S+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->columnHasType($matches[1], $matches[2], $matches[3]);
        }
        else {
          // If no such table then the column type is deemed not "missing".
          $skipping = true;
        }
      }
    else if (preg_match('/^#IfNotIndex\s+(\S+)\s+(\S+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->tableHasIndex($matches[1], $matches[2]);
        }
        else {
          // If no such table then the index is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotRow\s+(\S+)\s+(\S+)\s+(.+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->tableHasRow($matches[1], $matches[2], $matches[3]);
        }
        else {
          // If no such table then the row is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotRow2D\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->tableHasRow2D($matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
        }
        else {
          // If no such table then the row is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotRow3D\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->tableHasRow3D($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7]);
        }
        else {
          // If no such table then the row is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotRow4D\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          $skipping = $this->tableHasRow4D($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7], $matches[8], $matches[9]);
        }
        else {
          // If no such table then the row is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#IfNotRow2Dx2\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)/', $line, $matches)) {
        if ($this->tableExists($matches[1])) {
          // If either check exist, then will skip
          $firstCheck = $this->tableHasRow2D($matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
          $secondCheck = $this->tableHasRow2D($matches[1], $matches[2], $matches[3], $matches[6], $matches[7]);
          if ($firstCheck || $secondCheck) {
            $skipping = true;   
          }
          else {
                  $skipping = false;
          }
        }
        else {
          // If no such table then the row is deemed not "missing".
          $skipping = true;
        }
      }
      else if (preg_match('/^#EndIf/', $line)) {
        $skipping = false;
      }
  
      if (preg_match('/^\s*#/', $line)) continue;
      if ($skipping) continue;
  
      $query = $query . $line;
      if (substr($query, -1) == ';') {
        $query = rtrim($query, ';');
        if (!sqlStatement($query)) {
          $i++;
        }
        $query = '';
      }
    }
    if($i==0)
    return true;
    else
    return false;
  } 
    
}
?>
