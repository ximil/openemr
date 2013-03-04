<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Lab;
use Lab\Form\LabForm;
use Zend\View\Model\JsonModel;

class LabController extends AbstractActionController
{
    protected $labTable;
	
    public function indexAction()
    {
        $form = new LabForm();
	$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
	$providers = $helper->getProviders();
	$form->get('provider')->setValueOptions($providers);
	
	$labs = $helper->getLabs();
	$form->get('lab_id')->setValueOptions($labs);
	
	$priority = $helper->getList("ord_priority");
	$form->get('priority')->setValueOptions($priority);
	
	$status = $helper->getList("ord_status");
	$form->get('status')->setValueOptions($status);
	//$form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
	    $Lab = new Lab();
	    //$form->setInputFilter($Lab->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
		$Lab->exchangeArray($form->getData());
                $this->getLabTable()->saveLab($Lab);
            
                return $this->redirect()->toRoute('result');
            }
	    else {
		echo 'invalid ..';
		foreach ($form->getMessages() as $messageId => $message) {
			echo "Validation failure '$messageId':"; var_dump($message);
		} //echo '<pre>'; var_dump($form); echo '</pre>';
	    }//die("jjjjjjjjjj");
        }
        return array('form' => $form);
    }
    
    public function labLocationAction()
    {
	$request 		= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	if ($request->isPost()) {
		$location = $this->getLabLocation($inputString);
		$data = new JsonModel($location);
		return $data;
	}
    }
    
    public function getLabLocation($inputString)
    {
	$patents = $this->getLabTable()->listLabLocation($inputString);
	return $patents;
    }
    
    public function searchAction()
    {	
	$request 	= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$data = new JsonModel($procedures);
		//$data = json_encode($procedures);
		return $data;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		$AOE = $this->getAOE($inputString,$dependentId);
		$data = new JsonModel($AOE);
		return $data;
	    }
	}
    }
    public function search11Action()
    {
	//$fh = fopen("D:/AOE3.txt","a");
	//fwrite($fh,"gfgzGGGGGGG\r\n");
	$request 	= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	$dependentId 	= $request->getPost('dependentId');
	$viewModel = new ViewModel();
    //$viewModel->setTemplate('module/controller/action');
    $viewModel->setTerminal(true);
    
	//$fh = fopen("D:/AOE.txt","a");
		//fwrite($fh,"gfgzGGGGGGG\r\n");
		//fwrite($fh,print_r($request->getPost(),1));
	if ($request->isPost()) {
	    if($request->getPost('type') == 'getProcedures' ){ 
		$procedures = $this->getProcedures($inputString,$dependentId);
		$data = new JsonModel($procedures);
		//$data = json_encode($procedures);
		return $data;
	    }
	    if($request->getPost('type') == 'loadAOE'){
		//$fh = fopen("D:/AOE.txt","a");
		//fwrite($fh,"gfgzG");
		$AOE = $this->getAOE($inputString,$dependentId);
		$data = new JsonModel($AOE);
		return $data;
	    }
	}
    }
    
    public function getProcedures($inputString,$labId)
    {
	$procedures = $this->getLabTable()->listProcedures($inputString,$labId);
	return $procedures;
    }
    
    public function getAOE($procedureCode,$labId)
    {
	$AOE = $this->getLabTable()->listAOE($procedureCode,$labId);
	return $AOE;
    }
    
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\LabTable');
        }
        return $this->labTable;
    }
	
    public function resultAction()
    {

    }
	
    public function getLabResult()
    {
	$labResult = $this->getLabTable()->listLabResult();
	return $labResult;
    }
	
    public function resultShowAction()
    {
	$labResult = $this->getLabResult();
	$data = new JsonModel($labResult);
	return $data;
    }
    public function resultUpdateAction()
    {
	/*$form = new LabForm();
	$lab = new Lab();
		$request = $this->getRequest();
	if ($request->isPost()) {
	    $form->setInputFilter($lab->getInputFilter());
	    if ($form->isValid()) {
		    $lab->exchangeArray($form->getData());
		    print_r($form->getData());
		    $this->getLabTable()->saveLab($lab);
		    echo 'Successfuly inserted..';
	    } else {
		    echo 'invalid ..';
		    foreach ($form->getMessages() as $messageId => $message) {
			    echo "Validation failure '$messageId':"; print_r($message);
		    }
	    }
	}*/
	    
	$request = $this->getRequest();
	if ($request->isPost()) {
	    $data = array(
		'procedure_report_id'	=> $request->getPost('procedure_report_id'),
		'procedure_result_id'	=> $request->getPost('procedure_result_id'),
		'procedure_order_id'	=> $request->getPost('procedure_order_id'),
		'specimen_num'		=> $request->getPost('specimen_num'),
		'report_status'  	=> $request->getPost('report_status'),
		'procedure_order_seq'	=> $request->getPost('procedure_order_seq'),
		'date_report'		=> $request->getPost('date_report'),
		'date_collected'	=> $request->getPost('date_collected'),
	    );
	    $this->getLabTable()->saveResult($data);
	    return $this->redirect()->toRoute('result');
	}

}