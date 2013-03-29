<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Configuration;//vip
use Lab\Form\PullForm;//EDITED
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

class ConfigurationController extends AbstractActionController
{
    protected $configurationTable;

    public function indexAction()
    {
       
    }
    
    public function getConfigurationTable()
    {
        if (!$this->configurationTable) {
            $sm = $this->getServiceLocator();
            $this->configurationTable = $sm->get('Lab/Model/ConfigurationTable');
        }
        return $this->configurationTable;
    }

    public function getConfigEditDeatilsAction()
    {	
	$request    	= $this->getRequest();
	$data  	    	= array('type_id'    => $request->getQuery('type_id'));		
        $typeID		= $data['type_id'];
	
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	
	$body_sites 	= $helper->getList("proc_body_site");
	$specimen_type 	= $helper->getList("proc_specimen");
	$admin_via 	= $helper->getList("proc_route");
	$laterality 	= $helper->getList("proc_lat");
	$dafault_units 	= $helper->getList("proc_unit");
	
	$list_arr	= array();
	
	$list_arr[]	= $body_sites;
	$list_arr[]	= $specimen_type;
	$list_arr[]	= $admin_via;
	$list_arr[]	= $laterality;
	$list_arr[]	= $dafault_units;
	
	$ret_arr 	= $this->getConfigurationTable()->getConfigDetails($typeID,$list_arr);	
	return $ret_arr;	
    }
    
    public function deleteConfigDetailsAction()
    {
	$request    	= $this->getRequest();
	$data  	    	= array('type_id'    => $request->getQuery('type_id'));		
        $typeID		= $data['type_id'];	
	$ret_arr 	= $this->getConfigurationTable()->deleteConfigDetails($typeID);	
	return $ret_arr;	
    }
    
    
    public function getAllConfigDeatilsAction()
    {
	$ret_arr 	= $this->getConfigurationTable()->getAllConfigDetails();		
	return $ret_arr;	
    }
    
    public function saveConfigDetailsAction()
    {
	$request    	= $this->getRequest();
	$up_res		= $this->getConfigurationTable()->updateConfigDetails($request);
	return $up_res;
    }
    
    public function getConfigAddPageDeatilsAction()
    {
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	
	$body_sites 	= $helper->getList("proc_body_site");
	$specimen_type 	= $helper->getList("proc_specimen");
	$admin_via 	= $helper->getList("proc_route");
	$laterality 	= $helper->getList("proc_lat");
	$dafault_units 	= $helper->getList("proc_unit");
	
	$list_arr	= array();
	
	$list_arr[]	= $body_sites;
	$list_arr[]	= $specimen_type;
	$list_arr[]	= $admin_via;
	$list_arr[]	= $laterality;
	$list_arr[]	= $dafault_units;
		
	$ret_arr 	= $this->getConfigurationTable()->getAddConfigDetails($list_arr);
	return $ret_arr;
    }
    
    public function addConfigDetailsAction()
    {
	$request    	= $this->getRequest();
	$up_res		= $this->getConfigurationTable()->addConfigDetails($request);
	return $up_res;
    }
    
    /*public function getAddExistConfigDeatilsAction()
    {
	$request    	= $this->getRequest();
	$data  	    	= array('type_id'    => $request->getQuery('type_id'));	
        $typeID		= $data['type_id'];	
	$ret_arr 	= $this->getConfigurationTable()->getAddExistConfigDetails($typeID);
	return $ret_arr;
    }*/
    
    
    
}