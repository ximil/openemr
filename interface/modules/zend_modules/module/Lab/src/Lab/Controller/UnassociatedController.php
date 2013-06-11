<?php
namespace Lab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Lab\Model\Unassociated;
use Lab\Form\UnassociatedForm;
use Zend\View\Model\JsonModel;
use Zend\Soap\Client;
use Zend\Config;
use Zend\Config\Reader;
use C_Document;
use Lab\Controller\ResultController;

class UnassociatedController extends AbstractActionController
{
  protected $unassociatedTable;
  
  public function getUnassociatedTable()
  {
    if (!$this->unassociatedTable) {
        $sm = $this->getServiceLocator();
        $this->unassociatedTable = $sm->get('Lab\Model\UnassociatedTable');
    }
    return $this->unassociatedTable;
  }
  
  public function indexAction()
  {
    $request = $this->getRequest();
    $type = $request->getQuery('type');
    $this->layout()->type = $type;
    if($type == 'resolved'){
      $this->layout()->res = $this->getUnassociatedTable()->listResolvedPdf();
      $form = new UnassociatedForm();
      return array('form' => $form);
    }else{
      $this->layout()->res = $this->getUnassociatedTable()->listPdf();
      $form = new UnassociatedForm();
      return array('form' => $form);
    }
  }
  
  public function viewAction(){
    $request = $this->getRequest();
    $filename = $request->getQuery('filename');
    $unassociated_result_dir  = $GLOBALS['OE_SITE_DIR']."/lab/unassociated_result/";
    while(ob_get_level()) {
      ob_end_clean();
    }
    header('Content-Disposition: attachment; filename='.$filename );
    header("Content-Type: application/octet-stream" );
    header("Content-Length: " . filesize( $unassociated_result_dir.$filename ) );
    readfile( $unassociated_result_dir.$filename );
    return false;
  }
  
  public function attachAction(){
    global $pid;
    $request = $this->getRequest();
    $response = $this->getResponse();
    if(!$pid){
      return $response->setContent(\Zend\Json\Json::encode(array('error' => 'You must select a patient')));
    }
    if ($request->isPost()) {
      if($request->getPost()->type == 'attachToCurrentPatient'){
        require(dirname(__FILE__)."/../../../../../../../../controllers/C_Document.class.php");
        $_POST['process'] = true;
        $file_path = $GLOBALS['OE_SITE_DIR']."/lab/unassociated_result/".$request->getPost()->file_name;
        $mime_types = array(
          "pdf"=>"application/pdf"
          ,"exe"=>"application/octet-stream"
          ,"zip"=>"application/zip"
          ,"docx"=>"application/msword"
          ,"doc"=>"application/msword"
          ,"xls"=>"application/vnd.ms-excel"
          ,"ppt"=>"application/vnd.ms-powerpoint"
          ,"gif"=>"image/gif"
          ,"png"=>"image/png"
          ,"jpeg"=>"image/jpg"
          ,"jpg"=>"image/jpg"
          ,"mp3"=>"audio/mpeg"
          ,"wav"=>"audio/x-wav"
          ,"mpeg"=>"video/mpeg"
          ,"mpg"=>"video/mpeg"
          ,"mpe"=>"video/mpeg"
          ,"mov"=>"video/quicktime"
          ,"avi"=>"video/x-msvideo"
          ,"3gp"=>"video/3gpp"
          ,"css"=>"text/css"
          ,"jsc"=>"application/javascript"
          ,"js"=>"application/javascript"
          ,"php"=>"text/html"
          ,"htm"=>"text/html"
          ,"html"=>"text/html"
        );
          
        $extension = strtolower(end(explode('.',$file_path)));
        $mime_type = $mime_types[$extension];
        $_FILES['file']['name'][0]     = $request->getPost()->file_name;
        $_FILES['file']['type'][0]     = $mime_type;
        $_FILES['file']['tmp_name'][0] = $file_path;
        $_FILES['file']['error'][0]    = 0;
        $_FILES['file']['size'][0]     = filesize($file_path);
        $_POST['category_id']          = '2';
        $_POST['patient_id']           = $pid;
        $_GET['patient_id']            = $pid;
        
        $cdoc = new C_Document();
        if(!file_exists($cdoc->file_path.$request->getPost()->file_name)){
          $cdoc->upload_action_process();
          copy($file_path,$cdoc->file_path.$request->getPost()->file_name);
          $this->getUnassociatedTable()->attachUnassociatedDetails($request->getPost());
          return $response->setContent(\Zend\Json\Json::encode(array('response' => true)));
        }else{
          return $response->setContent(\Zend\Json\Json::encode(array('error' => 'This file is already attached to current patient')));
        }
      }elseif($request->getPost()->type == 'attachToOrder'){
        $this->getLabResultDetails($request->getPost()->file_order_id);
        return $response->setContent(\Zend\Json\Json::encode(array('response' => true)));
      }
    }
  }
  
}