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
    	$check = sqlQuery("select mod_active from modules where mod_directory='$directory'");
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
    		$moduleInsertId = sqlInsert("insert into modules set
    				mod_name='$name',
    				mod_active='$state',
    				mod_ui_name= '$uiname',
    				mod_relative_link= '" . strtolower($rel_path) . "',".$typeSet."
				mod_directory='".mysql_escape_string($directory)."',
				date=NOW()
				");
		 
		if($GLOBALS['srcdir']."/../interface/modules/$base/$added$directory/moduleSettings.php"){
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
				if($i==0)
				addObjectSectionAcl($ModuleObject, $ModuleObjectTitle);
				addObjectAcl($ModuleObject, $ModuleObjectTitle, $k, $v);
				$i++;
			    }
			    sqlStatement("INSERT INTO modules_settings VALUES (?,?,?,?)",array($moduleInsertId,$SettingsVal,$k,$v));
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
    	$sql = "select * from modules order by mod_ui_order asc";
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
    	$sql = "select $cols from modules where mod_id=?";    	
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
    	return sqlInsert("update modules set $mod,date=NOW() where mod_id=?",array($id));    
    }
    
    /**
     * Function to get ACL objects for module
     * @param int 		$mod_id		Module PK
     */
    public function getACL($mod_id){
	$all = array();
    	$sql = "SELECT * FROM modules_settings WHERE mod_id=?";
    	$res = sqlStatement($sql,array($mod_id));
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
    	$sql = "SELECT id,CONCAT_WS(' ',fname,mname,lname) AS USER FROM users WHERE active=1";
    	$res = sqlStatement($sql);
    	if($res){
    		while($m = sqlFetchArray($res)){
		    $all[$m['id']] = $m['USER'];
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
}
?>
