<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ResultController extends AbstractActionController
{
    protected $labTable;
	
    public function indexAction()
    {

    }
    
    public function getLabTable()
    {
        if (!$this->labTable) {
            $sm = $this->getServiceLocator();
            $this->labTable = $sm->get('Lab\Model\ResultTable');
        }
        return $this->labTable;
    }
    
    public function resultShowAction()
    {
        $request = $this->getRequest();
        $data =array();
        if($request->isPost()){
            $data = array(
                    'statusReport'  => $request->getPost('statusReport'),
                    'statusOrder'   => $request->getPost('statusOrder'),
                    'statusResult'  => $request->getPost('statusResult'),
                    'dtFrom'        => $request->getPost('dtFrom'),
                    'dtTo'          => $request->getPost('dtTo'),
                    'page'          => $request->getPost('page'),
                    'rows'          => $request->getPost('rows'),
            ); 
        }
        //$data = array(1,2);
        //$fh = fopen("D:/test.txt","a");
        //fwrite($fh,print_r($data,1));
        $labResult = $this->getLabResult($data);
        $data = new JsonModel($labResult);
        return $data;
    }
    
    public function getLabResult($data)
    {
        $labResult = $this->getLabTable()->listLabResult($data);
        return $labResult;
    }
    
    public function getLabOptionsAction()
    {
        $request = $this->getRequest();
        $data =array();
            if($request->getQuery('opt')){
                switch ($request->getQuery('opt')) {
                    case 'search':
                        $data['opt'] = 'search';
                        break;
                    case 'status':
                        $data['opt'] = 'status';
                        break;
                    case 'abnormal':
                        $data['opt'] = 'abnormal';
                        break;
                }
            }
            if($request->getQuery('optId')){
                switch ($request->getQuery('optId')) {
                    case 'order':
                        $data['optId'] = 'ord_status';
                        break;
                    case 'report':
                        $data['optId'] = 'proc_rep_status';
                        break;
                    case 'result':
                        $data['optId'] = 'proc_res_status';
                        break;
                    case 'abnormal':
                        $data['optId'] = 'proc_res_abnormal';
                        break;
                }
            }
        $labOptions = $this->getLabTable()->listLabOptions($data);
        $data = new JsonModel($labOptions);
        return $data;
    }
    
    /*public function getLabStatusAction()
    {
        //$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('emr_helper');
        $request = $this->getRequest();
        $data =array();
            if($request->getQuery('opt')){
                $data['opt'] = 'search';
            }
        $labStatus = $this->getLabTable()->listLabStatus($data);
        $data = new JsonModel($labStatus);
        return $data;
    }
    
    public function getLabAbnormalAction()
    {
        $labAbnormal = $this->getLabTable()->listLabAbnormal();
        $data = new JsonModel($labAbnormal);
        return $data;
    }
    */
    public function getResultCommentsAction()
    {
        $request = $this->getRequest();
        $data =array();
            if($request->getPost('prid')){
                $data['procedure_result_id'] = $request->getPost('prid');
            }
        $comments = $this->getLabTable()->listResultComment($data);
        $data = new JsonModel($comments);
        return $data;
    }
    
    public function resultUpdateAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arr = explode('|', $request->getPost('comments'));
            $comments = '';
            $comments = $arr[2];
            if ($arr[3] != '') {
                $comments .=  "\n" . $arr[3];
            }
            $data = array(
                            'procedure_report_id'   => $request->getPost('procedure_report_id'),
                            'procedure_result_id'   => $request->getPost('procedure_result_id'),
                            'procedure_order_id'    => $request->getPost('procedure_order_id'),
                            'specimen_num'	    => $request->getPost('specimen_num'),
                            'report_status'  	    => $request->getPost('report_status'),
                            'procedure_order_seq'   => $request->getPost('procedure_order_seq'),
                            'date_report'	    => $request->getPost('date_report'),
                            'date_collected'	    => $request->getPost('date_collected'),
                            'result_code'	    => $request->getPost('result_code'),
                            'result_text'	    => $request->getPost('result_text'),
                            'abnormal'		    => $request->getPost('abnormal'),
                            'result'		    => $request->getPost('result'),
                            'range'		    => $request->getPost('range'),
                            'units'		    => $request->getPost('units'),
                            'result_status'	    => $arr[0],
                            'facility'		    => $arr[1],
                            'comments'		    => $comments,
            );
            $this->getLabTable()->saveResult($data);
            return $this->redirect()->toRoute('result');
        }
        return $this->redirect()->toRoute('result');
    }
}