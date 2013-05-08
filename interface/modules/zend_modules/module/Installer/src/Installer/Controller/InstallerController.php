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
namespace Installer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Installer\Model\InstModule;  
    

class InstallerController extends AbstractActionController
{
    protected $InstallerTable;
    
    public function nolayout(){
      // Turn off the layout, i.e. only render the view script.
      $viewModel = new ViewModel();
      $viewModel->setTerminal(true);
      return $viewModel;
    }
    
    public function indexAction(){    		
    	//get the list of installed and new modules
      return new ViewModel(array(
        'InstallersExisting' => $this -> getInstallerTable() -> fetchAll(""),
        'InstallersAll' => $this -> getInstallerTable() -> allModules(),
      )); 		
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
    	$status = false;
    	$request = $this->getRequest();
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
    				}else{
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
    $request = $this->getRequest();
    	$status  = "Failure";
    	if ($request->isPost()) {
    		if ($request->getPost('modAction') == "enable"){
    			$this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "mod_active=0" );
    			$status = "Success";
    		}
    		elseif ($request->getPost('modAction') == "disable"){
    			$this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "mod_active=1" );
    			$status = "Success";
    		}
    		elseif ($request->getPost('modAction') == "install"){    
    			$dirModule = $this -> getInstallerTable() -> getRegistryEntry ( $request->getPost('modId'), "mod_directory" );
          $mod_enc_menu = $request->getPost('mod_enc_menu');
          $mod_nick_name = mysql_real_escape_string($request->getPost('mod_nick_name'));
          $moddirpath = ($request->getPost('mtype') == 'zend') ? $GLOBALS['zendModDir'] : $GLOBALS['customDir'];
          if ($this -> installSQL ($GLOBALS['srcdir']."/../".$GLOBALS['baseModuleDir'].$moddirpath."/".$dirModule -> modDirectory)){
            $this -> getInstallerTable() -> updateRegistered ( $request->getPost('modId'), "sql_run=1,mod_nick_name='".$mod_nick_name."',mod_enc_menu='".$mod_enc_menu."'" );
            $status = "Success";
          }else{
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
      }
    	return true;
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
    		}else 
    			$string .= "'$value'";
    		$string .= ",";
    	}
    	return $string;    
    }
    
    public function SaveConfigurationsAction(){
      $request = $this->getRequest();
      $this->getInstallerTable()->SaveConfigurations($request->getPost());
      $return[0]  = array('return' => 1,'msg' => xlt("Saved Successfully"));
      $arr        = new JsonModel($return);
      return $arr;
    }
    
    public function SaveHooksAction(){
      $request = $this->getRequest();
      $this->getInstallerTable()->SaveHooks($request->getPost());
      $return[0]  = array('return' => 1,'msg' => xlt("Saved Successfully"));
      $arr        = new JsonModel($return);
      return $arr;
    }
    
    public function SaveSettingsAction(){
      $request = $this->getRequest();
      $this->getInstallerTable()->SaveSettings($request->getPost());
      $return[0]  = array('return' => 1,'msg' => xlt("Saved Successfully"));
      $arr        = new JsonModel($return);
      return $arr;
    }
    
    public function configureAction(){
      $request = $this->getRequest();
      return new ViewModel(array(
          'mod_id' => $request->getPost('mod_id'),
          'TabSettings' => $this->getInstallerTable()->getTabSettings($request->getPost('mod_id')),
          'ACL' => $this->getInstallerTable()->getSettings('ACL',$request->getPost('mod_id')),
          'OemrUserGroup' => $this->getInstallerTable()->getOemrUserGroup(),
          'OemrUserGroupAroMap' => $this->getInstallerTable()->getOemrUserGroupAroMap(),
          'ListActiveUsers' => $this->getInstallerTable()->getActiveUsers(),
          'ListActiveACL' => $this->getInstallerTable()->getActiveACL($request->getPost('mod_id')),
          'Hooks' => $this->getInstallerTable()->getSettings('Hooks',$request->getPost('mod_id')),
          'ListActiveHooks' => $this->getInstallerTable()->getActiveHooks($request->getPost('mod_id')),
          'Modules' => $this->getInstallerTable()->getModulesRow($request->getPost('mod_id')),
      ));
    }
    
    public function DeleteAclAction(){
      $request = $this->getRequest();
      $this->getInstallerTable()->DeleteAcl($request->getPost());
      $return[0]  = array('return' => 1,'msg' => xlt("Deleted Successfully"));
      $arr        = new JsonModel($return);
      return $arr;
    }
    
    public function DeleteHooksAction(){
      $request = $this->getRequest();
      $this->getInstallerTable()->DeleteHooks($request->getPost());
      $return[0]  = array('return' => 1,'msg' => xlt("Deleted Successfully"));
      $arr        = new JsonModel($return);
      return $arr;
    }
}
