<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2014 Z&H Consultancy Services Private Limited <sam@zhservices.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*    @author  Chandni Babu <chandnib@zhservices.com> 
* +------------------------------------------------------------------------------+
*/
namespace Cancercare\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Listener\Listener;
use Zend\Soap\Server;
use Cancercare\Model\CancercaredispatchTable;
use Zend\Filter\Compress\Zip;

class CancercaredispatchController extends AbstractActionController
{
  protected $data;
    
  protected $patient_id;

  protected $encounter_id;

  protected $cancercaredispatchTable;
    
	protected $serviceManager;
  
  protected $createdtime;
  
  public function __construct($serviceManager = null)
  {
    $this->serviceManager = $serviceManager;
  }
    
  public function indexAction()
  {
    $mirth_ip           = $this->getCancercaredispatchTable()->getSettings('Cancercare', 'cancercare_mirth_ip');
    $combination        = $this->getRequest()->getQuery('combination');
    $view               = $this->getRequest()->getQuery('view') ? $this->getRequest()->getQuery('view') : 0;
    $send               = $this->getRequest()->getQuery('send') ? $this->getRequest()->getQuery('send') : 0;
    $type               = 'cancer_care';

    if($combination != ''){
      $arr = explode('|', $combination);
      foreach($arr as $row){
        $arr = explode('_',$row);
        $this->patient_id   = $arr[0];
        $this->encounter_id = ($arr[1] > 0 ? $arr[1] : NULL);
        $this->create_cancercare_data($this->patient_id, $this->encounter_id,$send);
        $content            = $this->socket_get("$mirth_ip", "6662", $this->data);
        if($content=='Authetication Failure'){
          echo $content;
          die();
        }

        $to_replace = '<?xml version="1.0" encoding="UTF-8"?>
        <?xml-stylesheet type="text/xsl" href="cancer.xsl"?>
        <ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="urn:hl7-org:v3 http://xreg2.nist.gov:8080/hitspValidation/schema/cdar2c32/infrastructure/cda/C32_CDA.xsd"
        xmlns="urn:hl7-org:v3"
        xmlns:mif="urn:hl7-org:v3/mif">
        <!--';
        
        $content = preg_replace('/<ClinicalDocument.*><!--/', $to_replace, trim($content)); 
        $this->getCancercaredispatchTable()->logCancerData($this->patient_id, $this->encounter_id, base64_encode($content), $this->createdtime, 0, $_SESSION['authId'], $view, $send,$type);				
      }

      if(!$view)
        echo "Queued for Transfer";
      if($view){
        $xml = simplexml_load_string($content);
        $xsl = new \DOMDocument;
        $xsl->load(dirname(__FILE__).'/../../../../../public/css/cancer.xsl');
        $proc = new \XSLTProcessor;
        $proc->importStyleSheet($xsl); // attach the xsl rules
        $outputFile = sys_get_temp_dir() . '/out_'.time().'.html';
        $proc->transformToURI($xml, $outputFile);

        $htmlContent = file_get_contents($outputFile);
        echo htmlspecialchars_decode($htmlContent);
      }
    }
    else{
      $practice_filename  = "CancerCare_{$this->patient_id}.xml";
      $this->create_cancercare_data($this->patient_id, $this->encounter_id,$send);
      $content            = $this->socket_get("$mirth_ip", "6662", $this->data);
      $to_replace = '<?xml version="1.0" encoding="UTF-8"?>
      <?xml-stylesheet type="text/xsl" href="cancer.xsl"?>
      <ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="urn:hl7-org:v3 http://xreg2.nist.gov:8080/hitspValidation/schema/cdar2c32/infrastructure/cda/C32_CDA.xsd"
      xmlns="urn:hl7-org:v3"
      xmlns:mif="urn:hl7-org:v3/mif">
      <!--';
      
      $content = preg_replace('/<ClinicalDocument.*><!--/', $to_replace, trim($content));
      $this->getCancercaredispatchTable()->logCancerData($this->patient_id, $this->encounter_id, base64_encode($content), $this->createdtime, 0, $_SESSION['authId'], $view, $send,$type);
      echo $content;
      die;
    }        

    try{
      ob_clean();
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Disposition: attachment; filename=".$practice_filename);
      header("Content-Type: application/download");
      header("Content-Transfer-Encoding: binary");
      echo $content;
      exit;
    }
    catch(Exception $e){
      die('SOAP Error');
    }
  }
  
    public function create_cancercare_data($pid, $encounter,$send)
    {
      $username = $this->getCancercaredispatchTable()->getSettings('Cancercare', 'cancercare_mirth_username');
      $password = $this->getCancercaredispatchTable()->getSettings('Cancercare', 'cancercare_mirth_password');
      $client_id = $this->getCancercaredispatchTable()->getSettings('Cancercare', 'cancercare_mirth_clientid');
      $this->createdtime = time();
      
      $this->data .= "<CancerCare>";
      $this->data .= "<username>$username</username>";
      $this->data .= "<password>$password</password>";
      $this->data .= "<client_id>".$client_id."</client_id>";
      $this->data .= "<registry>test_registry</registry>";
      $this->data .= "<time>".$this->createdtime."</time>";
      $this->data .= "<send>".htmlspecialchars($send,ENT_QUOTES)."</send>";
      $this->data .= $this->getCancercaredispatchTable()->getPatientdata($pid,$encounter);
      $this->data .= $this->getCancercaredispatchTable()->getAuthor($pid);
      $this->data .= $this->getCancercaredispatchTable()->getCustodian();
      $this->data .= $this->getCancercaredispatchTable()->getParticipant($pid);
      $this->data .= $this->getCancercaredispatchTable()->getComponentOf($pid);
      $this->data .= $this->getCancercaredispatchTable()->getProgressNotes($pid);
      $this->data .= $this->getCancercaredispatchTable()->getProcedures($pid);
      $this->data .= $this->getCancercaredispatchTable()->getAdministeredMedications($pid);
      $this->data .= $this->getCancercaredispatchTable()->getCancerDiagnosis($pid);
      $this->data .= $this->getCancercaredispatchTable()->getProceduresSection($pid);
      $this->data .= $this->getCancercaredispatchTable()->getCodedSocialHistorySection($pid);
      $this->data .= $this->getCancercaredispatchTable()->getActiveProblems($pid);
      $this->data .= $this->getCancercaredispatchTable()->getCodedResultsSection($pid,$encounter);
      $this->data .= $this->getCancercaredispatchTable()->getCarePlanSection($pid,$encounter);
      $this->data .= $this->getCancercaredispatchTable()->getPayersSection($pid);
      $this->data .= $this->getCancercaredispatchTable()->getMedications($pid);
      $this->data .="</CancerCare>";
      $f123 = fopen(dirname(__FILE__)."/log.txt","w");    
      fwrite($f123, "<br><br>".$this->data);
      fclose($f123);
      
    }
    
    public function socket_get($ip, $port, $data)
    {
      $output = "";

      // Create a TCP Stream Socket
      $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if ($socket === false)
        throw new Exception("Socket Creation Failed");

      // Connect to the server.
      $result = socket_connect($socket, $ip, $port);
      if ($result === false)
        throw new Exception("Connection Failed");

      $data = chr(11).$data.chr(28)."\r";
      // Write to socket!
      $out = socket_write($socket, $data, strlen($data));

      //Read from socket!
      do {
        $line = "";
        $line = socket_read($socket, 1024, PHP_NORMAL_READ);
        $output .= $line;
      } while ($line != "");        

      $output = substr(trim($output),0,strlen($output)-3);
      // Close and return.
      socket_close($socket);
      return $output;
    }
    
    public function downloadAction()
    {
      $id         = $this->getRequest()->getQuery('id');
      $dir        = sys_get_temp_dir()."/Cancercare_$id/";
      $filename   = "Cancercare_$id.xml";
      if(!is_dir($dir)){
          mkdir($dir, true);
          chmod($dir, 0777);
      }

      $zip_dir    = sys_get_temp_dir()."/";
      $zip_name   = "Cancercare_$id.zip";

      $content    = $this->getCancercaredispatchTable()->getFile($id);        
      $f          = fopen($dir.$filename, "w");
      fwrite($f, $content);
      fclose($f);

      copy(dirname(__FILE__)."/../../../../../public/css/cancer.xsl", $dir."cancer.xsl");

      $zip = new Zip();
      $zip->setArchive($zip_dir.$zip_name);
      $zip->compress($dir);

      ob_clean();
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Disposition: attachment; filename=$zip_name");
      header("Content-Type: application/download");
      header("Content-Transfer-Encoding: binary");
      readfile($zip_dir.$zip_name);

      $view = new ViewModel();
      $view->setTerminal(true);
      return $view;
    }

        /**
  * Table Gateway
  * 
  * @return type
  */
  public function getCancercaredispatchTable()
  {
    if (!$this->cancercaredispatchTable) {
      if(($this->serviceManager == null))
        $this->serviceManager = $this->getServiceLocator();
      $this->cancercaredispatchTable = $this->serviceManager->get('Cancercare\Model\CancercaredispatchTable');
    }
    return $this->cancercaredispatchTable;
  }
}