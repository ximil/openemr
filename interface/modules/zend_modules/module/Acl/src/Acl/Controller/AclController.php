<?php
namespace Acl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Acl\Events\Events;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Application\Listener\Listener;
use Zend\Db\Sql\Expression;
class AclController extends AbstractActionController
{
    protected $aclTable;
    protected $listenerObject;    
    
    public function __construct()
    {
        $this->listenerObject = new Listener;
    }
    
    /**
     * Example for Model use Application Model
     */
    public function exampleAction()
    {
        $result = $this->getAclTable()->sqlTest();
        foreach ($result as $row) {
            echo '<pre>'; print_r($row); echo '</pre>';
        }
    }
    public function indexAction()
    {
        $listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
        
        $module_id = $this->params()->fromQuery('module_id');
        $tableName 	= "module_acl_sections";
        $fields 	= "*";
        if($module_id !=''){
            $where          = "section_id = ".$module_id." OR parent_section = ".$module_id;
            $parameter  = array(
                'tableName' => $tableName,
                'fields'    => $fields,
                'where'     => $where,
            );
        }
	else{
            $parameter  = array(
                'tableName' => $tableName,
                'fields'    => $fields,
            );
	}

        $result = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        $arrayCategories = array();
        foreach($result as $key_level_1 => $val_level_1) {
            foreach($val_level_1 as $row){
                $arrayCategories[$row['section_id']] = array("parent_id" => $row['parent_section'], "name" =>
                $row['section_name'],"id" => $row['section_id']);
            }
        }
    
        ob_start();                                      
        $this->createTreeView($arrayCategories,0);
        $sections = ob_get_clean();
    
        $user_group_main     = $this->createUserGroups("user_group_","","draggable2");
        $user_group_allowed  = $this->createUserGroups("user_group_allowed_","display:none;","draggable3","class='class_li'");
        $user_group_denied   = $this->createUserGroups("user_group_denied_","display:none;","draggable4","class='class_li'");
        
        $tableName 	= "modules";
        $fields 	= "*";
        $where          = "mod_active = 1";
        $parameter      = array(
                            'tableName' => $tableName,
                            'fields'    => $fields,
                            'where'     => $where,
                          );
        $result = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        $array_active_modules = array();
        foreach($result as $key_level_1 => $val_level_1) {
            foreach($val_level_1 as $row){
                $array_active_modules[$row['mod_id']] = $row['mod_name'];
            }
        }
        
        $index = new ViewModel(array(
	    'user_group_main' 	 => $user_group_main,
            'user_group_allowed' => $user_group_allowed,
            'user_group_denied'  => $user_group_denied,
            'sections'           => $sections,
            'component_id'       => "0-".$module_id,
            'module_id'          => $module_id,
            'listenerObject' 	 => $this->listenerObject,
            'active_modules'     => $array_active_modules,
	));
	return $index;
      
   
    }
    public function acltabAction()
    {
        $module_id = $this->params()->fromQuery('module_id');
        $this->layout('layout/layout_tabs');
        $index = new ViewModel(array(
            'mod_id'          => $module_id,
	));
	return $index;
    }
    public function aclAction()
    {
        $listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
        $module_id = $this->params()->fromQuery('module_id');
        
        /**
         * Fetch Groups From DB
         **/
        
        $tableName 	= "module_acl_groups";
        $fields 	= "*";
        $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                     ); 
        $data = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
        $user_groups = array();
        foreach($data as $val_level_1) {
             foreach($val_level_1 as $row){
                 $user_groups[$row['group_id']] = $row['group_name'];
             }
        }
        
        /**
         * Fetch Componets From DB
         **/
        
        $tableName 	= "module_acl_sections";
        $fields 	= "*";
        $where          = "section_id = ".$module_id." OR parent_section = ".$module_id;
        $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                        'where'     => $where
                     ); 
        $data = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
        $module_data = array();
        $module_data['module_components'] = array();
        foreach($data as $val_level_1) {
            foreach($val_level_1 as $row){
                if($row['parent_section'] == 0){
                    $module_data['module_name'] = array(
                                                    'id'    => $row['section_id'],
                                                    'name'  => $row['section_name']
                                                );
                }else{
                    $module_data['module_components'][$row['section_id']] = $row['section_name'];
                } 
                
            }
        }
        
        /**
         * Fetch Saved ACL
         **/
        
        
        $tableName 	= "module_acl_group_settings";
        $fields 	= "*";
        $where          = "module_id = ".$module_id." AND allowed = 1";
        $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                        'where'     => $where
                     );
        
        $saved_ACL = array();
        $data = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
        foreach($data as $val_level_1) {
            foreach($val_level_1 as $row){
                if(!$saved_ACL[$row['section_id']]) $saved_ACL[$row['section_id']] = array();
                array_push($saved_ACL[$row['section_id']],$row['group_id']);
            }
        }
       
        /**
         * Return View Model
         **/
        $acl_view = new ViewModel(
                                  array(
                                        'user_groups'  => $user_groups,
                                        'listenerObject' => $this->listenerObject,
                                        'module_data'  => $module_data,
                                        'module_id'    => $module_id,
                                        'acl_data'     => $saved_ACL
                                    )
                                );
        return $acl_view;
        
    }
    public function ajaxAction()
    {
        $listener = $this->getServiceLocator()->get('Listener');
        $this->getEventManager()->attachAggregate($listener);
        
        $ajax_mode  = $this->getRequest()->getPost('ajax_mode', null);
        if($ajax_mode == "save_acl"){
            $selected_componet = $this->getRequest()->getPost('selected_module', null);
            $selected_componet_arr = explode("-",$selected_componet);
            if($selected_componet_arr[0] == 0) $selected_componet_arr[0] = $selected_componet_arr[1];
            
            $allowed_users = json_decode($this->getRequest()->getPost('allowed_users', null));
            $denied_users = json_decode($this->getRequest()->getPost('denied_users', null));
            
            $allowed_users = array_unique($allowed_users);
            $denied_users = array_unique($denied_users);
            
            /**
             * Delete Previously Stored ACL Data
             **/
            $tableName = "module_acl_group_settings";
            $where     = "module_id = ".$selected_componet_arr[0]." AND section_id = ".$selected_componet_arr[1];
            $parameter = array(
                'tableName' => $tableName,
                'where'    => $where,
             );
            $data = $this->getEventManager()->trigger('deleteEvent', $this, $parameter);
            
            $tableName = "module_acl_user_settings";
            $where     = "module_id = ".$selected_componet_arr[0]." AND section_id = ".$selected_componet_arr[1];
            $parameter = array(
                'tableName' => $tableName,
                'where'    => $where,
             );
            $data = $this->getEventManager()->trigger('deleteEvent', $this, $parameter);
            

            /**
             * Save Allowed Section
             **/
            foreach($allowed_users as $allowed_user){
               $id = str_replace("li_user_group_allowed_","",$allowed_user);
               $arr_id = explode("-",$id);
               
               if($arr_id[1] == 0){
                    /**
                     * User Groups
                    **/
                    $tableName 	= "module_acl_group_settings";
                    $fields 	= array(
                        'module_id'  => $selected_componet_arr[0],
                        'group_id'   => $arr_id[0],
                        'section_id' =>$selected_componet_arr[1],
                        'allowed'    =>1
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
               }else{
                    /**
                     * User Lists
                    **/
                    $tableName 	= "module_acl_user_settings";
                    $fields 	= array(
                        'module_id'  => $selected_componet_arr[0],
                        'user_id'   => $arr_id[1],
                        'section_id' =>$selected_componet_arr[1],
                        'allowed'    =>1
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
               }
            }
            
            /**
             * Save Denied Section
             **/
            foreach($denied_users as $denied_user){
               echo $denied_user;
               $id = str_replace("li_user_group_denied_","",$denied_user);
               $arr_id = explode("-",$id);
               
               if($arr_id[1] == 0){
                /**
                 * User Groups
                **/
                $tableName 	= "module_acl_group_settings";
                    $fields 	= array(
                        'module_id'  => $selected_componet_arr[0],
                        'group_id'   => $arr_id[0],
                        'section_id' =>$selected_componet_arr[1],
                        'allowed'    =>0
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
               }else{
                /**
                 * User Lists
                **/
                  $tableName 	= "module_acl_user_settings";
                    $fields 	= array(
                        'module_id'  => $selected_componet_arr[0],
                        'user_id'   => $arr_id[1],
                        'section_id' =>$selected_componet_arr[1],
                        'allowed'    =>0
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
               }
            }

         }elseif($ajax_mode == "rebuild"){
            $selected_componet = $_REQUEST['selected_module'];
            $selected_componet_arr = explode("-",$selected_componet);
            if($selected_componet_arr[0] == 0) $selected_componet_arr[0] = $selected_componet_arr[1];
            
            $array_users_allowed = array();
            $array_users_denied = array();
            $array_groups_allowed = array();
            $array_groups_denied = array();

            $tableName 	= "module_acl_user_settings";
            $fields 	= "*";
            $where      = "section_id =".$selected_componet_arr[1];
            $join       = array(
                            array(
                                'table'     => array(
                                                'module_acl_user_groups'=>'module_acl_user_groups',
                                             ), 
                                'on'        => 'module_acl_user_settings.user_id = module_acl_user_groups.user_id', 
                                'fields'    => array (
                                             ), 
                                'type'      => 'left',
                            ),
                        );

            $parameter = array(
                'tableName' => $tableName,
                'fields'    => $fields,
                'join'      => $join,
                'where'     => $where,
            ); 
            $res_users = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
            foreach($res_users as $key_level_1 => $val_level_1) {
                foreach($val_level_1 as $row){
                    if($row['allowed'] == 1){
                        if(!$array_users_allowed[$row['group_id']]) $array_users_allowed[$row['group_id']] = array();
                        array_push($array_users_allowed[$row['group_id']],$row['user_id']);
                    }else{
                        if(!$array_users_denied[$row['group_id']]) $array_users_denied[$row['group_id']] = array();
                        array_push($array_users_denied[$row['group_id']],$row['user_id']);
                    }
                }
            }

            $tableName 	= "module_acl_group_settings";
            $fields 	= "*";
            $where      = "section_id =".$selected_componet_arr[1];

            $parameter = array(
                'tableName' => $tableName,
                'fields'    => $fields,
                'where'     => $where,
            ); 
            $res_group = $this->getEventManager()->trigger('selectEvent', $this, $parameter);

            foreach($res_group as $key_level_1 => $val_level_1) {
                foreach($val_level_1 as $row){
                    if($row['allowed'] == 1){
                        array_push($array_groups_allowed,$row['group_id']);
                    }else{
                        array_push($array_groups_denied,$row['group_id']);
                    }
                }
            }
            
            $arr_return = array();
            $arr_return['group_allowed'] = $array_groups_allowed;
            $arr_return['group_denied'] = $array_groups_denied;
            $arr_return['user_allowed'] = $array_users_allowed;
            $arr_return['user_denied'] = $array_users_denied;
            echo json_encode($arr_return);
        }elseif($ajax_mode == "save_acl_advanced"){
            
            $ACL_DATA  = json_decode($this->getRequest()->getPost('acl_data', null),true);
            $module_id = $this->getRequest()->getPost('module_id', null);
            
            /**
             * Delete Previously Stored ACL Data
             **/
            
            $tableName = "module_acl_group_settings";
            $where     = "module_id = ".$module_id;
            $parameter = array(
                'tableName' => $tableName,
                'where'    => $where,
             );

            $data = $this->getEventManager()->trigger('deleteEvent', $this, $parameter);
            foreach($ACL_DATA['allowed'] as $section_id => $sections){
                foreach($sections as $group_id){
                    $tableName 	= "module_acl_group_settings";
                    $fields 	= array(
                        'module_id'  => $module_id,
                        'group_id'   => $group_id,
                        'section_id' => $section_id,
                        'allowed'    => 1
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
                }  
            }
            
            foreach($ACL_DATA['denied'] as $section_id => $sections){
                foreach($sections as $group_id){
                    $tableName 	= "module_acl_group_settings";
                    $fields 	= array(
                        'module_id'  => $module_id,
                        'group_id'   => $group_id,
                        'section_id' => $section_id,
                        'allowed'    => 0
                        
                    );
                    $parameter = array(
                        'tableName' => $tableName,
                        'fields'    => $fields,
                    );
                    $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
                }
            }
        }elseif($ajax_mode == "get_sections_by_module"){
            $module_id = $this->getRequest()->getPost('module_id', null);
            $tableName 	= "module_acl_sections";
            $fields 	= "*";
            $where          = "parent_section = ".$module_id;
            $parameter      = array(
                                'tableName' => $tableName,
                                'fields'    => $fields,
                                'where'     => $where,
                              );
            $result = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
            $array_sections = array();
            foreach($result as $key_level_1 => $val_level_1) {
                foreach($val_level_1 as $row){
                    $array_sections[$row['section_id']] = $row['section_name'];
                }
            }
            echo json_encode($array_sections);
        }elseif($ajax_mode == "save_sections_by_module"){
            
            $tableName 	= "module_acl_sections";
            $fields 	= "*";
            $order      = "section_id DESC";
            $limit      = "0,1";
            $parameter  = array(
                                'tableName' => $tableName,
                                'fields'    => $fields,
                                'order'     => $order,
                                'limit'     => $limit
                            );
            $result = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
            $current_section_id ='';
            foreach($result as $key_level_1 => $val_level_1) {
                foreach($val_level_1 as $row){
                    $current_section_id = $row['section_id'];
                }
            }
            $current_section_id++;
            
            $module_id          = $this->getRequest()->getPost('mod_id', null);
            $parent_id          = $this->getRequest()->getPost('parent_id', null);
            $section_identifier = $this->getRequest()->getPost('section_identifier', null);
            $section_name       = $this->getRequest()->getPost('section_name', null);
            
            if(!$parent_id) $parent_id = $module_id;
            
            $tableName 	= "module_acl_sections";
            $fields 	= array(
                'section_id'       => $current_section_id,
                'section_name'     => $section_name,
                'parent_section'   => $parent_id,
                'section_identifier' => $section_identifier,
            );
            $parameter = array(
                'tableName' => $tableName,
                'fields'    => $fields,
             );
            
            $data = $this->getEventManager()->trigger('insertEvent', $this, $parameter);
        }
        exit();
    }
    
    
    /**
     *
     * Function to Print Componets Tree Structure
     * @param String $currentParent Root Node of Tree
     * @param String $currLevel Current Depth of Tree
     * @param String $prevLevel Prev Depth of Tree
     * 
     **/
    private function createTreeView($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
        foreach($array as $categoryId => $category) {
         if($category['name']=='') continue;
         if ($currentParent == $category['parent_id']) {
            if ($currLevel > $prevLevel) echo " <ul> "; 
            if ($currLevel == $prevLevel) echo " </li> ";
            $class="";
            echo '<li id="'.$category['parent_id']."-".$category['id'].'" value="'.$category['name'].'" '.$class.' ><div onclick="selectThis(\''.$category['parent_id'].'-'.$category['id'].'\');rebuild();" class="list">'.$category['name']."</div>";
            if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }
            $currLevel++; 
            $this->createTreeView ($array, $categoryId, $currLevel, $prevLevel);
            $currLevel--;
    }  
        }
        if ($currLevel == $prevLevel) echo "</li></ul> ";
    }
    
    /**
     *
     * Function to Print User group Tree Structure
     * @param String $id String to Prepend with <li> Id
     * @param String $visibility <li> Visibility
     * @param String $dragabble Class to Make <li> Title Draggable
     * @param String $li_class <li> Class Name
     * 
     **/
    private function createUserGroups($id="user_group_",$visibility="",$dragabble="draggable",$li_class=""){
                
        $output_string = "";
        $tableName  = "module_acl_user_groups";
        //$fields     = "module_acl_user_groups.user_id,CONCAT_WS(' ',users.fname,users.mname,users.lname) AS user_name";
        $fields     = "*";
        $order      = "group_id";
        $join       = array(
                            array(
                                'table'     => array(
                                                'module_acl_groups'=>'module_acl_groups',
                                             ), 
                                'on'        => 'module_acl_user_groups.group_id = module_acl_groups.group_id', 
                                'fields'    => array (
                                                'group_name' => 'group_name',
                                             ), 
                                'type'      => 'left',
                            ),
                            array(
                                'table'     => array(
                                                'users'=>'users',
                                             ), 
                                'on'        => 'users.id = module_acl_user_groups.user_id', 
                                'fields'    => array (
                                                'user_name' => new Expression("CONCAT_WS(' ',users.fname,users.mname,users.lname)"),
                                             ), 
                                'type'      => 'left',
                            ),

                        );

        $parameter = array(
            'tableName' => $tableName,
            'fields'    => $fields,
            'join'      => $join,
            'order'     => $order,
        ); 
        $res_users = $this->getEventManager()->trigger('selectEvent', $this, $parameter);
        
        $tempList  = array();
        foreach($res_users as $key_level_1 => $val_level_1) {
                foreach($val_level_1 as $row){
                    $tempList[$row['group_id']]['group_name'] = $row['group_name'];
                    $tempList[$row['group_id']]['group_id'] = $row['group_id'];
                    $tempList[$row['group_id']]['items'][] = $row;
                }
        }

        $output_string .='<ul>';   
        foreach ($tempList as $groupID => $tempListRow) {
            $output_string .='<li '.$li_class.' id="li_'.$id.$tempListRow['group_id'].'-0" style="'.$visibility.'"><div class="'.$dragabble.'" id="'.$id.$tempListRow['group_id'].'-0" >' . $tempListRow['group_name'].'</div>';
            if(!empty($tempListRow['items'])) {
              $output_string .='<ul>';   
              foreach ($tempListRow['items'] as $key => $itemRow){
                 $output_string .='<li '.$li_class.' id="li_'.$id.$itemRow['group_id'].'-'.$itemRow['user_id'].'" style="'.$visibility.'"><div class="'.$dragabble.'" id="'.$id.$itemRow['group_id'].'-'.$itemRow['user_id'].'">' . $itemRow['user_name'] . '</div></li>';
              }
              $output_string .='</ul>';
            }
            $output_string .='</li>';
        }
        $output_string .='</ul>';
        return $output_string;
    }
    
    /**
     * Table Gateway
     * 
     * @return type
     */
    public function getAclTable()
    {	
        if (!$this->aclTable) {
            $sm = $this->getServiceLocator();
            $this->aclTable = $sm->get('Acl\Model\AclTable');
        }
        return $this->aclTable;
    }
}
