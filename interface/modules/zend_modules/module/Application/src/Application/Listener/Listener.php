<?php
namespace Application\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Listener extends AbstractActionController implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    protected $applicationTable;
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    { 	
      $sharedEvents      = $events->getSharedManager();
      $this->listeners[] = $events->attach('aclcheckEvent', array($this, 'onAclcheckEvent'));
    }
    

    public function detach(EventManagerInterface $events)
    {
      foreach ($this->listeners as $index => $listener) {
        if ($events->detach($listener)) {
          unset($this->listeners[$index]);
        }
      }
    }
   
    /**
     * Language converter
     * @param string $str
     * @return string
     */
    public function zht($str)
    {
      return xlt($str);
    }
   
    /**
     * ACL Check
     * @param array $e
     * @return boolean
     * 
     */
    
    public function onAclcheckEvent($e){
      $event  = $e->getName();
      $params = $e->getParams();
      $user_id 	= $params['user_id'];
      $module_id 	= $params['module_id'];
      $section_id	= $params['section_id'];
      $query_check_acl_user  = "SELECT allowed FROM module_acl_user_settings WHERE module_id = ? AND section_id = ? AND user_id = ? AND allowed = ?";
      $query_check_acl_group = "SELECT allowed FROM module_acl_group_settings WHERE module_id = ? AND section_id = ? AND group_id IN (?) AND allowed = ?";
      $query_get_user_group  = "SELECT * FROM `module_acl_user_groups` WHERE user_id = ?";
      $res = sqlStatement($query_get_user_group,array($user_id));
      $groups = array();
      while($row = sqlFetchArray($res)){
        array_push($groups,$row['group_id']);
      }
      $groups_str = implode(",",$groups);
      if(sqlNumRows(sqlStatement($query_check_acl_user,array($module_id,$section_id,$user_id,0))) > 0)
        return false;
      elseif(sqlNumRows(sqlStatement($query_check_acl_user,array($module_id,$section_id,$user_id,1))) > 0)
        return true;
      elseif(sqlNumRows(sqlStatement($query_check_acl_group,array($module_id,$section_id,$groups_str,0))) > 0)
        return false;
      elseif(sqlNumRows(sqlStatement($query_check_acl_group,array($module_id,$section_id,$groups_str,1))) > 0)
        return true;
      else
        return false;
    }

}