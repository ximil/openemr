<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Pull;
use Lab\Form\PullForm;//EDITED
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

class PullController extends AbstractActionController
{
    protected $pullTable;

    public function indexAction()
    {
        $form 	= new PullForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	
	$labs = $helper->getLabs();
	$form->get('lab_id')->setValueOptions($labs);
	
        return array('form' => $form);
    }
    
    public function getPullTable()
    {
        if (!$this->pullTable) {
            $sm = $this->getServiceLocator();
            $this->pullTable = $sm->get('Lab/Model/PullTable');
        }
        return $this->pullTable;
    }
   
    public function pullcompendiumtestAction()
    {
	$retmsg		= "";
	
	$request 	= $this->getRequest();
	$response       = $this->getResponse();
	
	$lab_id 	= $request->getPost('lab_id');
	
	$cred 		= $this->getPullTable()->getLabCredentials($lab_id);
                
	$username   	= trim($cred['login']);
	$password   	= trim($cred['password']);        
	$remote_host   	= trim($cred['remote_host']);
	
	if(($username == "")||($password == ""))
	{
	    $retmsg	= "Lab Credentials not found";
	}	
	else if($remote_host == "")
	{
	    $retmsg	= "Remote Host not found";
	}
	else
	{
	    ini_set("soap.wsdl_cache_enabled","0");	
	    ini_set('memory_limit', '-1');
	    
	    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
	    
	    $client     	= new Client(null,$options);	
	    $result     	= $client->check_for_tests($username,$password,$remote_host);
	    
	    if($result <> "-3")
	    {	    
		$testconfig_arr = $this->getPullTable()->pullcompendiumTestConfig();		
		$retstr		= $this->getPullTable()->importDataCheck($result,$testconfig_arr);	
		$retmsg		= "Test Pulled";		
	    }
	    else
	    {
		$retmsg		= "Unauthorised Lab Access";
	    }
	}
	//return $this->redirect()->toRoute('result');
	$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'result' => $retmsg)));
	return $response;
    }
    
    public function pullcompendiumaoeAction()
    {
	$retmsg		= "";
	
	$request 	= $this->getRequest();
	$response       = $this->getResponse();
	
	$lab_id 	= $request->getPost('lab_id');	
	
	$cred 		= $this->getPullTable()->getLabCredentials($lab_id);
                
	$username   	= trim($cred['login']);
	$password   	= trim($cred['password']);        
	$remote_host   	= trim($cred['remote_host']);
	
	if(($username == "")||($password == ""))
	{
	    $retmsg	= "Lab Credentials not found";
	}	
	else if($remote_host == "")
	{
	    $retmsg	= "Remote Host not found";
	}
	else
	{	    
	    ini_set("soap.wsdl_cache_enabled","0");
	    ini_set('memory_limit', '-1');
	    
	    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
	    $client     = new Client(null,$options);
	    $result     = $client->check_for_aoe($username,$password,$remote_host);	
	    
	    if($result <> "-3")
	    {		
		$aoeconfig_arr 	= $this->getPullTable()->pullcompendiumAoeConfig();		
		$retstr		= $this->getPullTable()->importDataCheck($result,$aoeconfig_arr);	    
		$retmsg		= "AOE Pulled";
	    }
	    else
	    {
		$retmsg		= "Unauthorised Lab Access";
	    }	    
	}
	
	//return $this->redirect()->toRoute('result');
	$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'result' => $retmsg )));
	return $response;
    }
    
    public function pullcompendiumtestaoeAction()
    {
	$retmsg		= "";
	$retmsg_test	= "";
	$retmsg_aoe	= "";
	
	$request 	= $this->getRequest();
	$response       = $this->getResponse();
	
	$lab_id 	= $request->getPost('lab_id');
	
	$cred 		= $this->getPullTable()->getLabCredentials($lab_id);
                
	$username   	= trim($cred['login']);
	$password   	= trim($cred['password']);        
	$remote_host   	= trim($cred['remote_host']);
	
	if(($username == "")||($password == ""))
	{
	    $retmsg	= "Lab Credentials not found";
	}	
	else if($remote_host == "")
	{
	    $retmsg	= "Remote Host not found";
	}
	else
	{	    
	    ini_set("soap.wsdl_cache_enabled","0");	
	    ini_set('memory_limit', '-1');
	    
	    $options    	= array('location' => $remote_host,
					'uri'      => "urn://zhhealthcare/lab"
					);
	    
	    $client     	= new Client(null,$options);	
	    $result_test     	= $client->check_for_tests($username,$password,$remote_host);
	    
	    if($result_test <> "-3")
	    {	    
		$testconfig_arr = $this->getPullTable()->pullcompendiumTestConfig();		
		$retstr		= $this->getPullTable()->importDataCheck($result_test,$testconfig_arr);			
		$retmsg_test	= "Test Pulled";		
	    }
	    else
	    {
		$retmsg		= "Unauthorised Lab Access";
	    }
	    
	    $result_aoe	= $client->check_for_aoe($username,$password,$remote_host);	
	    
	    if($result <> "-3")
	    {		
		$aoeconfig_arr 	= $this->getPullTable()->pullcompendiumAoeConfig();		
		$retstr		= $this->getPullTable()->importDataCheck($result_aoe,$aoeconfig_arr);	    
		$retmsg_aoe	= "AOE Pulled";
	    }
	    else
	    {
		$retmsg		= "Unauthorised Lab Access";
	    }    	    
	}
	//return $this->redirect()->toRoute('result');
	$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'result' => $retmsg." ".$retmsg_test.", ".$retmsg_aoe)));
	return $response;
    }    
}