<?php
//    +-----------------------------------------------------------------------------+ 
//    OpenEMR - Open Source Electronic Medical Record
//    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU Affero General Public License as
//    published by the Free Software Foundation, either version 3 of the
//    License, or (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU Affero General Public License for more details.
//
//    You should have received a copy of the GNU Affero General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
//    Author:   Remesh Babu S <remesh@zhservices.com>
//
// +------------------------------------------------------------------------------+
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