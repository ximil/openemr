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

namespace Cancercare\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Model\ApplicationTable;
use Zend\Db\Adapter\Driver\Pdo\Result;

class CancercaredispatchTable extends AbstractTableGateway
{
  protected $sm;
  
	public function __construct($table_gateway,$sm)
	{
		$this->sm = $sm;
	}
  
  /*
  * Retrive the saved settings of the module from database
  *
  * @param    string      $module_directory       module directory name
  * @param    string      $field_name             field name as in the module_settings table
  */
  public function getSettings($module_directory, $field_name)
  {
      $query = "SELECT mo_conf.field_value 
                FROM modules AS mo 
                LEFT JOIN module_configuration AS mo_conf ON mo_conf.module_id = mo.mod_id
                WHERE mo.mod_directory = ? AND mo_conf.field_name = ?";
      $appTable   = new ApplicationTable();
      $result     = $appTable->zQuery($query, array($module_directory, $field_name));
      foreach($result as $row){
        return $row['field_value'];
      }
  }
  
  public function getRepresentedOrganization()
	{
		$query 		= "SELECT * FROM facility WHERE primary_business_entity = 1";
		$appTable   = new ApplicationTable();
		$res        = $appTable->zQuery($query,array());
		
		$records = array();
		foreach($res as $row){
			$records = $row;
		}
		return $records;
	}
    
  /*Fetch Patient data from EMR

  * @param    $pid
  * @param    $encounter
  * @return   $patient_data   Patient Data in XML format
  */
  public function getPatientdata($pid,$encounter)
  {
      $query      = "SELECT patient_data.*, l1.notes AS race_code, l1.title as race_title, 
                     l2.notes AS ethnicity_code,l2.title as ethnicity_title, l3.title as religion, 
                     l3.notes as religion_code, l4.title as marital_status, l4.notes as marital_status_code
                     FROM patient_data
                     LEFT JOIN list_options AS l1 ON l1.list_id=? AND l1.option_id=race
                     LEFT JOIN list_options AS l2 ON l2.list_id=? AND l2.option_id=ethnicity
                     LEFT JOIN list_options AS l3 ON l3.list_id=? AND l3.option_id=religion
                     LEFT JOIN list_options AS l4 on l4.list_id=? AND l4.option_id=status
                     WHERE pid=?";
      $appTable   = new ApplicationTable();
      $row        = $appTable->zQuery($query, array('race','ethnicity','religious_affiliation','marital',$pid));

      $provider_organization = $this->getProviderDetails($pid,$encounter);
      $usable_period_start = "20091002";
      $usable_period_end = "20130510";
      foreach($row as $result){
        $patient_data = "<patient>
                           <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                           <extension>".htmlspecialchars($pid.$result['id'], ENT_QUOTES)."</extension>
                           <usable_period_start>".htmlspecialchars($usable_period_start,ENT_QUOTES)."</usable_period_start>
                           <usable_period_end>".htmlspecialchars($usable_period_end,ENT_QUOTES)."</usable_period_end>
                           <fname>".htmlspecialchars($result['fname'],ENT_QUOTES)."</fname>
                           <mname>".htmlspecialchars($result['mname'],ENT_QUOTES)."</mname>
                           <lname>".htmlspecialchars($result['lname'],ENT_QUOTES)."</lname>
                           <home_phone>".htmlspecialchars(($result['phone_home'] ? $result['phone_home']: 0),ENT_QUOTES)."</home_phone>
                           <work_phone>".htmlspecialchars(($result['phone_biz'] ? $result['phone_biz']: 0),ENT_QUOTES)."</work_phone>
                           <street>".htmlspecialchars($result['street'],ENT_QUOTES)."</street>
                           <city>".htmlspecialchars($result['city'],ENT_QUOTES)."</city>
                           <state>".htmlspecialchars($result['state'],ENT_QUOTES)."</state>
                           <country>".htmlspecialchars($result['country_code'],ENT_QUOTES)."</country>
                           <postalCode>".htmlspecialchars($result['postal_code'],ENT_QUOTES)."</postalCode>
                           <birth_date>".htmlspecialchars(str_replace('-','',$result['DOB']),ENT_QUOTES)."</birth_date>
                           <gender>".htmlspecialchars($result['sex'],ENT_QUOTES)."</gender>
                           <gender_code>".htmlspecialchars(strtoupper(substr($result['sex'],0,1)),ENT_QUOTES)."</gender_code>
                           <marital_status>".htmlspecialchars($result['marital_status'],ENT_QUOTES)."</marital_status>
                           <marital_status_code>".htmlspecialchars(strtoupper(substr($result['marital_status_code'],0,1)),ENT_QUOTES)."</marital_status_code>
                           <race>".htmlspecialchars($result['race_title'],ENT_QUOTES)."</race>
                           <race_code>".htmlspecialchars($result['race_code'],ENT_QUOTES)."</race_code>
                           <ethnicity>".htmlspecialchars($result['ethnicity_title'],ENT_QUOTES)."</ethnicity>
                           <ethnicity_code>".htmlspecialchars($result['ethnicity_code'],ENT_QUOTES)."</ethnicity_code>
                           $provider_organization
                         </patient>";
      }

      return $patient_data;
  }
    
  public function getProviderDetails($pid,$encounter)
  {
    $provider_details = '';
    if(!$encounter){
        $query_enc = "SELECT encounter FROM form_encounter WHERE pid=? ORDER BY date DESC LIMIT 1";
        $appTable   = new ApplicationTable();
        $res_enc    = $appTable->zQuery($query_enc, array($pid));
        foreach($res_enc as $row_enc){
          $encounter = $row_enc['encounter'];
        }
    }

    $query      = "SELECT * FROM form_encounter
                   JOIN users AS u ON u.id = provider_id
                   JOIN facility AS f ON f.id = u.facility_id 
                   WHERE pid = ? AND encounter = ?";
    $appTable   = new ApplicationTable();
    $row        = $appTable->zQuery($query, array($pid,$encounter));
    $row_count  = $row->count();

    if($row_count > 0) {
      foreach($row as $result){
        $provider_details = "<provider_organization>
                               <root>".htmlspecialchars("2.16.840.1.113883.4.6", ENT_QUOTES)."</root>
                               <extension>".htmlspecialchars($pid.$result['id'], ENT_QUOTES)."</extension>
                               <name>".htmlspecialchars($result['name'],ENT_QUOTES)."</name>
                               <telecom>".htmlspecialchars(($result['phone'] ? $result['phone'] : 0),ENT_QUOTES)."</telecom>
                               <street>".htmlspecialchars($result['street'],ENT_QUOTES)."</street>
                               <city>".htmlspecialchars($result['city'],ENT_QUOTES)."</city>
                               <state>".htmlspecialchars($result['state'],ENT_QUOTES)."</state>
                               <country>".htmlspecialchars($result['country_code'],ENT_QUOTES)."</country>
                             </provider_organization>
                            ";
      }
      return $provider_details;
    }
    
  }
    
  public function getAuthor($pid)
  {
    global $representedOrganization;
    $author = '';
    $details = $this->getDetails('cancercare_author_id');
    $representedOrganization = $this->getRepresentedOrganization();
    $date = substr($details['date'],0,10);
    if($date != '' && $date != 0)
      $date = str_replace('-','',$date);
    else
      $date = '';
    $author = " <author>
                  <root>".htmlspecialchars("2.16.840.1.113883.4.6", ENT_QUOTES)."</root>
                  <extension>".htmlspecialchars($pid.$details['id'], ENT_QUOTES)."</extension>
                  <date>".htmlspecialchars($date,ENT_QUOTES)."</date>
                  <street>".htmlspecialchars($details['street'],ENT_QUOTES)."</street>
                  <city>".htmlspecialchars($details['city'],ENT_QUOTES)."</city>
                  <state>".htmlspecialchars($details['state'],ENT_QUOTES)."</state>
                  <country>".htmlspecialchars($details[''],ENT_QUOTES)."</country>
                  <postal_code>".htmlspecialchars($details['zip'],ENT_QUOTES)."</postal_code>   
                  <manufacturer_name>".htmlspecialchars($details['organization'],ENT_QUOTES)."</manufacturer_name>
                  <software_name>".htmlspecialchars('Blue EHR',ENT_QUOTES)."</software_name>
                  <telecom>".htmlspecialchars(($details['phonew1'] ? $details['phonew1'] : 0),ENT_QUOTES)."</telecom>
                  <representedOrganization>
                    <root>".htmlspecialchars("2.16.840.1.113883.4.6", ENT_QUOTES)."</root>
                    <name>".htmlspecialchars($representedOrganization['name'],ENT_QUOTES)."</name>
                    <telecom use='WP' value='".htmlspecialchars(($representedOrganization['phone'] ? $representedOrganization['phone'] : 0),ENT_QUOTES)."'/>
                    <street>".htmlspecialchars($representedOrganization['street'],ENT_QUOTES)."</street>
                    <city>".htmlspecialchars($representedOrganization['city'],ENT_QUOTES)."</city>
                    <state>".htmlspecialchars($representedOrganization['state'],ENT_QUOTES)."</state>
                    <country>".htmlspecialchars($representedOrganization['country_code'],ENT_QUOTES)."</country>
                    <postal_code>".htmlspecialchars($representedOrganization['postal_code'],ENT_QUOTES)."</postal_code>
                  </representedOrganization>
                </author>";

    return $author;
  }
    
  public function getDetails($field_name)
  {
    $query      = "SELECT u.id AS id, u.title, u.fname, u.mname, u.lname, u.street, u.city, u.state, u.zip, 
                   CONCAT_WS(' ','',u.phonew1) AS phonew1, u.organization, u.specialty, 
                   conf.field_name, mo.mod_name, mo.date
                   FROM users AS u
                   JOIN modules AS mo ON mo.mod_directory='Cancercare'
                   JOIN module_configuration AS conf ON conf.field_value=u.id AND mo.mod_id=conf.module_id
                   WHERE conf.field_name=?";        
    $appTable   = new ApplicationTable();
    $res        = $appTable->zQuery($query, array($field_name));
    foreach($res as $result){
      return $result;
    }
  }
    
  public function getCustodian()
  {
    $custodian = '';
    $details   = $this->getFacilities();

    $custodian = "<custodian>
                    <root>".htmlspecialchars("2.16.840.1.113883.4.6", ENT_QUOTES)."</root>
                    <name>".htmlspecialchars($details['name'],ENT_QUOTES)."</name>
                    <telecom>".htmlspecialchars(($details['phone'] ? $details['phone'] : 0),ENT_QUOTES)."</telecom>
                    <street>".htmlspecialchars($details['street'],ENT_QUOTES)."</street>
                    <city>".htmlspecialchars($details['city'],ENT_QUOTES)."</city>
                    <state>".htmlspecialchars($details['state'],ENT_QUOTES)."</state>
                    <country>".htmlspecialchars($details['country_code'],ENT_QUOTES)."</country>
                    <postal_code>".htmlspecialchars($details['postal_code'],ENT_QUOTES)."</postal_code>
                  </custodian>";

    return $custodian;
  }

  public function getFacilities()
  {
    $appTable   = new ApplicationTable();
    $res = $appTable->zQuery(("SELECT `id`, `name`, `phone`, `street`, `city`, `state`, `country_code`, `postal_code` FROM `facility`"));
    foreach($res as $row){
      return $row;
    }  
  }
    
  public function getParticipant($pid)
  {
    $participant = '';
    
    $query      = "SELECT id, street, city, state, country_code, postal_code, phone_home, title, fname,
                   lname, mname
                   FROM patient_data
                   WHERE pid=?";
    $appTable   = new ApplicationTable();
    $res        = $appTable->zQuery($query, array($pid));
    
    foreach($res as $row) {
      $participant = "<participant>
                        <root>".htmlspecialchars("4ff51570-83a9-47b7-91f2-93ba30373141", ENT_QUOTES)."</root>
                        <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                        <prefix>".htmlspecialchars($row['title'], ENT_QUOTES)."</prefix>
                        <fname>".htmlspecialchars($row['fname'], ENT_QUOTES)."</fname>
                        <lname>".htmlspecialchars($row['lname'], ENT_QUOTES)."</lname>
                        <mname>".htmlspecialchars($row['mname'], ENT_QUOTES)."</mname>
                        <telecom>".htmlspecialchars(($row['phone_home'] ? $row['phone_home'] : 0), ENT_QUOTES)."</telecom>
                        <street>".htmlspecialchars($row['street'], ENT_QUOTES)."</street>
                        <city>".htmlspecialchars($row['city'], ENT_QUOTES)."</city>
                        <state>".htmlspecialchars($row['state'], ENT_QUOTES)."</state>
                        <country>".htmlspecialchars($row['country_code'], ENT_QUOTES)."</country>
                        <postal_code>".htmlspecialchars($row['postal_code'], ENT_QUOTES)."</postal_code>
                      </participant>";
    }
    return $participant;
  }
    
  public function getComponentOf($pid)
  {
    $componentOf = '';
    
    $query ="SELECT u.* FROM users AS u 
             LEFT JOIN patient_data AS pd ON pd.`providerID`=u.`id`
             WHERE pd.pid=?";
    $appTable   = new ApplicationTable();
    $res        = $appTable->zQuery($query, array($pid));
    
    foreach($res as $row) {   
      $componentOf = "<componentof>
                        <root>".htmlspecialchars("2.16.840.1.113883.4.6", ENT_QUOTES)."</root>
                        <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                        <effective_time_start>".htmlspecialchars("20130311", ENT_QUOTES)."</effective_time_start>
                        <effective_time_end>".htmlspecialchars("20140517", ENT_QUOTES)."</effective_time_end>
                        <responsible_party>
                          <fname>".htmlspecialchars($row['fname'], ENT_QUOTES)."</fname>
                          <lname>".htmlspecialchars($row['lname'], ENT_QUOTES)."</lname>
                          <mname>".htmlspecialchars($row['mname'], ENT_QUOTES)."</mname>
                          <telecom>".htmlspecialchars(($row['phone'] ? $row['phone'] : 0), ENT_QUOTES)."</telecom>
                          <street>".htmlspecialchars($row['street'], ENT_QUOTES)."</street>
                          <city>".htmlspecialchars($row['city'], ENT_QUOTES)."</city>
                          <state>".htmlspecialchars($row['state'], ENT_QUOTES)."</state>
                          <country>".htmlspecialchars('', ENT_QUOTES)."</country>
                          <postal_code>".htmlspecialchars($row['zip'], ENT_QUOTES)."</postal_code>
                        </responsible_party>
                      </componentof>";
    }
    return $componentOf;
  }
    
  public function getProgressNotes($pid)
  {
    $progress_notes = '';
    $rowid          = '';
    
    $query          = "SELECT id, description FROM form_progress_notes WHERE pid=?";
    $appTable       = new ApplicationTable();
    $res            = $appTable->zQuery($query, array($pid));
    
    $progress_notes = "<progress_notes>
                          <root>".htmlspecialchars('2.16.840.1.113883.3.225', ENT_QUOTES)."</root>";
    
    foreach($res as $row) {
      $progress_notes .= " <text>".htmlspecialchars($row['description'], ENT_QUOTES)."</text>";
      $rowid          .= $row['id'];
    }
    $progress_notes .= "<extension>".htmlspecialchars($pid.$rowid, ENT_QUOTES)."</extension>
                        </progress_notes>";
    return $progress_notes;
  }
    
  public function getProcedures($pid)
  {
    $procedures = '';
    $rowid      = '';
    
    $query      = "SELECT id, description FROM form_procedure_notes WHERE pid=?";
    $appTable   = new ApplicationTable();
    $res        = $appTable->zQuery($query, array($pid));
    
    $procedures = "<procedures>
                      <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>";
    
    foreach($res as $row) {
      $procedures .= "<text>".htmlspecialchars($row['description'], ENT_QUOTES)."</text>";
      $rowid      .= $row['id'];
    }
    
    $procedures .= "<extension>".htmlspecialchars($pid.$rowid, ENT_QUOTES)."</extension>
                    </procedures>";
    return $procedures;
  }
    
  public function getAdministeredMedications($pid)
  { 
    $medications  = '';
    $query        = "SELECT l.start_date, l.drug, l.size, l.rxnorm_drugcode, l.route AS route_code, 
                     l3.title AS route, l2.title AS form, l.id, l.active
                     FROM prescriptions AS l
                     LEFT JOIN list_options AS l2 ON l2.option_id=form AND l2.list_id = ?
                     LEFT JOIN list_options AS l3 ON l3.option_id=route AND l3.list_id = ?
                     WHERE l.patient_id = ?";
    $appTable     = new ApplicationTable();
    $res          = $appTable->zQuery($query, array('drug_form','drug_route',$pid));

    $medications  = "<adm_medications>";
    foreach($res as $row){
      if(!$row['rxnorm_drugcode']){
        $row['rxnorm_drugcode'] = $this->generate_code($row['drug']);
      }
      if($row['start_date'] !='' && $row['start_date'] != 0) {
        $start_date           = str_replace('-','',$row['start_date']);
        $start_date_formatted = \Application\Model\ApplicationTable::fixDate($row['start_date'],$GLOBALS['date_display_format'],'yyyy-mm-dd');
      }
      else {
        $start_date           = 0;
        $start_date_formatted = '';
      }
      
      $status = 'completed';
      
      $medications .= "<adm_med>
                        <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                        <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                        <rxnorm>".htmlspecialchars($row['rxnorm_drugcode'],ENT_QUOTES)."</rxnorm>
                        <medication_name>".htmlspecialchars($row['drug'],ENT_QUOTES)."</medication_name>
                        <strength>".htmlspecialchars($row['size'],ENT_QUOTES)."</strength>
                        <form>".htmlspecialchars($row['form'],ENT_QUOTES)."</form>
                        <route>".htmlspecialchars($row['route'],ENT_QUOTES)."</route>
                        <route_code>".htmlspecialchars($row['route_code'],ENT_QUOTES)."</route_code>
                        <start_date>".htmlspecialchars($start_date,ENT_QUOTES)."</start_date>
                        <start_date_formatted>".htmlspecialchars($start_date_formatted,ENT_QUOTES)."</start_date_formatted>
                        <status>".htmlspecialchars($status,ENT_QUOTES)."</status>
                       </adm_med>
                      ";
    }
    $medications .= "</adm_medications>";
    return $medications;
  }
    
  /*
  * Generate CODE for medication, allergies etc.. if the code is not present by default.
  * The code is generated from the text that we give for medications or allergies.
  * 
  * The text is encrypted using SHA1() and the string is parsed. Alternate letters from the SHA1 string is fetched
  * and the result is again parsed. We again take the alternate letters from the string. This is done twice to reduce
  * duplicate codes beign generated from this function.
  *
  * @param	String		Code text
  *
  * @return	String		Code
  */
  public function generate_code($code_text)
  {
    $encrypted  = sha1($code_text);	
    $code 	    = '';
    for($i = 0; $i <= strlen($encrypted); ){
      $code  .= $encrypted[$i];
      $i 	    = $i+2;
    }
    $encrypted  = $code;
    $code 	    = '';
    for($i = 0; $i <= strlen($encrypted); ){
      $code  .= $encrypted[$i];
      $i 	    = $i+2;
    }
    $code 	    = strtoupper(substr($code, 0, 6));
    return $code;
  }
    
  public function getCancerDiagnosis($pid)
  {
    $cancer_diagnosis = '';
    $query            = "SELECT * FROM form_cancer_diagnosis
                         WHERE pid=? ORDER BY `date` DESC";
    $appTable         = new ApplicationTable();
    $res              = $appTable->zQuery($query, array($pid));
    
    $cancer_diagnosis = "<cancer_diagnosis>";
    foreach($res as $row) {   
      $laterality              = $this->getListTitle($row['laterality'],'Cancer_Diagnosis_Laterality');
      $behavior                = $this->getListTitle($row['behavior'],'Cancer_Diagnosis_Behavior');
      $diagnostic_confirmation = $this->getListTitle($row['diagnostic_confirmation'],'Cancer_Diagnosis_Confirmation');

      $laterality_code              = $this->getCodes($row['laterality'],'Cancer_Diagnosis_Laterality');
      $behavior_code                = $this->getCodes($row['behavior'],'Cancer_Diagnosis_Behavior');
      $diagnostic_confirmation_code = $this->getCodes($row['diagnostic_confirmation'],'Cancer_Diagnosis_Confirmation');
      
      if($row['date'] !='' && $row['date'] !=0) {
        $date           = str_replace('-','',$row['date']);
        $date_formatted = \Application\Model\ApplicationTable::fixDate($row['date'],$GLOBALS['date_display_format'],'yyyy-mm-dd');
      }
      else {
        $date           = 0;
        $date_formatted = '';
      }

      if($row['status'] == 1)
        $status = 'active';
      else
        $status = 'completed';
    
      $cancer_diagnosis .= " <diagnosis>
                               <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                               <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                               <date>".htmlspecialchars($date,ENT_QUOTES)."</date>
                               <date_formatted>".htmlspecialchars($date_formatted,ENT_QUOTES)."</date_formatted>
                               <site>".htmlspecialchars($row['primary_description'],ENT_QUOTES)."</site>
                               <site_code>".htmlspecialchars($row['primary_site'],ENT_QUOTES)."</site_code>
                               <laterality>".htmlspecialchars($laterality,ENT_QUOTES)."</laterality>
                               <laterality_code>".htmlspecialchars($laterality_code,ENT_QUOTES)."</laterality_code>
                               <histology>".htmlspecialchars($row['histology_description'],ENT_QUOTES)."</histology>
                               <histology_code>".htmlspecialchars($row['histology'],ENT_QUOTES)."</histology_code>
                               <behavior>".htmlspecialchars($behavior,ENT_QUOTES)."</behavior>
                               <behavior_code>".htmlspecialchars($behavior_code,ENT_QUOTES)."</behavior_code>
                               <diagnostic_confirmation>".htmlspecialchars($diagnostic_confirmation,ENT_QUOTES)."</diagnostic_confirmation>
                               <diagnostic_confirmation_code>".htmlspecialchars($diagnostic_confirmation_code,ENT_QUOTES)."</diagnostic_confirmation_code>
                               <stage>".htmlspecialchars($row['stage'],ENT_QUOTES)."</stage>
                               <status>".htmlspecialchars($status,ENT_QUOTES)."</status>
                             </diagnosis>";
    }                          
    $cancer_diagnosis .= "</cancer_diagnosis>";
    return $cancer_diagnosis;
  }

  public function getProceduresSection($pid)
  {
    $procedures_section = '';
    
    $query              = "SELECT id, code_text, `code`, `date` FROM billing WHERE pid=? AND activity=?
                           AND code_type=?";
    $appTable           = new ApplicationTable();
    $res                = $appTable->zQuery($query, array($pid,1,'CPT4'));
    
    $procedures_section = "<procedures_section>";
    
    foreach($res as $row) {
      if($row['date'] !='' && $row['date'] !=0) {
        $date           = str_replace('-','',substr($row['date'],0,10));
        $date_formatted = \Application\Model\ApplicationTable::fixDate(substr($row['date'],0,10),$GLOBALS['date_display_format'],'yyyy-mm-dd');  
      }
      else {
        $date           = 0;
        $date_formatted = '';
      }
      $procedures_section .= "  <procedure>
                                 <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                                 <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                                 <name>".htmlspecialchars($row['code_text'],ENT_QUOTES)."</name>
                                 <procedure_code>".htmlspecialchars($row['code'],ENT_QUOTES)."</procedure_code>
                                 <date>".htmlspecialchars($date,ENT_QUOTES)."</date>
                                 <date_formatted>".htmlspecialchars($date_formatted,ENT_QUOTES)."</date_formatted>
                                 <status>".htmlspecialchars('completed',ENT_QUOTES)."</status>
                               </procedure>";
    }
    $procedures_section .= "</procedures_section>";
    return $procedures_section;
  }
    
  public function getCodedSocialHistorySection($pid)
  {
    $coded_social_history_section = '';
    
    $arr_status                   = array(  'currenttobacco'    => 'Current',
                                            'quittobacco'       => 'Quit',
                                            'nevertobacco'      => 'Never'
                                         );
    
    $smoking_status_code          = array(
                                            '449868002'       => 'Current'	,
                                            '8517006'         => 'Quit' ,
                                            '266919005'       => 'Never' 
                                    );
        
    $query      = "SELECT hd.id,hd.tobacco, pd.occupation,pd.industry 
                   FROM history_data AS hd 
                   LEFT JOIN patient_data AS pd ON hd.pid=pd.pid
                   WHERE pd.pid=? ORDER BY hd.id DESC LIMIT 1";
    $appTable   = new ApplicationTable();
    $res        = $appTable->zQuery($query, array($pid));
    $res_cur    = $res->current();
    
    $tobacco         = explode('|',$res_cur['tobacco']);
    $industry        = $this->getListTitle($res_cur['industry'],'Industry');
    $industry_code   = $this->getCodes($res_cur['industry'],'Industry');
    $occupation      = $this->getListTitle($res_cur['occupation'],'Occupation');
    $occupation_code = $this->getCodes($res_cur['occupation'],'Occupation');
    
    foreach($arr_status as $key => $value) {
      if($tobacco[1] == $key) {
        $status = $value."  Smoker";
        foreach($smoking_status_code as $key2 => $value2) {
          if($value == $value2)
            $status_code = $key2;
        }
      }
    }
    $statusCode                    = 'completed';
    $coded_social_history_section  = "<social_history_section>";      
    $coded_social_history_section .= "<social_history>";
    $coded_social_history_section .= "  <occupation>".htmlspecialchars($occupation,ENT_QUOTES)."</occupation>
                                        <occupation_code>".htmlspecialchars($occupation_code,ENT_QUOTES)."</occupation_code>
                                        <industry>".htmlspecialchars($industry,ENT_QUOTES)."</industry>
                                        <industry_code>".htmlspecialchars($industry_code,ENT_QUOTES)."</industry_code>
                                        <smoking_status>".htmlspecialchars($status,ENT_QUOTES)."</smoking_status>
                                        <smoking_status_code>".htmlspecialchars($status_code,ENT_QUOTES)."</smoking_status_code>
                                        <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                                        <extension>".htmlspecialchars($pid.$res_cur['id'], ENT_QUOTES)."</extension>
                                        <status>".htmlspecialchars($statusCode, ENT_QUOTES)."</status>
                                      </social_history>";
    $coded_social_history_section .= "</social_history_section>";
    return $coded_social_history_section;  
  }
    
  public function getActiveProblems($pid)
  {
    require_once(dirname(__FILE__) . "/../../../../../../../../custom/code_types.inc.php");
    $active_problems = '';
    
    $query      = "SELECT l.*, lcode.icd9_code AS code,icd9.short_desc AS code_text
                   FROM lists AS l
                   LEFT JOIN list_codes AS lcode ON l.id=lcode.list_id
                   LEFT JOIN `icd9_dx_code` AS icd9 ON lcode.`icd9_code`=icd9.`formatted_dx_code`
                   WHERE l.type = ? AND l.pid = ?";
    $appTable   = new ApplicationTable();
		$res        = $appTable->zQuery($query, array('medical_problem',$pid));
        
    $active_problems = '<active_problems>';
    foreach($res as $row){
      $code           = $row['code'];
			$code_text      = $row['code_text'];
      
      if($row['begdate'] != '' && $row['begdate'] != 0) {
        $start_date           = str_replace('-','',$row['begdate']);  
        $start_date_formatted = \Application\Model\ApplicationTable::fixDate($row['begdate'],$GLOBALS['date_display_format'],'yyyy-mm-dd');
      }
      else {
        $start_date           = 0;
        $start_date_formatted = '';
      }
      
      if($row['enddate'] != '' && $row['enddate'] != 0) {
        $end_date           = str_replace('-','',$row['enddate']);  
      }
      else {
        $end_date           = 0;
      }
		
			if($row['activity'] == 0){
				$status 		= 'completed';
			}
			else{
				$status         = 'active';
			}
            
      $active_problems .= "<problem>			
                            <code_text>".htmlspecialchars($code_text,ENT_QUOTES)."</code_text>
                            <code>".htmlspecialchars($code,ENT_QUOTES)."</code>
                            <type>".htmlspecialchars('Problem',ENT_QUOTES)."</type>
                            <start_date>".htmlspecialchars($start_date,ENT_QUOTES)."</start_date>
                            <start_date_formatted>".htmlspecialchars($start_date_formatted,ENT_QUOTES)."</start_date_formatted>
                            <end_date>".htmlspecialchars($end_date,ENT_QUOTES)."</end_date>
                            <status>".$status."</status>
                            <root>".htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES)."</root>
                            <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                         </problem>";
    }
    $active_problems .= '</active_problems>';
    return $active_problems;
  }
    
  public function getCodedResultsSection($pid,$encounter)
  {
    $coded_results_section = '';
    
    $author                = $this->getAuthor($pid, $encounter);
    
    $query                 = "SELECT CONCAT_WS('',po.procedure_order_id,poc.`procedure_order_seq`) AS tcode, 
                                      psr.result_value AS result_value, psr.units, psr.range, 
                                      IF(psr.order_title!='', psr.order_title,poc.procedure_name ) AS order_title, 
                                      psr.subtest_code as result_code,psr.subtest_desc as result_desc, 
                                      psr.code_suffix AS test_code, po.date_ordered, psr.result_time AS result_time, 
                                      psr.abnormal_flag, psr.procedure_subtest_result_id AS result_id
                              FROM procedure_order AS po
                              JOIN procedure_order_code AS poc ON poc.`procedure_order_id`=po.`procedure_order_id`
                              JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND pr.`procedure_order_seq`=poc.`procedure_order_seq`
                              JOIN procedure_subtest_result AS psr ON psr.procedure_report_id = pr.procedure_report_id
                              WHERE po.patient_id = ? AND psr.result_value NOT IN ('DNR','TNP')
                                      UNION
                              SELECT CONCAT_WS('',po.procedure_order_id,poc.`procedure_order_seq`) AS tcode, 
                                     prs.result AS result_value, prs.units, prs.range, 
                                     IF(prs.order_title!='', prs.order_title,poc.procedure_name ) AS order_title, 
                                     prs.result_code as result_code, prs.result_text as result_desc, 
                                     prs.code_suffix AS test_code, po.date_ordered, prs.date AS result_time, 
                                     prs.abnormal AS abnormal_flag, prs.procedure_result_id AS result_id
                              FROM procedure_order AS po
                              JOIN procedure_order_code AS poc ON poc.`procedure_order_id`=po.`procedure_order_id`
                              JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND pr.`procedure_order_seq`=poc.`procedure_order_seq`
                              JOIN procedure_result AS prs ON prs.procedure_report_id = pr.procedure_report_id
                              WHERE po.patient_id = ? AND prs.result NOT IN ('DNR','TNP')";
    $appTable              = new ApplicationTable();
		$res                   = $appTable->zQuery($query, array($pid,$pid));
    $results_list = array();
    foreach($res as $row) {
      $results_list[$row['tcode']]['tcode'] = $row['tcode'];
      $results_list[$row['tcode']]['order_title'] = $row['order_title'];
      $results_list[$row['tcode']]['date_ordered'] = substr(str_replace('-', '', $row['date_ordered']), 0, 8);
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['result_code'] = $row['result_code'];
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['result_desc'] = $row['result_desc'];
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['units'] = $row['units'];
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['range'] = $row['range'];
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['result_value'] = $row['result_value'];
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['result_time'] = substr($row['result_time'], 0, 10);
      $results_list[$row['tcode']]['subtest'][$row['result_id']]['abnormal_flag'] = $row['abnormal_flag'];
    }
    
    $coded_results_section  = "<results_section>";
    
    foreach($results_list as $row){
      if($row['date_ordered'] != 0 && $row['date_ordered'] != '')
        $date_ordered = $row['date_ordered'];
      else
        $date_ordered = 0;
      $procedure_status = 'completed';
      $coded_results_section .= '<result_procedure>
                                    <procedure_name>'.htmlspecialchars($row['order_title'],ENT_QUOTES).'</procedure_name>
                                    <date_ordered>'.htmlspecialchars($date_ordered,ENT_QUOTES).'</date_ordered>
                                    <root>'.htmlspecialchars("2.16.840.1.113883.3.225", ENT_QUOTES).'</root>
                                    <extension>'.htmlspecialchars($pid.$row['id'], ENT_QUOTES).'</extension>
                                    <p_status>'.htmlspecialchars($procedure_status, ENT_QUOTES).'</p_status>';
                                    foreach($row['subtest'] as $row_1){ 
                                      $units = $row_1['units'] ? $row_1['units'] : 'Unit';
                                      
                                      if($row_1['result_time'] != '' && $row_1['result_time'] != '0000-00-00') {
                                        $observation_date           = str_replace('-','',$row_1['result_time']);
                                        $observation_date_formatted = \Application\Model\ApplicationTable::fixDate($row_1['result_time'],$GLOBALS['date_display_format'],'yyyy-mm-dd');
                                      }
                                      else {
                                        $observation_date           = 0;
                                        $observation_date_formatted = '';
                                      }
                                      $observation_status = 'completed';
                                      $coded_results_section .= '<result_observations>
                                                                    <result_code>'.htmlspecialchars($row_1['result_code'],ENT_QUOTES).'</result_code>
                                                                    <name>'.htmlspecialchars($row_1['result_desc'],ENT_QUOTES).'</name>
                                                                    <observation_date>'.htmlspecialchars($observation_date,ENT_QUOTES).'</observation_date>
                                                                    <observation_date_formatted>'.htmlspecialchars($observation_date_formatted,ENT_QUOTES).'</observation_date_formatted>
                                                                    <value>'.htmlspecialchars(($row_1['result_value'] ? $row_1['result_value'] : 0),ENT_QUOTES).'</value>
                                                                    <unit>'.htmlspecialchars($units,ENT_QUOTES).'</unit>
                                                                    <range>'.htmlspecialchars($row_1['range'],ENT_QUOTES).'</range>
                                                                    <abnormal_flag>'.htmlspecialchars($row_1['abnormal_flag'],ENT_QUOTES).'</abnormal_flag>
                                                                    <o_status>'.htmlspecialchars($observation_status,ENT_QUOTES).'</o_status>
                                                                    '.$author.'
                                                                 </result_observations>';
                                    }            
      $coded_results_section .= '</result_procedure>';
    }
    $coded_results_section .= '</results_section>';
    return $coded_results_section;
  }

  public function getCarePlanSection($pid,$encounter)
  {
    $care_plan_section = '';
    $care_plan_section = "<care_plan_section>
                            <care_plan>
                              <fname>".htmlspecialchars('Maxwell',ENT_QUOTES)."</fname>
                              <lname>".htmlspecialchars('WW',ENT_QUOTES)."</lname>
                              <telecom>".htmlspecialchars('7632455233',ENT_QUOTES)."</telecom>
                              <npi>".htmlspecialchars('1891813223',ENT_QUOTES)."</npi>
                              <root>".htmlspecialchars("7d5a02b0-67a4-11db-bd13-0800200c9a66", ENT_QUOTES)."</root>
                              <extension>".htmlspecialchars(base64_encode($_SESSION['site_id'].'666'), ENT_QUOTES)."</extension>
                            </care_plan>
                            <care_plan>
                              <fname>".htmlspecialchars('Maxwell',ENT_QUOTES)."</fname>
                              <lname>".htmlspecialchars('QQ',ENT_QUOTES)."</lname>
                              <telecom>".htmlspecialchars('7632455111',ENT_QUOTES)."</telecom>
                              <npi>".htmlspecialchars('1891813214',ENT_QUOTES)."</npi>
                              <root>".htmlspecialchars("7d5a02b0-67a4-11db-bd13-0800200c9a66", ENT_QUOTES)."</root>
                              <extension>".htmlspecialchars(base64_encode($_SESSION['site_id'].'777'), ENT_QUOTES)."</extension>
                            </care_plan>
                            <care_plan>
                              <fname>".htmlspecialchars('Maxwell',ENT_QUOTES)."</fname>
                              <lname>".htmlspecialchars('ZZ',ENT_QUOTES)."</lname>
                              <telecom>".htmlspecialchars('5671455111',ENT_QUOTES)."</telecom>
                              <npi>".htmlspecialchars('1891811234',ENT_QUOTES)."</npi>
                              <root>".htmlspecialchars("7d5a02b0-67a4-11db-bd13-0800200c9a66", ENT_QUOTES)."</root>
                              <extension>".htmlspecialchars(base64_encode($_SESSION['site_id'].'888'), ENT_QUOTES)."</extension>
                            </care_plan>
                          </care_plan_section>";
    return $care_plan_section;
  }
    
  public function getPayersSection($pid)
  {
    $payers_section = '';
    $appTable   = new ApplicationTable();
    
    $query2         = "SELECT DISTINCT provider FROM insurance_data WHERE pid=? AND provider <> 0 
                       AND provider <> ''";
    $res2           = $appTable->zQuery($query2, array($pid));
    foreach($res2 as $row2) {
      $provider     = $row2['provider'];
    
    
      $query          = "SELECT  DISTINCT ars.description, id.`subscriber_relationship`, 
                                 id.`policy_number`, id.`pid`, ad.`line1`, ad.`city`, ad.`state`, 
                                 ad.`country`, ad.`zip`, id.`subscriber_phone`, id.`subscriber_fname`, 
                                 id.`subscriber_lname`, id.`subscriber_mname`, aa.code,c.code_text, id.`id`
                         FROM ar_session AS ars  JOIN ar_activity AS aa ON ars.session_id = aa.session_id
                         JOIN insurance_data AS id ON ars.payer_id=id.`provider`
                         JOIN insurance_companies AS ic ON id.`provider`=ic.`id`
                         JOIN addresses AS ad ON ic.`id` = ad.`foreign_id`
                         JOIN codes AS c ON aa.code=c.code
                         WHERE id.`pid`=? AND id.`provider`=?";
      $res        = $appTable->zQuery($query, array($pid, $provider));

      $payers_section = "<payers_section>";
    
      foreach($res as $row) {
        $status = 'completed';
        
        $payers_section .= " <payer>
                               <company_name>".htmlspecialchars($row['description'],ENT_QUOTES)."</company_name>
                               <coverage_type>".htmlspecialchars($row['subscriber_relationship'],ENT_QUOTES)."</coverage_type>
                               <policy_id>".htmlspecialchars($row['policy_number'],ENT_QUOTES)."</policy_id>
                               <covered_party_id>".htmlspecialchars($row['pid'],ENT_QUOTES)."</covered_party_id>
                               <subscriber_name>
                                  <fname>".htmlspecialchars($row['subscriber_fname'],ENT_QUOTES)."</fname>
                                  <lname>".htmlspecialchars($row['subscriber_lname'],ENT_QUOTES)."</lname>
                                  <mname>".htmlspecialchars($row['subscriber_mname'],ENT_QUOTES)."</mname>
                               </subscriber_name>
                               <address>
                                  <street>".htmlspecialchars($row['line1'],ENT_QUOTES)."</street>
                                  <city>".htmlspecialchars($row['city'],ENT_QUOTES)."</city>
                                  <state>".htmlspecialchars($row['state'],ENT_QUOTES)."</state>
                                  <country>".htmlspecialchars($row['country'],ENT_QUOTES)."</country>
                                  <postal_code>".htmlspecialchars($row['zip'],ENT_QUOTES)."</postal_code>
                               </address>
                               <telecom>".htmlspecialchars(($row['subscriber_phone'] ? $row['subscriber_phone'] : 0),ENT_QUOTES)."</telecom>
                               <code>".htmlspecialchars($row['code'],ENT_QUOTES)."</code>
                               <code_text>".htmlspecialchars($row['code_text'],ENT_QUOTES)."</code_text>
                               <root>".htmlspecialchars('2.16.840.1.113883.3.225',ENT_QUOTES)."</root>
                               <extension>".htmlspecialchars($pid.$row['id'], ENT_QUOTES)."</extension>
                               <status>".htmlspecialchars($status,  ENT_QUOTES)."</status>
                               <pid>".htmlspecialchars($pid,  ENT_QUOTES)."</pid>
                             </payer>";
      }
      $payers_section .= "</payers_section>";
    }
    
    return $payers_section;
  }
  
  public function getMedications($pid)
  { 
    $medications  = '';
    $query        = "SELECT l.start_date, l.drug, l.dosage, l.size, l.rxnorm_drugcode,  
                     l2.title AS form, l.id
                     FROM prescriptions AS l
                     LEFT JOIN list_options AS l2 ON l2.option_id=form AND l2.list_id = ?
                     WHERE l.patient_id = ?";
    $appTable     = new ApplicationTable();
    $res          = $appTable->zQuery($query, array('drug_form',$pid));

    $medications  = "<medications>";
    foreach($res as $row){
      if(!$row['rxnorm_drugcode']){
        $row['rxnorm_drugcode'] = $this->generate_code($row['drug']);
      }
      if($row['start_date'] !='' && $row['start_date'] != 0) {
        $start_date           = str_replace('-','',$row['start_date']);
        $start_date_formatted = \Application\Model\ApplicationTable::fixDate($row['start_date'],$GLOBALS['date_display_format'],'yyyy-mm-dd');
      }
      else {
        $start_date           = 0;
        $start_date_formatted = '';
      }
      $status = 'completed';

      $medications .= "<med>
                        <root>".htmlspecialchars("7d5a02b0-67a4-11db-bd13-0800200c9a66", ENT_QUOTES)."</root>
                        <extension>".htmlspecialchars(base64_encode($_SESSION['site_id'].$row['id']), ENT_QUOTES)."</extension>
                        <rxnorm>".htmlspecialchars($row['rxnorm_drugcode'],ENT_QUOTES)."</rxnorm>
                        <medication_name>".htmlspecialchars($row['drug'],ENT_QUOTES)."</medication_name>
                        <strength>".htmlspecialchars($row['size'],ENT_QUOTES)."</strength>
                        <form>".htmlspecialchars($row['form'],ENT_QUOTES)."</form>
                        <dose>".htmlspecialchars($row['dosage'],ENT_QUOTES)."</dose>
                        <start_date>".htmlspecialchars($start_date,ENT_QUOTES)."</start_date>
                        <start_date_formatted>".htmlspecialchars($start_date_formatted,ENT_QUOTES)."</start_date_formatted>
                        <status>".htmlspecialchars($status,ENT_QUOTES)."</status>
                       </med>
                      ";
    }
    $medications .= "</medications>";
    return $medications;
  }
    
  /*
  * Store the status of the CancerData sent to HIE
  *
  * @param    integer     $pid
  * @param    integer     $encounter
  * @param    integer     $content
  * @param    integer     $time
  * @param    integer     $status
  * @return   None
  */
  public function logCancerData($pid, $encounter, $content, $time, $status, $user_id, $view = 0, $transfer = 0,$type='cancer_care')
  {
    $content    = base64_decode($content);
    $file_path	= '';
    $couch_id	= array();
    if($GLOBALS['document_storage_method']==1){
      $data = array(
        'data'      => base64_encode($content),
        'pid'       => $pid,
        'encounter' => $encounter,
        'mimetype'  => 'text/xml'
      );
      $couch    = \Documents\Plugin\Documents::couchDB();
      $couch_id = \Documents\Plugin\Documents::saveCouchDocument($couch, $data);
    }
    else{
      $file_path 	= $GLOBALS['OE_SITE_DIR'].'/documents/'.$pid;
      $file_name	= $pid."_".$encounter."_".$time.".xml";
      if(!is_dir($file_path)){
        mkdir($file_path, 0777, true);
      }
      $fccda = fopen($file_path."/".$file_name, "w");
      fwrite($fccda, $content);
      fclose($fccda);
      $file_path = $file_path."/".$file_name;
    }

    $query      = "INSERT INTO ccda (pid, encounter, ccda_data, time, status, user_id, couch_docid, couch_revid, view, transfer, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $appTable   = new ApplicationTable();
    $result     = $appTable->zQuery($query, array($pid, $encounter, $file_path, $time, $status, $user_id, $couch_id[0], $couch_id[1], $view, $transfer, $type));
    return;
  }
    
  public function getFile($id)
  {
    require_once(dirname(__FILE__) . "/../../../../../../../../library/classes/CouchDB.class.php");
    $query 	    = "SELECT couch_docid, couch_revid, ccda_data FROM ccda where id=? AND type=?";
    $appTable   = new ApplicationTable();
    $result     = $appTable->zQuery($query, array($id,'cancer_care'));
    foreach($result as $row){
      if($row['couch_docid'] != ''){
        $couch 	 = new CouchDB();
        $data 	 = array($GLOBALS['couchdb_dbase'], $row['couch_docid']);
        $resp 	 = $couch->retrieve_doc($data);
        $content = base64_decode($resp->data);
      }
      else if(!$row['couch_docid']){
        $fccda 	 = fopen($row['ccda_data'], "r");		
        $content = fread($fccda, filesize($row['ccda_data']));
        fclose($fccda);
      }
      else{
        $content = $row['ccda_data'];
      }
      return $content;
    }
  }
  
  public function getListTitle($option_id,$list_id)
  {
    $appTable  = new ApplicationTable();
    if($option_id) {
      $query   = "SELECT title 
                  FROM list_options 
                  WHERE list_id=? AND option_id=? AND activity=?";
      $result  = $appTable->zQuery($query,array($list_id,$option_id,1));
      $res_cur = $result->current();
    }
    return $res_cur['title'];
  }
  
  public function getCodes($option_id,$list_id)
  {
    $appTable  = new ApplicationTable();
    if($option_id) {
      $query   = "SELECT notes 
                  FROM list_options 
                  WHERE list_id=? AND option_id=? AND activity=?";
      $result  = $appTable->zQuery($query,array($list_id,$option_id,1));
      $res_cur = $result->current();
    }
    return $res_cur['notes'];
  }
}