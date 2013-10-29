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
use Zend\Config\Reader\Ini;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use \Application\Model\ApplicationTable;

class InstModuleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
	$adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
        $this->adapter              = $adapter;
        $this->resultSetPrototype   = new ResultSet();
        $this->application          = new ApplicationTable;
    }
    
    /**
     * Get All Modules
     * 
     * @return type
     */
    /*public function getModules()
    {
        $sql    = "SELECT * FROM modules ORDER BY mod_ui_order ASC";
        $params = array();
        $obj    = new ApplicationTable;
        $result = $obj->sqlQuery($sql, $params);
        return $result;
    }*/
	
    /**
     * Get the list of modules as per the the params passed
     * @param string 	$state 	1/0	Installation status
     * @param int 		$limit	Limit
     * @param int 		$offset Offset
     * @return boolean|Ambigous <boolean, multitype:>
     */
    public function fetchAll($state="0", $limit="unlimited", $offset="0"){
    	$all 		= array();
    	$stateMod 	= "";
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
    public function register($directory,$rel_path,$state=0, $base = "custom_modules" )
    {
    	/*$check = sqlQuery("select mod_active from modules where mod_directory='$directory'");*/
        $sql = "SELECT mod_active FROM modules WHERE mod_directory = ?";
        $params = array(
                   $directory,
                );
        $check = $this->application->sqlQuery($sql, $params);

        if ($check->count() == 0) {
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

                $sql = "INSERT INTO modules SET mod_name = ?,
                                                mod_active = ?, 
                                                mod_ui_name = ?, 
                                                mod_relative_link = ?,
                                                $typeSet 
                                                mod_directory = ?, 
                                                date=NOW()
                                                ";
                $params = array(
                   $name,
                   $state,
                   $uiname,
                   strtolower($rel_path),
                   mysql_escape_string($directory),
                );
                
                $result = $this->application->sqlQuery($sql, $params);
                $moduleInsertId = $result->getGeneratedValue();

    		/*$moduleInsertId = sqlInsert("insert into modules set
    				mod_name='$name',
    				mod_active='$state',
    				mod_ui_name= '$uiname',
    				mod_relative_link= '" . strtolower($rel_path) . "',".$typeSet."
				mod_directory='".mysql_escape_string($directory)."',
				date=NOW()
				");*/
		 
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
                                    if($i==0)
                                            addObjectSectionAcl($ModuleObject, $ModuleObjectTitle);
                                    addObjectAcl($ModuleObject, $ModuleObjectTitle, $k, $v['menu_name']);
                                    $i++;
                            }
                            /*sqlStatement("INSERT INTO modules_settings VALUES (?,?,?,?,?)",array($moduleInsertId,$SettingsVal,$k,$v['menu_name'],$v['[path']));*/
                            $sql = "INSERT INTO modules_settings VALUES (?,?,?,?,?)";
                            $params = array($moduleInsertId,$SettingsVal,$k,$v['menu_name'],$v['[path']);
                            $result = $this->application->sqlQuery($sql, $params);
                        }
                    }
                }
                /*sqlStatement("INSERT INTO module_acl_sections VALUES (?,?,?,?)",array($moduleInsertId,$name,0,strtolower($directory)));*/
                $sql = "INSERT INTO module_acl_sections VALUES (?,?,?,?)";
                $params = array($moduleInsertId,$name,0,strtolower($directory));
                $result = $this->application->sqlQuery($sql, $params);
                return $moduleInsertId;
    	}
    	return false;
    	
    }
    
    /**
     * get the list of all modules
     * @return multitype:
     */
    public function allModules(){
    	$sql    = "SELECT * FROM modules ORDER BY mod_ui_order ASC";
        $params = array();
        $result = $this->application->sqlQuery($sql, $params);
        return $result;
    	
    }
    
    /**
     * get the list of all modules
     * @return multitype:
     */
    public function getInstalledModules(){
    	$all = array();
	
	$adapter 	= $this->adapter;
        $sql 		= new Sql($adapter);
       
        $select = $sql->select();
	$where	= array('mod_active' => "1");
        $select->from("modules")
		->where($where)
		->order("mod_ui_order asc");
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
        $obj = new ApplicationTable;
        $obj->log($parameter);

        $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	
	if(count($resArr) > 0){
	    foreach($resArr as $res)
	    {
		$mod = new InstModule();
		$mod -> exchangeArray($res);
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
    function getRegistryEntry ( $id, $cols = "" )
    {
    	$adapter 	= $this->adapter;
        $sql 		= new Sql($adapter);
	
	if($cols <> ""){
	    $colsArr	= explode(",",$cols);
	}
       
        $select = $sql->select();
	$where	= array('mod_id' => $id);
        $select->from("modules")
		->columns($colsArr)
		->where($where);
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
        $obj = new ApplicationTable;
        $obj->log($parameter);

        $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	$rslt 		= $resArr[0];
    	
    	$mod = new InstModule();
    	$mod -> exchangeArray($rslt);   
    	
	return $mod;
    }
	
    /**
     * Function to enable/disable a module
     * @param int 		$id		Module PK
     * @param string 	$mod	Status
     */
    function updateRegistered ( $id, $mod ) {
        if($mod == "mod_active=1"){
            $resp = $this->checkDependencyOnEnable($id); 
            if($resp['status'] == 'success' && $resp['code'] == '1') {
                $sql = "UPDATE modules SET mod_active = ?, 
                                            date = ? 
                                       WHERE mod_id = ?";
                $params = array(
                            1,
                            date('Y-m-d H:i:s'),
                            $id,
                         );
                $results   = $this->application->sqlQuery($sql, $params);
                
                /*$adapter = $this->adapter;
                $sql = new Sql($adapter);
                $update = $sql->update("modules");
                $fields	= array(
                                'mod_active' => "1",
                                'date' => date('Y-m-d H:i:s'));
                $where	= array('mod_id' => $id);
                $update->set($fields);
                $update->where($where);
                $selectString = $sql->getSqlStringForSqlObject($update);
                //LOGGING QUERIES
                $parameter 	= array(
                                                'query' 	=> $selectString,
                                                'type'    	=> 1, // 1- for log to table ; 0 - for log file
                                 );
                $obj = new ApplicationTable;
                $obj->log($parameter);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);*/
            }
        }
        else if($mod == "mod_active=0"){
            $resp	= $this->checkDependencyOnDisable($id);	    
            if($resp['status'] == 'success' && $resp['code'] == '1') {
                $sql = "UPDATE modules SET mod_active = ?, 
                                            date = ? 
                                       WHERE mod_id = ?";
                $params = array(
                   0,
                   date('Y-m-d H:i:s'),
                   $id,
                );
                $results   = $this->application->sqlQuery($sql, $params);
                /*$adapter = $this->adapter;
                $sql = new Sql($adapter);
                $update = $sql->update("modules");
                $fields	= array(
                                'mod_active' => "0",
                                'date' => date('Y-m-d H:i:s'));
                $where	= array('mod_id' => $id);
                $update->set($fields);
                $update->where($where);
                $selectString = $sql->getSqlStringForSqlObject($update);
                //LOGGING QUERIES
                $parameter 	= array(
                                                'query' 	=> $selectString,
                                                'type'    	=> 1, // 1- for log to table ; 0 - for log file
                                 );
                $obj = new ApplicationTable;
                $obj->log($parameter);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);*/
            }	 
        }
        else{
            /*$resp = sqlInsert("update modules set $mod,date=NOW() where mod_id=?",array($id));*/
            $sql = "UPDATE modules SET $mod, 
                                            date=NOW() 
                                       WHERE mod_id = ?";
                $params = array(
                   $id,
                );
                $resp   = $this->application->sqlQuery($sql, $params);
        }
	return $resp;
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
     * Function to Save Configurations
     */
    public function SaveConfigurations($post){
	foreach($post as $aco=>$acoArray){
	    $Arr 	= explode("_-_-_",$aco);
	    $acoSection = $Arr[0];
	    $acoValue 	= $Arr[1];
	    foreach($acoArray as $aroKey=>$aro){
		$ACLARR = sqlQuery("SELECT acl_id FROM gacl_aco_map WHERE section_value=? AND value=?",array($acoSection,$acoValue));
		if($ACLARR['acl_id']){
		    $aclSeq = $ACLARR['acl_id'];
		}
		else{
		    sqlStatement("UPDATE gacl_acl_seq SET id=LAST_INSERT_ID(id+1)");
		    $aclSeqArr 	= sqlQuery("SELECT id FROM gacl_acl_seq");
		    $aclSeq 	= $aclSeqArr['id'];
		    sqlStatement("INSERT INTO gacl_acl (id,section_value,allow,enabled,return_value,note)
				    VALUES(?,?,1,1,?,?)",array($aclSeq,'user','',''));
		    sqlStatement("INSERT INTO gacl_aco_map (acl_id,section_value,value) VALUES (?,?,?)",array($aclSeq,$acoSection,$acoValue));
		}
		sqlStatement("INSERT INTO gacl_aro_map (acl_id,section_value,value) VALUES (?,?,?)",array($aclSeq,'users',$aro));
	    }
	}
    }
        
      
    /**
     * Function to get Status of a Hook
     */
    public function getHookStatus($modId,$hookId,$hangerId){
      if($modId && $hookId && $hangerId){
        /*$modArr	= sqlQuery("SELECT * FROM modules_hooks_settings WHERE mod_id=? AND enabled_hooks = ? AND attached_to = ? ",
			   array($modId,$hookId,$hangerId));*/
	$adapter 	= $this->adapter;
        $sql 		= new Sql($adapter);
       
        $select = $sql->select();
	$where	= array('mod_id' => $modId, 'enabled_hooks' => $hookId, 'attached_to' => $hangerId );
        $select->from("modules_hooks_settings")
		->columns(array('mod_id'))
		->where($where);
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
        $obj = new ApplicationTable;
        $obj->log($parameter);

        $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	
	$modArr		= $resArr[0];
	
	if($modArr['mod_id'] <> ""){
	    return "1";
	}
	else{
	    return "0";
	}
      }
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
	    //sqlStatement("DELETE FROM modules_hooks_settings WHERE id=?",array($post['hooksID']));
	    $adapter 	= $this->adapter;
	    $sql 	= new Sql($adapter); 
	
	    $where	= array('id' => $post['hooksID']);
	    $delete 	= $sql->delete();
	    $delete->from("modules_hooks_settings");	    
	    $delete->where($where);
	    
	    $selectString 	= $sql->getSqlStringForSqlObject($delete);
	    //LOGGING QUERIES
	    $parameter 	= array(
				'query' 	=> $selectString,
				'type'    	=> 1, // 1- for log to table ; 0 - for log file
			     );
	    $obj = new ApplicationTable;
	    $obj->log($parameter);
    
	    $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	    
	}
    }
    
    public function checkDependencyOnEnable($mod_id)
    {
	$retArray	= array();

	$reader 	= new Ini();
	
	$adapter 	= $this->adapter;
        $sql 		= new Sql($adapter);
       
        $select = $sql->select();
	$where	= array('mod_id' => $mod_id);
        $select->from("modules")
		->columns(array('mod_directory'))
		->where($where);
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
        $obj = new ApplicationTable;
        $obj->log($parameter);

        $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	
	$modArr		= $resArr[0];
	
	if($modArr['mod_directory']){			
	    $configFile	= $GLOBALS['fileroot']."/interface/modules/zend_modules/module/".$modArr['mod_directory']."/config";	
	    if(file_exists($configFile.'/config.ini')){
	    
		$data   	= $reader->fromFile($configFile.'/config.ini');
		$depModules	= explode(",",$data['dependency']['modules']);  
		
		$requiredModules	= array();
		if(count($depModules) > 0){
		    foreach($depModules as $depModule){
			if($depModule <> ""){
			    $select 	= $sql->select();
			    $where	= array('mod_directory' => $depModule);
			    $select->from("modules")
				    ->columns(array('mod_active'))
				    ->where($where);
			    
			    $selectString 	= $sql->getSqlStringForSqlObject($select);
			    //LOGGING QUERIES
			    $parameter 	= array(
						'query' 	=> $selectString,
						'type'    	=> 1, // 1- for log to table ; 0 - for log file
					     );
			    $obj = new ApplicationTable;
			    $obj->log($parameter);
		    
			    $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
			    
			    $resultSet 	= new ResultSet();
			    $resultSet->initialize($results);	
			    $resArr		= $resultSet->toArray();
			    $check		= $resArr[0];
			    
			    if($check['mod_active'] <> "1"){
				$requiredModules[]	= $depModule;
			    }	
			}						
		    }			
		}
		
		if(count($requiredModules) > 0){
		    $retArray['status']	= "failure";
		    $retArray['code']	= "200";
		    $retArray['value']	= $requiredModules;
		}
		else{
		    $retArray['status']	= "success";
		    $retArray['code']	= "1";
		    $retArray['value']	= "";
		}
	    }
	    else{
		$retArray['status']	= "success";
		$retArray['code']	= "1";
		$retArray['value']	= "";
	    }
	}
	else{
	    $retArray['status']	= "failure";
	    $retArray['code']	= "400";
	    $retArray['value']	= "Module Directory not found";
	}
	return $retArray;
    }
    
    
    public function checkDependencyOnDisable($mod_id)
    {
	$retArray	= array();
	$depFlag	= "0";
	
	$modArray	= $this->getInstalledModules();
	
	//GET MODULE DIRECTORY OF DISABLING MODULE
	$modDirectory	= $this->getModuleDirectory($mod_id);
	
	$usedModArr	= array();
	if(count($modArray) > 0){
	    //LOOP THROUGH INSTALLED MODULES
	    foreach($modArray as $module)
	    {
		if($module->modId <> ""){
		    //GET MODULE DEPENDED MODULES
		    //print_r($module);
		    $InstalledmodDirectory	= $this->getModuleDirectory($module->modId);
		    $depModArr	= $this->getDependencyModulesDir($module->modId);
		    if(count($depModArr) > 0){
			//LOOP THROUGH DEPENDENCY MODULES
			//CHECK IF THE DISABLING MODULE IS BEING DEPENDED BY OTHER INSTALLED MODULES
			foreach($depModArr as $depModule)
			{
			    if($modDirectory == $depModule){
				$depFlag	= "1";
				//break(2);
				$usedModArr[] = $InstalledmodDirectory;
			    }
			}		
		    }
		}
	    }
	}
	if($depFlag == "0"){
	    $retArray['status']	= "success";
	    $retArray['code']	= "1";
	    $retArray['value']	= "";
	}
	else{
	    $usedModArr		= array_unique($usedModArr);
	    $multiple = "";
	    if(count($usedModArr) > 1){
		$multiple	= "s";
	    }
	    $usedModules	= implode(",",$usedModArr);
	    $retArray['status']	= "failure";
	    $retArray['code']	= "200";
	    $retArray['value']	= "Dependency Problem : This module is being used by ".$usedModules." module".$multiple;
	}
	return $retArray;
    }
    
    public function getDependencyModules($mod_id)
    {
	$reader = new Ini();
	
	$modDirname	= $this->getModuleDirectory($mod_id);
	
	if($modDirname <> ""){			
	    $configFile	= $GLOBALS['fileroot']."/interface/modules/zend_modules/module/".$modDirname."/config";	
	    if(file_exists($configFile.'/config.ini')){
	    
		$data   		= $reader->fromFile($configFile.'/config.ini');
		$depModulesStr	= $data['dependency']['modules'];
		$depModulesArr	= explode(",",$data['dependency']['modules']);
		$depModuleStatusArr	= array();
		$ret_str="";
		if(count($depModulesArr)>0){
		    $count = 0;
		    foreach($depModulesArr as $modDir){
			if($count > 0){
			    $ret_str.= ", ";
			}
			$ret_str.= trim($modDir)."(".$this->getModuleStatusByDirectoryName($modDir).")";
			$count++;
		    }			
		}
	       
	    }
	}		
	return $ret_str;		
    }
    
    public function getDependencyModulesDir($mod_id)
    {
	$depModulesArr	= array();
	$reader = new Ini();
	
	$adapter 	= $this->adapter;
	$sql 		= new Sql($adapter);
       
	$select = $sql->select();
	$where	= array('mod_id' => $mod_id);
	$select->from("modules")
		->where($where)
		->columns(array('mod_directory'));
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
	$obj = new ApplicationTable;
	$obj->log($parameter);

	$results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	$modArr		= $resArr[0];
	
	if($modArr['mod_directory']){			
	    $configFile	= $GLOBALS['fileroot']."/interface/modules/zend_modules/module/".$modArr['mod_directory']."/config";	
	    if(file_exists($configFile.'/config.ini')){
	    
		$data   		= $reader->fromFile($configFile.'/config.ini');
		$depModulesStr	= $data['dependency']['modules'];
		$depModulesArr	= explode(",",$data['dependency']['modules']);    
	       
	    }
	}		
	return $depModulesArr;		
    }
    
    public function getModuleStatusByDirectoryName($moduleDir)
    {
	//$check = sqlQuery("select mod_active,mod_directory from modules where mod_directory='".trim($moduleDir)."'");
	
	$adapter 	= $this->adapter;
	$sql 		= new Sql($adapter);
       
	$select = $sql->select();
	$where	= array('mod_directory' => trim($moduleDir));
	$select->from("modules")
		->where($where)
		->columns(array('mod_active', 'mod_directory'));
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
	$obj = new ApplicationTable;
	$obj->log($parameter);

	$results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	$check		= $resArr[0];
	
	if((count($check) > 0)&& is_array($check)){
	    if($check['mod_active'] == "1"){
		return "Enabled";
	    }
	    else{
		return "Disabled";
	    }		
	}
	else{
	    return "Missing";
	}
    }
    
    public function getHangers()
    {
	return array(
			'reports' 	=> "Reports",
			'encounter' => "Encounter",
			'demographics' => "Demographics",
			'combination_forms' => "Combination Forms"
		     );
    }
    
    /*public function getModuleHooksFromIni($mod_id)
    {
	$reader = new Ini();
	$modArr = sqlQuery("SELECT mod_directory FROM modules WHERE mod_id = ? ",array($mod_id));
	if($modArr['mod_directory']){			
	    $configFile	= $GLOBALS['fileroot']."/interface/modules/zend_modules/module/".$modArr['mod_directory'];	
	    if(file_exists($configFile.'/config.ini')){		
		$data   		= $reader->fromFile($configFile.'/config.ini');
		$hooks	= $data['hooks'];		    		   
	    }
	}		
	return $hooks;		
    }*/
    
    public function getModuleDirectory($mod_id)
    {
	$moduleName	= "";
	if($mod_id <> ""){	
	    
	    $adapter 	= $this->adapter;
	    $sql 		= new Sql($adapter);
	   
	    $select = $sql->select();
	    $where	= array('mod_id' => $mod_id);
	    $select->from("modules")
		    ->where($where)
		    ->columns(array('mod_directory'));
	    
	    $selectString 	= $sql->getSqlStringForSqlObject($select);
	    
	    //LOGGING QUERIES
	    $parameter 	= array(
				'query' 	=> $selectString,
				'type'    	=> 1, // 1- for log to table ; 0 - for log file
			     );
	    $obj = new ApplicationTable;
	    $obj->log($parameter);
    
	    $results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	    
	    $resultSet 	= new ResultSet();
	    $resultSet->initialize($results);	
	    $resArr		= $resultSet->toArray();
	    $modArr		= $resArr[0];
	    
	    if($modArr['mod_directory'] <> ""){			
		$moduleName = $modArr['mod_directory'];
	    }		
	    return $moduleName;
	}
    }
    
    public function checkModuleHookExists($mod_id,$hookId)
    {  
	$adapter 	= $this->adapter;
	$sql 		= new Sql($adapter);
       
	$select = $sql->select();
	$where	= array('mod_id' => $mod_id, 'fld_type' => "3", 'obj_name' => $hookId );
	$select->from("modules_settings")
		->where($where)
		->columns(array('obj_name'));
	
	$selectString 	= $sql->getSqlStringForSqlObject($select);
	
	//LOGGING QUERIES
	$parameter 	= array(
			    'query' 	=> $selectString,
			    'type'    	=> 1, // 1- for log to table ; 0 - for log file
			 );
	$obj = new ApplicationTable;
	$obj->log($parameter);

	$results 	= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
	
	$resultSet 	= new ResultSet();
	$resultSet->initialize($results);	
	$resArr		= $resultSet->toArray();
	$modArr		= $resArr[0];
	
	if($modArr['obj_name'] <> ""){
	    return "1";
	}
	else{
	    return "0";
	}
    }
}
?>
