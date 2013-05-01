<?php

namespace Appointments\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Appointments\Model\Appointments;
//use Appointments\Form\AppointmentsForm;
use Zend\View\Model\JsonModel;


class AppointmentsController extends AbstractActionController
{
    protected $appointmentsTable;
    
    public function indexAction()
    {
    	
    	
    }
    
    public function getAppointmentsDataAction()
    {
	$request = $this->getRequest();
	$data = array();
	if($request->isPost()){
    	   $data = array(
			'criteria'	=> $request->getPost('criteria'),
			'patient' 	=> $request->getPost('patient'),
			'dos' 		=> $request->getPost('dos'),
			'dtFrom' 	=> $request->getPost('dtFrom'),
			'dtTo' 		=> $request->getPost('dtTo'),
			'page'          => $request->getPost('page'),
			'rows'          => $request->getPost('rows'),
		    );

	//$fh = fopen("D:/test.txt","a");
	//fwrite($fh,"rrr:".print_r($request->getPost(),1));
    	}
	$result = $this->getAppointmentsTable()->listAppointments($data);
	$data = new JsonModel($result);
	return $data;
    }
    
       
    public function getAppointmentsTable()
    {
        if (!$this->appointmentsTable) {
            $sm = $this->getServiceLocator();
            $this->appointmentsTable = $sm->get('Appointments\Model\AppointmentsTable');
        }
        return $this->appointmentsTable;
    } 
}