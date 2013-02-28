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
       //return new ViewModel(array(
       //     'lab' => $this->getLabTable()->fetchAll(),
       // ));
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
	
	public function getPatients()
	{
		$patents = $this->getLabTable()->listPatients();
		return $patents;
	}
	
	public function resultShowAction()
	{
		$patients = $this->getPatients();
		$data = new JsonModel($patients);
		return $data; 
		
	}
	public function resultUpdateAction()
	{
		$request = $this->getRequest();
        if ($request->isPost()) {
			$data = array(
					'procedure_result_id'	=> $request->getPost('procedure_result_id'),
					'procedure_order_id'	=> $request->getPost('procedure_order_id'),
					'specimen_num'			=> $request->getPost('specimen_num'),
					'report_status'  		=> $request->getPost('report_status'),
					'procedure_order_seq'	=> $request->getPost('procedure_order_seq'),
					'date_report'			=> $request->getPost('date_report'),
					'date_collected'		=> $request->getPost('date_collected'),
			);
			$this->getLabTable()->saveResult($data);
			return $this->redirect()->toRoute('result');
        }
        return $this->redirect()->toRoute('result');
	}

}