<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Specimen;
use Lab\Form\SpecimenForm;
use Zend\View\Model\JsonModel;

use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;

use Zend\ZendPdf;

class SpecimenController extends AbstractActionController
{
    protected $specimenTable;
    
    public function indexAction()
    {
        global $pid;
				$form = new SpecimenForm();
				$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
				$statuses = $helper->getList("proc_rep_status");
				$form->get('specimen_status[]')->setValueOptions($statuses);
				if($pid){
						$form->get('patient_id')->setValue($pid);
						$search_pid = $pid;
				}
				$form->get('search_patient')->setValue($this->getSpecimenTable()->getPatientName($pid));
				$request = $this->getRequest();
				$from_dt = null;
				$to_dt = null;
        if ($request->isPost()) {
						$search_pid = $request->getPost()->patient_id;
						$form->get('search_patient')->setValue($this->getSpecimenTable()->getPatientName($search_pid));
						$from_dt = $request->getPost()->search_from_date;
						$to_dt = $request->getPost()->search_to_date;
						$form->get('patient_id')->setValue($search_pid);
						$form->get('search_from_date')->setValue($from_dt);
						$form->get('search_to_date')->setValue($to_dt);
				}
				$this->layout()->res = $this->getSpecimenTable()->listOrders($search_pid,$from_dt,$to_dt);

        /*$request = $this->getRequest();
        if ($request->isPost()) {
				$Lab = new Lab();
				$aoeArr = array();
				foreach($request->getPost() as $key=>$val){
						if(substr($key,0,4)==='AOE_'){
								$NewArr = explode("_",$key);
								$aoeArr[$NewArr[1]][$NewArr[2]] = $val;
						}
				}
        $form->setData($request->getPost());
				if ($form->isValid()) {
						$Lab->exchangeArray($form->getData());
            //$clientorder_id = $this->getLabTable()->saveLab($Lab,$aoeArr);
						$clientorder_id = $this->getLabTable()->saveLab($request->getPost(),$aoeArr);
			
		//------------- STARTING PROCEDURE ORDER XML IMPORT -------------
                //GET CLIENT CREDENTIALS OF INITIATING ORDER
                $cred           = $this->getLabTable()->getClientCredentials($clientorder_id[0]);                
                $username       = $cred['login'];
                $password       = $cred['password'];
                $remote_host    = trim($cred['remote_host']);
                $site_dir       = $GLOBALS['OE_SITE_DIR'];
                
                if(($username <> "")&&($password <> "")&&($remote_host <> "")) {//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB               
                    //RETURNS AN ARRAY OF ALL PENDING ORDERS OF THE PATIENT
                    $xmlresult_arr = $this->getLabTable()->generateOrderXml($request->getPost('patient_id'),$request->getPost('lab_id'),"");
                    
                    ini_set("soap.wsdl_cache_enabled","0");            
                    ini_set('memory_limit', '-1');
                    
                    $options    = array('location' => $remote_host,
				'uri'      => "urn://zhhealthcare/lab"
				);
                    $client     = new Client(null,$options);                    
                    $lab_id     = $request->getPost('lab_id');   
                    
                    foreach($xmlresult_arr as $xmlresult){
                        $order_id   = $xmlresult['order_id'];
                        $xmlstring  = $xmlresult['xmlstring'];
                        
                        //GET CLIENT CREDENTIALS OF EACH PENDING ORDER OF A PARTICULAR PATIENT   
                        $cred           = $this->getLabTable()->getClientCredentials($order_id);                    
                        $username       = $cred['login'];
                        $password       = $cred['password'];
                        $remote_host    = trim($cred['remote_host']);
                        $site_dir       = $GLOBALS['OE_SITE_DIR'];
                        
                        if(($username <> "")&&($password <> "")&&($remote_host <> "")){//GENERATE ORDER XML OF EXTERNAL LAB ONLY, NOT FOR LOCAL LAB
                            $result = $client->importOrder($username,$password,$site_dir,$order_id,$lab_id,$xmlstring);
                            
                            if(is_numeric($result))// CHECKS IF ORDER IS SUCCESSFULLY IMPORTED
                            {
                                $this->getLabTable()->setOrderStatus($order_id,"routed");
                            }
                        }                        
                    }                    
                }
		//------------- END PROCEDURE ORDER XML IMPORT -------------
			
                return $this->redirect()->toRoute('result');
            }
	    else {
		echo 'invalid ..';
		foreach ($form->getMessages() as $messageId => $message) {
			echo "Validation failure '$messageId':"; var_dump($message);
		}
	    }
        }*/
        return array('form' => $form);
    }
		
		public function saveAction()
    {
				$request = $this->getRequest();
        if ($request->isPost()) {
						$this->getSpecimenTable()->saveSpecimenDetails($request->getPost());
						return $this->redirect()->toRoute('specimen');
				}
    }
		
		public function searchAction()
    {	
				$request 	= $this->getRequest();
				$response = $this->getResponse();
				$inputString 	= $request->getPost('inputValue');
				
				if ($request->isPost()) {
						if($request->getPost('type') == 'getPatient' ){
								$patients = $this->getPatients($inputString);
								$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'patientArray' => $patients)));
								return $response;
						}
				}
    }
    
    public function getSpecimenTable()
    {
        if (!$this->specimenTable) {
            $sm = $this->getServiceLocator();
            $this->specimenTable = $sm->get('Lab\Model\SpecimenTable');
        }
        return $this->specimenTable;
    }
		
		public function getPatients($inputString)
    {
				$patients = $this->getSpecimenTable()->listPatients($inputString);
				return $patients;
    }
   
}