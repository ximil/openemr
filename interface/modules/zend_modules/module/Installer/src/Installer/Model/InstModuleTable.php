<?php
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
    		return sqlInsert("insert into modules set
    				mod_name='$name',
    				mod_active='$state',
    				mod_ui_name= '$uiname',
    				mod_relative_link= '" . strtolower($rel_path) . "',".$typeSet."
				mod_directory='".mysql_escape_string($directory)."',
				date=NOW()
				");
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
    
   
}
?>
