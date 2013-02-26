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
       //return new ViewModel(array(
       //     'lab' => $this->getLabTable()->fetchAll(),
       // ));
    }

    public function getLabTable()
    {
        //if (!$this->labTable) {
        //    $sm = $this->getServiceLocator();
        //    $this->albumTable = $sm->get('Lab\Model\LabTable');
        //}
        //return $this->labTable;
    }
}