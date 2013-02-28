<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Lab;
use Lab\Form\LabForm;

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
	$form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Lab();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getLabTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('lab');
            }
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
    
    public function searchAjaxAction()
    {	
	$request 	= $this->getRequest();
	$inputString 	= $request->getPost('inputValue');
	if ($request->isPost()) {
		if($request->getPost('type') == 'getProcedures' ){ 
			$patients = $this->getPatients($inputString);
			$data = new JsonModel($patients);
			return $data;
		}
	}
    }
    
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\LabTable');
        }
        return $this->labTable;
    }
    
    public function getLabs()
    {
	$providers = $this->getLabTable()->listLabs();
	return $providers;
    }
}