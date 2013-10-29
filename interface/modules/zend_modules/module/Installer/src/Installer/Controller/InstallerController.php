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
namespace Installer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Installer\Model\InstModule;          
use Application\Listener\Listener;

class InstallerController extends AbstractActionController
{
    protected $InstallerTable;
    protected $listenerObject;
    
    public function __construct()
    {
	$this->listenerObject	= new Listener;
    }
    
    public function nolayout()
    {
        // Turn off the layout, i.e. only render the view script.
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function indexAction(){ 
    		
    	//get the list of installed and new modules
	
        $result = $this->getInstallerTable()->allModules();

        $allModules = array();
	foreach($result as $dataArray){
            $mod = new InstModule();
            $mod -> exchangeArray($dataArray);
            array_push($allModules,$mod);
	}

        return new ViewModel(array(
            'InstallersExisting'    => $allModules,
            'InstallersAll'         => $allModules,
            'listenerObject'        => $this->listenerObject,
            'dependencyObject'      => $this->getInstallerTable(),	
        )); 
	
        /*$listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
	
	//GET ALL MODULES	
	$tableName  = "modules";
        $fields     = "*";
        $order	    = "mod_ui_order asc";
	
        $parameter  = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,                       
                        'order'	    => $order,
                     ); 
        $data       = $this->getEventManager()->trigger('selectEvent', $this, $parameter);	
	
	$allModules = array();
	foreach($data as $dataArr)
	{
	    foreach($dataArr as $dataArray)
	    {		
		$mod = new InstModule();
    		$mod -> exchangeArray($dataArray);
    		array_push($allModules,$mod);
	    }
	}
		
 	return new ViewModel(array(
				'InstallersExisting' 	=> $allModules,
				'InstallersAll' 	=> $allModules,
				'listenerObject' 	=> $this->listenerObject,
				//'dependencyObject'	=> $this->getInstallerTable(),	
				)); */		
    }   

    public function getInstallerTable()
    {
        if (!$this->InstallerTable) {
            $sm = $this->getServiceLocator();
            $this -> InstallerTable = $sm -> get('Installer\Model\InstModuleTable');
        }
        return $this->InstallerTable;
    }
    
    public function  registerAction(){
    	$status 	= false;
    	$request 	= $this->getRequest();
    	if ($request->isPost()) {
	    if($request->getPost('mtype') == 'zend'){
		$rel_path = "public/".$request->getPost('mod_name')."/";
		if($this -> getInstallerTable() -> register($request->getPost('mod_name'),$rel_path,0,$GLOBALS['zendModDir'])){
		    //add the Module name in the application config file if not already present
		    $fileName = $GLOBALS['srcdir']."/../".$GLOBALS['baseModuleDir'].$GLOBALS['zendModDir']."/config/application.config.php";
		    $data = include  $fileName;
		    //TODO what if same name is already there for another module
		    $data['modules'] = array_merge($data['modules'],array($request->getPost('mod_name')));
		    //recreate the config file
		    if(is_writable ($fileName)){
			$content = "<?php return array(";
			$content .= $this -> getContent($data);
			$content .= ");";    					
			file_put_contents($fileName, $content);    					
		    }
		    else{
			die("Unable to modify application config. Please give write permission to $fileName");
		    }
		    $status = true;
		}
	    }else{
		$rel_path = $request->getPost('mod_name')."/index.php";
		if($this -> getInstallerTable() -> register($request->getPost('mod_name'),$rel_path)){
		    $status = true;
		}
	    }    	
	    die($status ? "Success" : "Failure");    		
    	}    	
    }
    
    public function manageAction(){
	
	$listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
	
	$request = $this->getRequest();
    	$status  = "Failure";
    	if ($request->isPost()) {
	    if ($request->getPost('modAction') == "enable"){
		$resp	= $this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "mod_active=0" );
		if($resp['status'] == 'failure' && $resp['code'] == '200'){
		    $status = $resp['value'];
		}			
		else{
		    $status = "Success";
		}
	    }
	    elseif ($request->getPost('modAction') == "disable"){
		$resp	= $this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "mod_active=1" );
		if($resp['status'] == 'failure' && $resp['code'] == '200'){
		    $plural = "";
		    if(count($resp['value']) > 1){
			$plural = "s";
		    }
		    $status = "Dependency Problem : ".implode(", ",$resp['value'])." Module".$plural." Should be Enabled";
		}
		else if($resp['status'] == 'failure' && ($resp['code'] == '300' || $resp['code'] == '400')){
		    $status = $resp['value'];
		}
		else{
		    $status = "Success";
		}
	    }
	    elseif ($request->getPost('modAction') == "install"){    
		$dirModule = $this -> getInstallerTable() -> getRegistryEntry ( $request->getPost('modId'), "mod_directory" );
		    $mod_enc_menu = $request->getPost('mod_enc_menu');
		    $mod_nick_name = mysql_real_escape_string($request->getPost('mod_nick_name'));
		if ($this -> installSQL ($GLOBALS['srcdir']."/../".$GLOBALS['baseModuleDir'].$GLOBALS['customDir']."/".$dirModule -> modDirectory)){
		    //$this -> installACL ($GLOBALS['srcdir']."/../".$GLOBALS['baseModuleDir'].$GLOBALS['customDir']."/".$dirModule -> modDirectory);
		    $this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "sql_run=1,mod_nick_name='".$mod_nick_name."',mod_enc_menu='".$mod_enc_menu."'" );
		    $status = "Success";
		}
		else{
		    $status = "ERROR: could not open table.sql, broken form?";
		}			    
	    }
    	}
    	echo $status;
    	exit(0);
    }
    

    /**
     * Function to update the db for any of the new modules that are installed
     * @param 	string 	$dir Location of the sql file
     * @return boolean
     */
    private function installSQL ( $dir )
    {
    	
    	$sqltext = $dir."/table.sql";
    	if ($sqlarray = @file($sqltext))
    	{
	    $sql = implode("", $sqlarray);
	    $sqla = split(";",$sql);
	    foreach ($sqla as $sqlq) {
		if (strlen($sqlq) > 5) {
		    sqlStatement(rtrim("$sqlq"));
		}
	    }		    
	    return true;
    	}
	else
    	    return true;
    }
    
    /**
     * Function to install ACL for the installed modules
     * @param 	string 	$dir Location of the php file which calling functions to add sections,aco etc.
     * @return boolean
     */
    private function installACL ( $dir )
    {    	
    	$aclfile = $dir."/moduleACL.php";
    	if (file_exists($aclfile))
    	{
    	    include_once($aclfile);
    	}
    }
    
    /**
     * Used to recreate the application config file
     * @param unknown_type $data
     * @return string
     */
    private function getContent($data){
    	$string = "";
    	foreach($data as $key => $value){
	    $string .= " '$key' => ";
	    if(is_array($value)){
		$string .= " array(";
		$string .= 		$this -> getContent($value);
		$string .= " )";
	    }
	    else 
		$string .= "'$value'";
	    $string .= ",";
    	}
    	return $string;    
    }
    
    public function SaveConfigurationsAction(){
	$request = $this->getRequest();      
	$this->getInstallerTable()->SaveConfigurations($request->getPost());
	$return[0]  = array('return' => 1,'msg' => $this->listenerObject->zht("Saved Successfully"));
	$arr        = new JsonModel($return);
	return $arr;
    }
    
    public function SaveHooksAction(){
	
	$listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
	
	$request = $this->getRequest();
      
	$postArr	= $request->getPost();
	
	//DELETE HOOKS
	$tableName = "modules_hooks_settings";
        $where     = "mod_id = '".$postArr['mod_id']."'";
        $parameter = array(
			    'tableName' => $tableName,
			    'where'    	=> $where,
			);        
        $this->getEventManager()->trigger('deleteEvent', $this, $parameter);	
	
	if(count($postArr['hook_hanger']) > 0){
	    
	    foreach($postArr['hook_hanger'] as $hookId => $hooks)
	    {
		foreach($hooks as $hangerId => $hookHanger)
		{
		    $insArr			= array();
		    $insArr['mod_id']		= $postArr['mod_id'];
		    $insArr['Hooks']		= $hookId;
		    $insArr['AttachedTo']	= $hangerId;
		    
		    //SAVE HOOK
		    $tableName  = "modules_hooks_settings";
		    $fields     = array(
					    'mod_id'      	=> $postArr['mod_id'],
					    'enabled_hooks'    	=> $hookId,
					    'attached_to'       => $hangerId
					);
		    $parameter 	= array(
					    'tableName' => $tableName,
					    'fields'    => $fields,
					);
		    
		    $this->getEventManager()->trigger('insertEvent', $this, $parameter);
		}
	    }
	    $return[0]  = array('return' => 1,'msg' => $this->listenerObject->zht("Saved Successfully"));
	}
	else{
	    $return[0]  = array('return' => 1,'msg' => $this->listenerObject->zht("No Hangers selected for Hooks"));
	}	
	
	$arr        = new JsonModel($return);
	return $arr;
    }   
   
    
    public function configureAction()
    {	
        $request 	= $this->getRequest();
				$modId		= $request->getPost('mod_id');
        /** Configuration Details */
        $tableName 	= "module_configuration";
        $fields 	= "*";
        $where          = "module_configuration.module_id=" . $request->getPost('mod_id');
        $listener 	= $this->getServiceLocator()->get('Listener');	
        $this->getEventManager()->attachAggregate($listener);
        $parameter 	= array(
			    'tableName' => $tableName,
			    'fields'    => $fields,
			    'where'     => $where,
			 );
        //$result = $this->getEventManager()->trigger('countEvent', $this, $parameter);
         $result 	= $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        $data 		= array();
        $serviceLocator = $this->getServiceLocator();
        $config         = $serviceLocator->get('config');
	
        foreach ($result as $rows => $row) {
            foreach($row as $tmp){
								array_push($data, $tmp);
								array_push($config['moduleconfig'], $tmp);
            }
        }
	
		//INSERT MODULE HOOKS IF NOT EXISTS
		$moduleDirectory	= $this->getInstallerTable()->getModuleDirectory($modId);
		//GET MODULE HOOKS FROM A FUNCTION IN CONFIGURATION MODEL CLASS
		
		$phpObjCode 	=  str_replace('[module_name]', $moduleDirectory, '$obj  = new \[module_name]\Model\Configuration();');
		$className	= str_replace('[module_name]', $moduleDirectory, '\[module_name]\Model\Configuration');
		
		if(class_exists($className)){
				eval($phpObjCode);
		}
		
		$hooksArr	= array();
		if($obj){
				//$obj	= new \Lab\Model\Configuration();
				$hooksArr	= $obj->getHookConfig();
		}
	
		if(count($hooksArr) > 0){
				foreach($hooksArr as $hook){
						if(count($hook) > 0){
								
								if($this->getInstallerTable()->checkModuleHookExists($modId,$hook['name']) == "0"){			
										$tableName  = "modules_settings";
										$fields     = array(
																		'mod_id' 	=> $modId,
																		'fld_type' 	=> "3",
																		'obj_name' 	=> $hook['name'],
																		'menu_name' => $hook['title'],
																		'path' 	=> $hook['path']
																);
										$parameter 	= array(
																		'tableName' => $tableName,
																		'fields'    => $fields,
																);
										$this->getEventManager()->trigger('insertEvent', $this, $parameter);
								}
						}
				}                     
		}
	else{
	    //DELETE ADDED HOOKS TO HANGERS OF THIS MODULE, IF NO HOOKS EXIST IN THIS MODULE
	    $tableName = "modules_hooks_settings";
	    $where     = "mod_id='".$modId."'";
	    $parameter = array(
				'tableName' 	=> $tableName,
				'where'    	=> $where,
			     );
        
	    $this->getEventManager()->trigger('deleteEvent', $this, $parameter);
	    
	    //DELETE MODULE HOOKS
	    $tableName = "modules_settings";
	    $where     = "mod_id='".$modId."' AND fld_type = '3'";
	    $parameter = array(
				'tableName' 	=> $tableName,
				'where'    	=> $where,
			     );
        
	    $this->getEventManager()->trigger('deleteEvent', $this, $parameter);
		}
		
		$aclArray	= array();
		if($obj){
				//$obj	= new \Lab\Model\Configuration();
				$aclArray	= $obj->getAclConfig();
		}
		
		if(count($aclArray) > 0){
				foreach($aclArray as $acl){
						if(count($acl) > 0){
						}
				}                     
		}
	else{
	}
	
	return new ViewModel(array(

            'mod_id'                	=> $request->getPost('mod_id'),
            'TabSettings'           	=> $this->getInstallerTable()->getTabSettings($request->getPost('mod_id')),
            'ACL'                   	=> $this->getInstallerTable()->getSettings('ACL',$request->getPost('mod_id')),
            'OemrUserGroup'         	=> $this->getInstallerTable()->getOemrUserGroup(),
            'OemrUserGroupAroMap'   	=> $this->getInstallerTable()->getOemrUserGroupAroMap(),
            'ListActiveUsers'       	=> $this->getInstallerTable()->getActiveUsers(),
            'ListActiveACL'         	=> $this->getInstallerTable()->getActiveACL($request->getPost('mod_id')),
            /*'Hooks'               	=> $this->getInstallerTable()->getSettings('Hooks',$request->getPost('mod_id')),*/
            'ListActiveHooks'       	=> $this->getInstallerTable()->getActiveHooks($request->getPost('mod_id')),
            'helperObject'          	=> $this->helperObject,
            'configuration'         	=> $data,
            'hangers'               	=> $this->getInstallerTable()->getHangers(),
            'Hooks'                 	=> $hooksArr,
            'hookObject'            	=> $this->getInstallerTable(),
            'settings'              	=> $obj,
	    'listenerObject' 		=> $this->listenerObject,
	    'listenerObject' 	    => $this->listenerObject,
	 
        ));
	
    }
    
    public function saveConfigAction()
    {   
        $request    = $this->getRequest();
        $moduleId   = $request->getPost()->module_id;
        $listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
        $i = 0;
        foreach ($request->getPost() as $key => $value) {
            $fieldName  = $key;
            $fieldValue = $value;
            if ($fieldName != 'module_id') {
                $tableName  = "module_configuration";
                $fields     = array(
				    'field_name'    => $fieldName,
				    'field_value'   => $fieldValue,
				    'module_id'     => $moduleId,
				);
                /** Check the field exist */
                $where          = "module_id='$moduleId' and field_name='$fieldName'";
                $parameter = array(
                        'tableName' => $tableName,
                        'where'     => $where,
                    );
        
                $data = $this->getEventManager()->trigger('countEvent', $this, $parameter);
                if ($data[0] > 0) { 
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                        'where'     => $where,
                    );
                    $data = $this->getEventManager()->trigger('updateEvent', $this, $parameter);
                }
		else {
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
                }
            }
        }
       
        /** Configuration Details to Global array */
        $tableName 	= "module_configuration";
        $fields 	= "*";
        $where          = "module_configuration.module_id=" . $moduleId;
        $parameter 	= array(
				'tableName' => $tableName,
				'fields'    => $fields,
				'where'     => $where,
			    ); 
	       
        $result 	= $this->getEventManager()->trigger('selectEvent', $this, $parameter);
  
        $serviceLocator = $this->getServiceLocator();
        $config         = $serviceLocator->get('config');
        foreach ($result as $rows => $row) {
            foreach($row as $tmp){
		array_push($config['moduleconfig'], $tmp);               
            }
        }

        /** End Configuration Details to Global array */
        $returnArr 	= array('modeId' => $moduleId);
        $return 	= new JsonModel($returnArr);
    	return $return;  
    }
    
    public function DeleteAclAction(){
	$request = $this->getRequest();
	$this->getInstallerTable()->DeleteAcl($request->getPost());
	$return[0]  = array('return' => 1,'msg' => $this->listenerObject->zht("Deleted Successfully"));
	$arr        = new JsonModel($return);
	return $arr;
    }
    
    public function DeleteHooksAction(){
	$request = $this->getRequest();
	$this->getInstallerTable()->DeleteHooks($request->getPost());
	$return[0]  = array('return' => 1,'msg' => $this->listenerObject->zht("Deleted Successfully"));
	$arr        = new JsonModel($return);
	return $arr;
    }
}
