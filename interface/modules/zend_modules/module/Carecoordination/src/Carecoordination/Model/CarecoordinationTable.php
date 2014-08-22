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
*    @author  Vinish K <vinish@zhservices.com>
*    @author  Chandni Babu <chandnib@zhservices.com> 
* +------------------------------------------------------------------------------+
*/
namespace Carecoordination\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Model\ApplicationTable;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\XmlRpc\Generator;

use DOMDocument;
use DOMXpath;

use Document;
use CouchDB;

use Documents\Model\DocumentsTable;

class CarecoordinationTable extends AbstractTableGateway
{
	protected $ccda_data_array;
	/*
	* Fetch the category ID using category name
	*
	* @param		$title		String		Category Name
	* @return		$records	Array		Category ID	
	*/
	public function fetch_cat_id($title)
	{
		$appTable   = new ApplicationTable();
		$query      = "SELECT * 
                   FROM categories 
                   WHERE name = ?";
		$result     = $appTable->zQuery($query, array($title));
		$records    = array();
		foreach($result as $row){
			$records[] = $row;
		}
		return $records;
	}
  
  /*
  * Fetch the documents uploaded by a user
  *
  * @param  user          Integer   Uploaded user ID
  * @param  time_start    Date      Uploaded start time
  * @param  time_end      Date      Uploaded end time
  *
  * @return records       Array     List of documents uploaded by the user during a particular time
  */
  public function fetch_uploaded_documents($data)
  {
    $query      = "SELECT * 
                   FROM categories_to_documents AS cat_doc
                   JOIN documents AS doc 
                   ON doc.id = cat_doc.document_id AND doc.owner = ? AND doc.date BETWEEN ? AND ?";
    $appTable   = new ApplicationTable();
    $result     = $appTable->zQuery($query, array($data['user'], $data['time_start'], $data['time_end']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
	
	/*
  * List the documents uploaded by the user alogn with the matched data
  *
  * @param    cat_title   Text    Category Name
  * @return   records     Array   List of CCDA imported to the system, pending approval
  */
	public function document_fetch($data)
	{
    $query      =   "SELECT am.id as amid, 
                        cat.name, 
                        u.fname, 
                        u.lname, 
                        d.imported, 
                        d.size, 
                        d.date, 
                        d.couch_docid, 
                        d.couch_revid, 
                        d.url AS file_url, 
                        d.id AS document_id, 
                        ad.field_value, 
                        ad1.field_value, 
                        ad2.field_value, 
                        pd.pid, 
                        CONCAT(ad.field_value,' ',ad1.field_value) as pat_name, 
                        DATE(ad2.field_value) as dob, 
                        CONCAT_WS(' ',pd.lname, pd.fname) as matched_patient
                     FROM documents AS d
                     JOIN categories AS cat ON cat.name = ?
                     JOIN categories_to_documents AS cd ON cd.document_id = d.id AND cd.category_id = cat.id
                     LEFT JOIN audit_master AS am ON am.type = '12' AND am.approval_status = '1' AND d.audit_master_id = am.id
                     LEFT JOIN audit_details ad ON ad.audit_master_id = am.id AND ad.table_name = 'patient_data' AND ad.field_name = 'lname' 
                     LEFT JOIN audit_details ad1 ON ad1.audit_master_id = am.id AND ad1.table_name = 'patient_data' AND ad1.field_name = 'fname' 
                     LEFT JOIN audit_details ad2 ON ad2.audit_master_id = am.id AND ad2.table_name = 'patient_data' AND ad2.field_name = 'DOB' 
                     LEFT JOIN patient_data pd ON pd.lname = ad.field_value AND pd.fname = ad1.field_value AND pd.DOB = DATE(ad2.field_value)
                     LEFT JOIN users AS u ON u.id = d.owner
                     WHERE d.audit_master_approval_status = 1
                     ORDER BY date DESC";
    $appTable   = new ApplicationTable();
    $result     = $appTable->zQuery($query, array($data['cat_title']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
	}
	
	/*
	 * Fetch the component values from the CCDA XML
	 *
	 * @param	$components		Array of components
	*/
	public function import($xml,$document_id)
	{
		$components      = $xml['component']['structuredBody']['component']; 
		$components_oids = array(
            '2.16.840.1.113883.10.20.22.4.7' 	=> 'allergy',
            '2.16.840.1.113883.10.20.22.2.1' 	=> 'medication',
            '2.16.840.1.113883.10.20.22.2.1.1' 	=> 'medication',
            '2.16.840.1.113883.10.20.22.2.5.1' 	=> 'medical_problem',
            '2.16.840.1.113883.10.20.22.2.5' 	=> 'medical_problem',
            '2.16.840.1.113883.10.20.22.2.2' 	=> 'immunization',
            '2.16.840.1.113883.3.88.11.83.145' 	=> 'procedure',
            '2.16.840.1.113883.10.20.22.2.3.1' 	=> 'lab_result',
            '2.16.840.1.113883.10.20.22.2.3' 	=> 'lab_result',
            '2.16.840.1.113883.10.20.22.2.4.1' 	=> 'vital_sign',
            '2.16.840.1.113883.10.20.22.2.17' 	=> 'social_history',
            '2.16.840.1.113883.3.88.11.83.127' 	=> 'encounter',
        );
		
    for($i = 0 ; $i <= count($components) ; $i++){
      if(count($components[$i]['section']['templateId']) > 1){
        foreach($components[$i]['section']['templateId'] as $key_1 => $value_1){
          if($components_oids[$components[$i]['section']['templateId'][$key_1]['root']] != ''){
            $func_name = $components_oids[$components[$i]['section']['templateId'][$key_1]['root']];
            $this->$func_name($components[$i]);
          }
        }
      }
      else{
        if($components_oids[$components[$i]['section']['templateId']['root']] != ''){
          $func_name = $components_oids[$components[$i]['section']['templateId']['root']];
          $this->$func_name($components[$i]);
        }
      }
    }
		$audit_master_approval_status         = $this->ccda_data_array['approval_status'] 	= 1;
		$this->ccda_data_array['ip_address']  = $_SERVER['REMOTE_ADDR'];
		$this->ccda_data_array['type'] 				= '12';
    
		//Patient Details					
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['fname']        = $xml['recordTarget']['patientRole']['patient']['name']['given'][0];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['lname']        = $xml['recordTarget']['patientRole']['patient']['name']['family'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['DOB']          = $xml['recordTarget']['patientRole']['patient']['birthTime']['value'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['sex']          = $xml['recordTarget']['patientRole']['patient']['administrativeGenderCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['pubpid']       = $xml['recordTarget']['patientRole']['id'][0]['extension'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['street']       = $xml['recordTarget']['patientRole']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['city']         = $xml['recordTarget']['patientRole']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['state']        = $xml['recordTarget']['patientRole']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['postal_code']  = $xml['recordTarget']['patientRole']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['country_code'] = $xml['recordTarget']['patientRole']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['phone_home'] 	= $xml['recordTarget']['patientRole']['telecom']['value'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['status']       = $xml['recordTarget']['patientRole']['patient']['maritalStatusCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['religion']   	= $xml['recordTarget']['patientRole']['patient']['religiousAffiliationCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['race']         = $xml['recordTarget']['patientRole']['patient']['raceCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['patient_data'][1]['ethnicity']    = $xml['recordTarget']['patientRole']['patient']['ethnicGroupCode']['displayName'];
		
		//Author details
		$this->ccda_data_array['field_name_value_array']['author'][1]['extension']          = $xml['author']['assignedAuthor']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['address']            = $xml['author']['assignedAuthor']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['city']               = $xml['author']['assignedAuthor']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['state']              = $xml['author']['assignedAuthor']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['zip']                = $xml['author']['assignedAuthor']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['country']            = $xml['author']['assignedAuthor']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['phone']              = $xml['author']['assignedAuthor']['telecom']['value'];
		$this->ccda_data_array['field_name_value_array']['author'][1]['name']               = $xml['author']['assignedAuthor']['assignedPerson']['name']['given'];
		
		//Data Enterer
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['extension']     = $xml['dataEnterer']['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['address']       = $xml['dataEnterer']['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['city']          = $xml['dataEnterer']['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['state']         = $xml['dataEnterer']['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['zip']           = $xml['dataEnterer']['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['country']       = $xml['dataEnterer']['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['phone']         = $xml['dataEnterer']['assignedEntity']['telecom']['value'];
		$this->ccda_data_array['field_name_value_array']['dataEnterer'][1]['name']          = $xml['dataEnterer']['assignedEntity']['assignedPerson']['name']['given'];
		
		//Informant
		$this->ccda_data_array['field_name_value_array']['informant'][1]['extension']       = $xml['informant'][0]['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['street']          = $xml['informant'][0]['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['city']            = $xml['informant'][0]['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['state']           = $xml['informant'][0]['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['postalCode']      = $xml['informant'][0]['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['country']         = $xml['informant'][0]['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['phone']           = $xml['informant'][0]['assignedEntity']['telecom']['value'];
		$this->ccda_data_array['field_name_value_array']['informant'][1]['name']            = $xml['informant'][0]['assignedEntity']['assignedPerson']['name']['given'];
		
		//Personal Informant
		$this->ccda_data_array['field_name_value_array']['custodian'][1]['extension']       = $xml['custodian']['assignedCustodian']['representedCustodianOrganization']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['custodian'][1]['organisation']    = $xml['custodian']['assignedCustodian']['representedCustodianOrganization']['name'];	
		
		$audit_master_id = \Application\Plugin\CommonPlugin::insert_ccr_into_audit_data($this->ccda_data_array);  
    $this->update_document_table($document_id,$audit_master_id,$audit_master_approval_status);
	}
  
  public function update_document_table($document_id,$audit_master_id,$audit_master_approval_status)
  { 
    $appTable   = new ApplicationTable();
    $query = "UPDATE documents 
              SET audit_master_id = ?,
                  imported = ?,
                  audit_master_approval_status=? 
              WHERE id = ?";
    $appTable->zQuery($query, array($audit_master_id, 
                                    1,
                                    $audit_master_approval_status,
                                    $document_id));
  }

  public function allergy($component)
	{
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_allergy_value($value);
			}
		}
		else{
			$this->fetch_allergy_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_allergy_value($allergy_array)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['lists2']) + 1;
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['type']            = 'allergy';
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['extension']       = $allergy_array['act']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['begdate']         = $allergy_array['act']['effectiveTime']['low']['value'];
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['enddate']         = $allergy_array['act']['effectiveTime']['high']['value'];	
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['list_code']       = $allergy_array['act']['entryRelationship']['observation']['code']['code'];
    $this->ccda_data_array['field_name_value_array']['lists2'][$i]['list_code_text']  = $allergy_array['act']['entryRelationship']['observation']['code']['displayName'];
		$this->ccda_data_array['field_name_value_array']['lists2'][$i]['outcome']         = $allergy_array['act']['entryRelationship']['observation']['entryRelationship'][1]['observation']['value']['code'];
    $this->ccda_data_array['field_name_value_array']['lists2'][$i]['severity_al']     = $allergy_array['act']['entryRelationship']['observation']['entryRelationship'][2]['observation']['value']['code'];
    $this->ccda_data_array['field_name_value_array']['lists2'][$i]['status']          = $allergy_array['act']['entryRelationship']['observation']['entryRelationship'][0]['observation']['value']['displayName'];
    $this->ccda_data_array['entry_identification_array']['lists2'][$i]                = $i;
		unset($allergy_array);
		return;
    
    //$snomed_text 	= $allergy_array['act']['id']['entryRelationship']['observation']['code']['displayName'];		
		//$rxnorm_code	= $allergy_array['act']['id']['entryRelationship']['observation']['participant']['participantRole']['playingEntity']['code']['code'];
		//$rxnorm_text	= $allergy_array['act']['id']['entryRelationship']['observation']['participant']['participantRole']['playingEntity']['code']['displayName'];	
		//$status_code	= $allergy_array['act']['id']['entryRelationship']['observation']['participant']['entryRelationship'][0]['observation']['value']['code'];
		//$observation_code = $allergy_array['act']['id']['entryRelationship']['observation']['participant']['entryRelationship'][1]['observation']['value']['code'];
		//$observation_text = $allergy_array['act']['id']['entryRelationship']['observation']['participant']['entryRelationship'][1]['observation']['value']['displayName'];
		//$severity_code	= $allergy_array['act']['id']['entryRelationship']['observation']['participant']['entryRelationship'][2]['observation']['value']['code'];
		//$severity_text	= $allergy_array['act']['id']['entryRelationship']['observation']['participant']['entryRelationship'][2]['observation']['value']['displayName'];	
	}
	
	public function medication($component)
	{
		$component['section']['text'] = '';		
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_medication_value($value);
			}
		}
		else{
			$this->fetch_medication_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_medication_value($medication_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['lists3']) + 1;
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['type']                = 'medication';
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['extension']           = $medication_data['substanceAdministration']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['root']                = $medication_data['substanceAdministration']['id']['root'];	
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['begdate']             = $medication_data['substanceAdministration']['effectiveTime'][0]['low']['value'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['enddate']             = $medication_data['substanceAdministration']['effectiveTime'][0]['high']['value'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['route']               = $medication_data['substanceAdministration']['routeCode']['code'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['route_display']       = $medication_data['substanceAdministration']['routeCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['dose']                = $medication_data['substanceAdministration']['doseQuantity']['value'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['rate']                = $medication_data['substanceAdministration']['rateQuantity']['value'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['rate_unit']           = $medication_data['substanceAdministration']['rateQuantity']['unit'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['drug_code']           = $medication_data['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['code']['code'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['drug_text']           = $medication_data['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['code']['displayName'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_npi']        = $medication_data['substanceAdministration']['performer']['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_name']       = $medication_data['substanceAdministration']['performer']['assignedEntity']['assignedPerson']['name']['given']." ".$medication_data['substanceAdministration']['performer']['assignedEntity']['assignedPerson']['name']['family'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_address']    = $medication_data['substanceAdministration']['performer']['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_city']       = $medication_data['substanceAdministration']['performer']['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_state']      = $medication_data['substanceAdministration']['performer']['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_postalCode']	= $medication_data['substanceAdministration']['performer']['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_country']    = $medication_data['substanceAdministration']['performer']['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['lists3'][$i]['provider_telecom']    = $medication_data['substanceAdministration']['performer']['assignedEntity']['telecom']['value'];
		$this->ccda_data_array['entry_identification_array']['lists3'][$i]                    = $i;
		unset($medication_data);
		return;
	}
	
	public function medical_problem($component)
	{
		$component['section']['text'] = '';		
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_medical_problem_value($value);
			}
		}
		else{
			$this->fetch_medical_problem_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_medical_problem_value($medical_problem_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['lists1']) + 1;
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['type']              = 'medical_problem';
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['extension']         = $medical_problem_data['act']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['root']              = $medical_problem_data['act']['id']['root'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['begdate']           = $medical_problem_data['act']['effectiveTime']['low']['value'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['enddate']           = $medical_problem_data['act']['effectiveTime']['high']['value'];		
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['list_code']         = $medical_problem_data['act']['entryRelationship']['observation']['value']['code'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['list_code_text']    = $medical_problem_data['act']['entryRelationship']['observation']['value']['displayName'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['observation']       = $medical_problem_data['act']['entryRelationship']['observation']['entryRelationship'][2]['observation']['value']['code'];
		$this->ccda_data_array['field_name_value_array']['lists1'][$i]['observation_text']  = $medical_problem_data['act']['entryRelationship']['observation']['entryRelationship'][2]['observation']['value']['displayName'];
    $this->ccda_data_array['field_name_value_array']['lists1'][$i]['status']            = $medical_problem_data['act']['entryRelationship']['observation']['entryRelationship'][0]['observation']['value']['displayName'];
		$this->ccda_data_array['entry_identification_array']['lists1'][$i]                  = $i;
		unset($medical_problem_data);
		return;
	}
	
	public function immunization($component)
	{
		$component['section']['text'] = '';		
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_immunization_value($value);
			}
		}
		else{
			$this->fetch_immunization_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_immunization_value($immunization_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['immunization']) + 1;
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['extension']                     = $immunization_data['substanceAdministration']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['root']                          = $immunization_data['substanceAdministration']['id']['root'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['administered_date']             = $immunization_data['substanceAdministration']['effectiveTime']['value'];	
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['route_code']                    = $immunization_data['substanceAdministration']['routeCode']['code'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['route_code_text']               = $immunization_data['substanceAdministration']['routeCode']['displayName'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['cvx_code']                      = $immunization_data['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['code']['code'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['cvx_code_text']                 = $immunization_data['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['code']['displayName'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_npi']                  = $immunization_data['substanceAdministration']['performer']['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_name']                 = $immunization_data['substanceAdministration']['performer']['assignedEntity']['assignedPerson']['name']['given'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_address']              = $immunization_data['substanceAdministration']['performer']['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_city']                 = $immunization_data['substanceAdministration']['performer']['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_state']                = $immunization_data['substanceAdministration']['performer']['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_postalCode']           = $immunization_data['substanceAdministration']['performer']['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_country']              = $immunization_data['substanceAdministration']['performer']['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['provider_telecom']              = $immunization_data['substanceAdministration']['performer']['assignedEntity']['telecom']['value'];	
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['represented_organization']      = $immunization_data['substanceAdministration']['performer']['assignedEntity']['representedOrganization']['name'];
		$this->ccda_data_array['field_name_value_array']['immunization'][$i]['represented_organization_tele']	= $immunization_data['substanceAdministration']['performer']['assignedEntity']['representedOrganization']['telecom'];
		$this->ccda_data_array['entry_identification_array']['immunization'][$i]                              = $i;
		unset($immunization_data);
		return;
	}
	
	public function procedure($component)
	{		
		$component['section']['text'] = '';
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				if($key%3 != 0) continue;//every third entry section has the procedure details
				$this->fetch_procedure_value($value);
			}
		}
		else{
			$this->fetch_procedure_value($component['section']['entry']['procedure']);
		}
		unset($component);
		return;
	}
	
	public function fetch_procedure_value($procedure_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['procedure']) + 1;
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['extension']                            = $procedure_data['procedure']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['root']                                 = $procedure_data['procedure']['id']['root'];	
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['code']                                 = $procedure_data['procedure']['code']['code'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['code_text']                            = $procedure_data['procedure']['code']['displayName'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['date']                                 = $procedure_data['procedure']['effectiveTime']['value'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_npi']                         = $procedure_data['procedure']['performer']['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_name']                        = $procedure_data['procedure']['performer']['assignedEntity']['assignedPerson']['name']['given'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_address']                     = $procedure_data['procedure']['performer']['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_city']                        = $procedure_data['procedure']['performer']['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_state']                       = $procedure_data['procedure']['performer']['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_postalCode']                  = $procedure_data['procedure']['performer']['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_country']                     = $procedure_data['procedure']['performer']['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['provider_telecom']                     = $procedure_data['procedure']['performer']['assignedEntity']['telecom']['value'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization']             = $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['name'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization_address']     = $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization_city']        = $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization_state']       = $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization_postalcode']  = $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['procedure'][$i]['represented_organization_country']     =  $procedure_data['procedure']['performer']['assignedEntity']['representedOrganization']['addr']['country'];
		$this->ccda_data_array['entry_identification_array']['procedure'][$i]                                     = $i;
		unset($procedure_data);
		return;
	}
	
	public function lab_result($component)
	{
		$component['section']['text'] = '';
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_lab_result_value($value);
			}
		}
		else{
			$this->fetch_lab_result_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_lab_result_value($lab_result_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['procedure_result']) + 1;
    foreach($lab_result_data['organizer']['component'] as $key => $value){
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['extension']           = $lab_result_data['organizer']['id']['extension'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['root']                = $lab_result_data['organizer']['id']['root'];	
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['proc_code']           = $lab_result_data['organizer']['code']['code'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['proc_text']           = $lab_result_data['organizer']['code']['displayName'];	
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['date']                = $lab_result_data['organizer']['effectiveTime']['value'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['status']              = $lab_result_data['organizer']['statusCode']['code'];
      if($key == 0) continue; //first array contains the procedure details which we have already fetched. so skipping the first array
      
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_extension']   = $value['observation']['id']['extension'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_root']        = $value['observation']['id']['root'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_code']        = $value['observation']['code']['code'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_text']        = $value['observation']['code']['displayName'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_date']        = $value['observation']['effectiveTime']['value'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_value']       = $value['observation']['value'];
      $this->ccda_data_array['field_name_value_array']['procedure_result'][$i]['results_range']       = $value['observation']['referenceRange']['observationRange']['text'];
      $this->ccda_data_array['entry_identification_array']['procedure_result'][$i]                    = $i;
      $i++;   
    }	
		unset($lab_result_data);
		return;
	}
	
	public function vital_sign($component)
	{
		$component['section']['text'] = '';
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_vital_sign_value($value);
			}
		}
		else{
			$this->fetch_vital_sign_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_vital_sign_value($vital_sign_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['vital_sign']) + 1;
		$this->ccda_data_array['field_name_value_array']['vital_sign'][$i]['extension'] = $vital_sign_data['organizer']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['vital_sign'][$i]['root']      = $vital_sign_data['organizer']['id']['root'];
		$this->ccda_data_array['field_name_value_array']['vital_sign'][$i]['date']      = $vital_sign_data['organizer']['component'][0]['observation']['effectiveTime']['value'];
		$vitals_array = array(
			'8310-5'	=> 'temperature',
			'8462-4'	=> 'bpd',
			'8480-6'	=> 'bps',
			'8287-5'	=> 'head_circ',
			'8867-4'	=> 'pulse',
			'8302-2'	=> 'height', 
			'2710-2'	=> 'oxygen_saturation',
			'9279-1'	=> 'respiration',
			'3141-9'	=> 'weight'
		);
		
		for($j=0 ; $j<9 ; $j++){
			$code = $vital_sign_data['organizer']['component'][$j]['observation']['code']['code'];
			$this->ccda_data_array['field_name_value_array']['vital_sign'][$i][$vitals_array[$code]]['extension'] = $vital_sign_data['organizer']['component'][$j]['observation']['id']['extension'];
			$this->ccda_data_array['field_name_value_array']['vital_sign'][$i][$vitals_array[$code]]['root']      = $vital_sign_data['organizer']['component'][$j]['observation']['id']['root'];
			$this->ccda_data_array['field_name_value_array']['vital_sign'][$i][$vitals_array[$code]]['date']      = $vital_sign_data['organizer']['component'][$j]['observation']['effectiveTime']['value'];
			$this->ccda_data_array['field_name_value_array']['vital_sign'][$i][$vitals_array[$code]]['value'] 		= $vital_sign_data['organizer']['component'][$j]['observation']['value']['value'];
			$this->ccda_data_array['field_name_value_array']['vital_sign'][$i][$vitals_array[$code]]['unit']      = $vital_sign_data['organizer']['component'][$j]['observation']['value']['unit'];
		}
		$this->ccda_data_array['entry_identification_array']['vital_sign'][$i] = $i;
		unset($vital_sign_data);
		return;
	}
	
	public function social_history($component)
	{
		$component['section']['text'] = '';
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_social_history_value($value);
			}
		}
		else{
			$this->fetch_social_history_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_social_history_value($social_history_data)
	{
		$social_history_array = array(
			'229819007'	=> 'smoking',
			'160573003'	=> 'alcohol',
		);
		$i    = 0;
		$code = $social_history_data['observation']['code']['code'];
		foreach($this->ccda_data_array['field_name_value_array']['social_history'] as $key => $value){
			if(!array_key_exists($social_history_array[$code], $value)){				
				$i = $key;
			}
			else{
				$i = count($this->ccda_data_array['field_name_value_array']['social_history']) + 1;
			}			
		}	
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['extension'] = $social_history_data['observation']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['root']      = $social_history_data['observation']['id']['root'];
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['status'] 		= $social_history_data['observation']['statusCode']['code'];
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['begdate']		= $social_history_data['observation']['effectiveTime']['low']['value'];
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['enddate']		= $social_history_data['observation']['effectiveTime']['high']['value'];
		$this->ccda_data_array['field_name_value_array']['social_history'][$i][$social_history_array[$code]]['value']     = $social_history_data['observation']['value'];
		$this->ccda_data_array['entry_identification_array']['social_history'][$i]                                        = $i;
		unset($social_history_data);
		return;
	}
	
	public function encounter($component)
	{
		$component['section']['text'] = '';
		if($component['section']['entry'][0]){
			foreach($component['section']['entry'] as $key => $value){
				$this->fetch_encounter_value($value);
			}
		}
		else{
			$this->fetch_encounter_value($component['section']['entry']);
		}
		unset($component);
		return;
	}
	
	public function fetch_encounter_value($encounter_data)
	{
		$i = count($this->ccda_data_array['field_name_value_array']['encounter']) + 1;
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['extension']                        = $encounter_data['encounter']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['root']                             = $encounter_data['encounter']['id']['root'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['date']                             = $encounter_data['encounter']['effectiveTime']['value'];	
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['code']                             = $encounter_data['encounter']['code']['code'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['code_text']                        = $encounter_data['encounter']['code']['displayName'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_npi']                     = $encounter_data['encounter']['performer']['assignedEntity']['id']['extension'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_name']                    = $encounter_data['encounter']['performer']['assignedEntity']['assignedPerson']['name']['given'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_address']                 = $encounter_data['encounter']['performer']['assignedEntity']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_city']                    = $encounter_data['encounter']['performer']['assignedEntity']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_state']                   = $encounter_data['encounter']['performer']['assignedEntity']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_postalCode']              = $encounter_data['encounter']['performer']['assignedEntity']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['provider_country']                 = $encounter_data['encounter']['performer']['assignedEntity']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_name']    = $encounter_data['encounter']['participant']['participantRole']['playingEntity']['name'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_address']	= $encounter_data['encounter']['participant']['participantRole']['addr']['streetAddressLine'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_city']    = $encounter_data['encounter']['participant']['participantRole']['addr']['city'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_state']   = $encounter_data['encounter']['participant']['participantRole']['addr']['state'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_zip']     = $encounter_data['encounter']['participant']['participantRole']['addr']['postalCode'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_country']	= $encounter_data['encounter']['participant']['participantRole']['addr']['country'];
		$this->ccda_data_array['field_name_value_array']['encounter'][$i]['represented_organization_telecom']	= $encounter_data['encounter']['participant']['participantRole']['telecom'];
		$this->ccda_data_array['entry_identification_array']['encounter'][$i]                                 = $i;
		unset($encounter_data);
		return;
	}
		
	/*
	* Fetch a document from the database
	*
	* @param	$document_id		Integer		Document ID
	* @return	$content			String		File content
	*/
	public function getDocument($document_id)
	{
		$content = \Documents\Plugin\Documents::getDocument($document_id);
		return $content;
	}
  
  public function getCategory()
  {
    $doc_obj  = new DocumentsTable();
    $category = $doc_obj->getCategory();
    return $category;
  }
  
  public function getIssues($pid)
  {
    $doc_obj  = new DocumentsTable();
    $issues   = $doc_obj->getIssues($pid);
    return $issues;
  }
  
  public function getCategoryIDs()
  {
    $doc_obj  = new DocumentsTable();
    $result   = implode("|",$doc_obj->getCategoryIDs(array('CCD','CCR','CCDA')));
    return $result;
  }
  
  /*
  * Fetch the demographics data from audit tables
  *
  * @param    audit_master_id   Integer   ID from audit master table
  * @return   records           Array     Demographics data
  */
  public function getDemographics($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT ad.id as adid, 
                          table_name, 
                          field_name, 
                          field_value 
                   FROM audit_master am 
                   JOIN audit_details ad ON ad.audit_master_id = am.id
                   WHERE am.id = ? AND ad.table_name = 'patient_data' 
                   ORDER BY ad.id";
    $result     = $appTable->zQuery($query, array($data['audit_master_id']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current demographics data of a patient from patient_data table
  *
  * @param    pid       Integer   Patient ID
  * @return   records   Array     current patient data
  */
  public function getDemographicsOld($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM patient_data 
                   WHERE pid = ?";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current Problems of a patient from lists table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of problems
  */
  public function getProblems($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM lists
                   WHERE pid = ? AND TYPE = 'medical_problem'";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current Allergies of a patient from lists table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of allergies
  */
  public function getAllergies($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM lists 
                   WHERE pid = ? AND TYPE = 'allergy'";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current Medications of a patient from prescriptions table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of medications
  */
  public function getMedications($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM prescriptions 
                   WHERE patient_id = ?";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current Immunizations of a patient from immunizations table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of immunizations
  */
  public function getImmunizations($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM immunizations 
                   WHERE patient_id = ?";//removed the field 'added_erroneously' from where condition
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the currect Lab Results of a patient
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of lab results
  */
  public function getLabResults($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT CONCAT_WS('',po.procedure_order_id,poc.`procedure_order_seq`) AS tcode, 
                          psr.result_value AS result_value, 
                          psr.units, 
                          psr.range, 
                          IF(psr.order_title!='', psr.order_title,poc.procedure_name ) AS order_title, 
                          psr.subtest_code as result_code,
                          psr.subtest_desc as result_desc, 
                          psr.code_suffix AS test_code, 
                          po.date_ordered, 
                          psr.result_time AS result_time, 
                          psr.abnormal_flag, 
                          psr.procedure_subtest_result_id AS result_id
                   FROM procedure_order AS po
                   JOIN procedure_order_code AS poc ON poc.`procedure_order_id`=po.`procedure_order_id`
                   JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id 
                        AND pr.`procedure_order_seq`=poc.`procedure_order_seq`
                   JOIN procedure_subtest_result AS psr ON psr.procedure_report_id = pr.procedure_report_id
                   WHERE po.patient_id = ? AND psr.result_value NOT IN ('DNR','TNP')
                            UNION
                   SELECT CONCAT_WS('',po.procedure_order_id,poc.`procedure_order_seq`) AS tcode, 
                          prs.result AS result_value, 
                          prs.units, prs.range, 
                          IF(prs.order_title!='', prs.order_title,poc.procedure_name ) AS order_title, 
                          prs.result_code as result_code,
                          prs.result_text as result_desc, 
                          prs.code_suffix AS test_code, 
                          po.date_ordered, 
                          prs.date AS result_time,
                          prs.abnormal AS abnormal_flag, 
                          prs.procedure_result_id AS result_id
                   FROM procedure_order AS po
                   JOIN procedure_order_code AS poc ON poc.`procedure_order_id`=po.`procedure_order_id`
                   JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id 
                        AND pr.`procedure_order_seq`=poc.`procedure_order_seq`
                   JOIN procedure_result AS prs ON prs.procedure_report_id = pr.procedure_report_id
                   WHERE po.patient_id = ? AND prs.result NOT IN ('DNR','TNP')";
    $result     = $appTable->zQuery($query, array($data['pid'],$data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the current Vitals of a patient from form_vitals table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       list of vitals
  */
  public function getVitals($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM form_vitals 
                   WHERE pid = ? AND activity=?";
    $result     = $appTable->zQuery($query, array($data['pid'],1));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the social history of a patient from history_data table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       history data
  */
  public function getSocialHistory($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT * 
                   FROM history_data 
                   WHERE pid=? 
                   ORDER BY id DESC LIMIT 1";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the encounter data of a patient from form_encounter table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       encounter data
  */
  public function getEncounterData($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "SELECT form_encounter.*,u.fname AS provider_name 
                   FROM form_encounter 
                   LEFT JOIN users AS u 
                   ON form_encounter.provider_id=u.id 
                   WHERE pid = ?";
    $result     = $appTable->zQuery($query, array($data['pid']));
    $records    = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }
  
  /*
  * Fetch the billing data of a patient from billing table
  *
  * @param    pid       Integer     patient id
  * @return   records   Array       billing data
  */
  public function getProcedure($data)
  {
    $appTable = new ApplicationTable();
    $query    = "SELECT *
                 FROM billing
                 WHERE pid=? AND activity=?";
    $result   = $appTable->zQuery($query,array($data['pid'],1));
    $records  = array();
    foreach($result as $row){
      $records[] = $row;
    }
    return $records;
  }

  /*
  * Fetch the data from audit tables
  *
  * @param    am_id         integer     audit master ID
  * @param    table_name    string      identifier inserted for each table (eg: prescriptions, list1 ...)
  */
  public function createAuditArray($am_id,$table_name)
  {
    $appTable     = new ApplicationTable();
    if(strpos($table_name,',')){
      $tables     = explode(',',$table_name);
      $arr        = array($am_id);
      $table_qry  = "";
      for($i = 0 ; $i < count($tables) ; $i++){
        $table_qry .= "?,";
        array_unshift($arr, $tables[$i]);
      }
      $table_qry  = substr($table_qry,0,-1);
      $query      = "SELECT * 
                     FROM audit_master am 
                     LEFT JOIN audit_details ad 
                     ON ad.audit_master_id = am.id 
                     AND ad.table_name IN ($table_qry) 
                     WHERE am.id = ? AND am.type = 12 AND am.approval_status = 1 
                     ORDER BY ad.entry_identification,ad.field_name";
      $result     = $appTable->zQuery($query, $arr);
    }
    else{
      $query        = "SELECT * 
                       FROM audit_master am 
                       LEFT JOIN audit_details ad 
                       ON ad.audit_master_id = am.id 
                       AND ad.table_name = ? 
                       WHERE am.id = ? AND am.type = 12 AND am.approval_status = 1 
                       ORDER BY ad.entry_identification,ad.field_name";
      $result       = $appTable->zQuery($query, array($table_name, $am_id));
    }
    $records = array();
    foreach($result as $res){
      $records[$table_name][$res['entry_identification']][$res['field_name']] = $res['field_value'];
    }
    return $records;
  }
  
  public function getListTitle($option_id='',$list_id,$codes='')
  {
    $appTable  = new ApplicationTable();
    if($option_id) {
      $query   = "SELECT title 
                  FROM list_options 
                  WHERE list_id=? AND option_id=? AND activity=?";
      $result  = $appTable->zQuery($query,array($list_id,$option_id,1));
      $res_cur = $result->current();
    }
    if($codes) {
      $query   = "SELECT title 
                  FROM list_options 
                  WHERE list_id=? AND (codes=? OR option_id=?) AND activity=?";
      $result  = $appTable->zQuery($query,array($list_id,$codes,$option_id,1));
      $res_cur = $result->current();
    } 
    return $res_cur['title'];
  }
  
  public function insertApprovedData($data)
  {
    $appTable            = new ApplicationTable();
    $patient_data_fields = '';
    $patient_data_values = array();
    $j                   = 1;
    $y                   = 1;
    $k                   = 1;
    $q                   = 1;
    $a                   = 1;
    $b                   = 1;
    $c                   = 1;
    $d                   = 1;
    
    $arr_procedure_res   = array();
    //$arr_procedures      = array();
    $arr_vitals          = array();
    $arr_encounter       = array();
    $arr_immunization    = array();
    $arr_prescriptions   = array();
    $arr_allergies       = array();
    $arr_med_pblm        = array();
    
    foreach($data as $key=>$val){   
      if(substr($key,-4) == '-sel'){ 
        if(is_array($val)){ 
          for($i=0;$i<count($val);$i++){
            if($val[$i] == 'insert'){ 
              if(substr($key,0,-4) == 'immunization'){
                $arr_immunization['immunization'][$a]['extension']                     = $data['immunization-extension'][$i];
                $arr_immunization['immunization'][$a]['administered_date']             = $data['immunization-administered_date'][$i];
                $arr_immunization['immunization'][$a]['route_code_text']               = $data['immunization-route_code_text'][$i];
                $arr_immunization['immunization'][$a]['cvx_code']                      = $data['immunization-cvx_code'][$i];
                $arr_immunization['immunization'][$a]['provider_npi']                  = $data['immunization-provider_npi'][$i];
                $arr_immunization['immunization'][$a]['provider_name']                 = $data['immunization-provider_name'][$i];
                $arr_immunization['immunization'][$a]['provider_address']              = $data['immunization-provider_address'][$i];
                $arr_immunization['immunization'][$a]['provider_city']                 = $data['immunization-provider_city'][$i];
                $arr_immunization['immunization'][$a]['provider_state']                = $data['immunization-provider_state'][$i];
                $arr_immunization['immunization'][$a]['provider_postalCode']           = $data['immunization-provider_postalCode'][$i];
                $arr_immunization['immunization'][$a]['provider_telecom']              = $data['immunization-provider_telecom'][$i];
                $arr_immunization['immunization'][$a]['represented_organization']      = $data['immunization-represented_organization'][$i];
                $arr_immunization['immunization'][$a]['represented_organization_tele'] = $data['immunization-represented_organization_tele'][$i];
                $a++; 
              }
              elseif(substr($key,0,-4) == 'lists3'){
                $arr_prescriptions['lists3'][$b]['extension']           = $data['lists3-extension'][$i];
                $arr_prescriptions['lists3'][$b]['begdate']             = $data['lists3-date_added'][$i];
                $arr_prescriptions['lists3'][$b]['route']               = $data['lists3-route'][$i];
                $arr_prescriptions['lists3'][$b]['route_display']       = $data['lists3-route_display'][$i];
                $arr_prescriptions['lists3'][$b]['dose']                = $data['lists3-dose'][$i];
                $arr_prescriptions['lists3'][$b]['rate']                = $data['lists3-size'][$i];
                $arr_prescriptions['lists3'][$b]['units']               = $data['lists3-units'][$i];
                $arr_prescriptions['lists3'][$b]['rate_unit']           = $data['lists3-rate_unit'][$i];
                $arr_prescriptions['lists3'][$b]['drug_code']           = $data['lists3-drugcode'][$i];
                $arr_prescriptions['lists3'][$b]['drug_text']           = $data['lists3-drug'][$i];
                $arr_prescriptions['lists3'][$b]['provider_npi']        = $data['lists3-provider_npi'][$i];
                $arr_prescriptions['lists3'][$b]['provider_name']       = $data['lists3-provider_name'][$i];
                $arr_prescriptions['lists3'][$b]['provider_address']    = $data['lists3-provider_address'][$i];
                $arr_prescriptions['lists3'][$b]['provider_city']       = $data['lists3-provider_city'][$i];
                $arr_prescriptions['lists3'][$b]['provider_state']      = $data['lists3-provider_state'][$i];
                $arr_prescriptions['lists3'][$b]['provider_postalCode'] = $data['lists3-provider_postalCode'][$i];
                $arr_prescriptions['lists3'][$b]['provider_telecom']    = $data['lists3-provider_telecom'][$i];
                $b++; 
              }
              elseif(substr($key,0,-4) == 'lists2'){
                $arr_allergies['lists2'][$c]['extension']      = $data['lists2-extension'][$i];
                $arr_allergies['lists2'][$c]['begdate']        = $data['lists2-begdate'][$i];
                $arr_allergies['lists2'][$c]['enddate']        = $data['lists2-enddate'][$i];
                $arr_allergies['lists2'][$c]['list_code']      = $data['lists2-diagnosis'][$i];
                $arr_allergies['lists2'][$c]['list_code_text'] = $data['lists2-title'][$i];
                $arr_allergies['lists2'][$c]['severity_al']    = $data['lists2-severity_al'][$i];
                $arr_allergies['lists2'][$c]['status']         = $data['lists2-activity'][$i];
                $c++;
              }
              else if(substr($key,0,-4) == 'lists1'){
                $arr_med_pblm['lists1'][$d]['extension']        = $data['lists1-extension'][$i];
                $arr_med_pblm['lists1'][$d]['begdate']          = $data['lists1-begdate'][$i];
                $arr_med_pblm['lists1'][$d]['enddate']          = $data['lists1-enddate'][$i];
                $arr_med_pblm['lists1'][$d]['list_code']        = $data['lists1-diagnosis'][$i];
                $arr_med_pblm['lists1'][$d]['list_code_text']   = $data['lists1-title'][$i];
                $arr_med_pblm['lists1'][$d]['status']           = $data['lists1-activity'][$i];
                $d++;
              }
              else if(substr($key,0,-4) == 'vital_sign'){ 
                $arr_vitals['vitals'][$q]['extension']         = $data['vital_sign-extension'][$i];
                $arr_vitals['vitals'][$q]['date']              = $data['vital_sign-date'][$i];
                $arr_vitals['vitals'][$q]['temperature']       = $data['vital_sign-temp'][$i];
                $arr_vitals['vitals'][$q]['bpd']               = $data['vital_sign-bpd'][$i];
                $arr_vitals['vitals'][$q]['bps']               = $data['vital_sign-bps'][$i];
                $arr_vitals['vitals'][$q]['head_circ']         = $data['vital_sign-head_circ'][$i];
                $arr_vitals['vitals'][$q]['pulse']             = $data['vital_sign-pulse'][$i];
                $arr_vitals['vitals'][$q]['height']            = $data['vital_sign-height'][$i];
                $arr_vitals['vitals'][$q]['oxygen_saturation'] = $data['vital_sign-oxy_sat'][$i];
                $arr_vitals['vitals'][$q]['respiration']       = $data['vital_sign-resp'][$i];
                $arr_vitals['vitals'][$q]['weight']            = $data['vital_sign-weight'][$i];
                $q++; 
              }
              else if(substr($key,0,-4) == 'social_history'){ 
                $tobacco = $data['social_history-tobacco_note'][$i]."|".
                           $data['social_history-tobacco_status'][$i]."|".
                           \Application\Model\ApplicationTable::fixDate($data['social_history-tobacco_date'][$i],'yyyy-mm-dd','dd/mm/yyyy');
                $alcohol = $data['social_history-alcohol_note'][$i]."|".
                           $data['social_history-alcohol_status'][$i]."|".
                           \Application\Model\ApplicationTable::fixDate($data['social_history-alcohol_date'][$i],'yyyy-mm-dd','dd/mm/yyyy');
                $query   = "INSERT INTO history_data 
                            ( pid,
                              tobacco,
                              alcohol
                            ) 
                            VALUES 
                            (
                              ?,
                              ?,
                              ?
                            )";
                $appTable->zQuery($query,array($data['pid'],
                                               $tobacco,
                                               $alcohol));
              }
              else if(substr($key,0,-4) == 'encounter'){
                $arr_encounter['encounter'][$k]['extension']                        = $data['encounter-extension'][$i];
                $arr_encounter['encounter'][$k]['date']                             = $data['encounter-date'][$i];
                $arr_encounter['encounter'][$k]['provider_npi']                     = $data['encounter-provider_npi'][$i];
                $arr_encounter['encounter'][$k]['provider_name']                    = $data['encounter-provider'][$i];
                $arr_encounter['encounter'][$k]['provider_address']                 = $data['encounter-provider_address'][$i];
                $arr_encounter['encounter'][$k]['provider_city']                    = $data['encounter-provider_city'][$i];
                $arr_encounter['encounter'][$k]['provider_state']                   = $data['encounter-provider_state'][$i];
                $arr_encounter['encounter'][$k]['provider_postalCode']              = $data['encounter-provider_postalCode'][$i];
                $arr_encounter['encounter'][$k]['represented_organization_name']    = $data['encounter-facility'][$i];
                $arr_encounter['encounter'][$k]['represented_organization_telecom'] = $data['encounter-represented_organization_telecom'][$i];
                $k++; 
              }
              else if(substr($key,0,-4) == 'procedure_result'){
                $arr_procedure_res['procedure_result'][$j]['proc_name']        = $data['procedure_result-proc_name'][$i];
                $arr_procedure_res['procedure_result'][$j]['proc_code']        = $data['procedure_result-proc_code'][$i];
                $arr_procedure_res['procedure_result'][$j]['proc_extension']   = $data['procedure_result-proc_extension'][$i];
                $arr_procedure_res['procedure_result'][$j]['proc_date']        = $data['procedure_result-proc_date'][$i];
                $arr_procedure_res['procedure_result'][$j]['proc_status']      = $data['procedure_result-proc_status'][$i];
                $arr_procedure_res['procedure_result'][$j]['result']           = $data['procedure_result-result'][$i];
                $arr_procedure_res['procedure_result'][$j]['results_code']     = $data['procedure_result-results_code'][$i];
                $arr_procedure_res['procedure_result'][$j]['range']            = $data['procedure_result-range'][$i];
                $arr_procedure_res['procedure_result'][$j]['value']            = $data['procedure_result-value'][$i];
                $arr_procedure_res['procedure_result'][$j]['proc_result_date'] = $data['procedure_result-result_date'][$i];
                $j++;                
            }
            else if(substr($key,0,-4) == 'procedure'){
              
//              $arr_procedures['procedure'][$y]['extension']                = $data['procedure-extension'][$i];
//              $arr_procedures['procedure'][$y]['code']                     = $data['procedure-code'][$i];
//              $arr_procedures['procedure'][$y]['code_text']                = $data['procedure-code_text'][$i];
//              $arr_procedures['procedure'][$y]['date']                     = $data['procedure-date'][$i];
//              $arr_procedures['procedure'][$y]['provider_npi']             = $data['procedure-provider_npi'][$i];
//              $arr_procedures['procedure'][$y]['provider_name']            = $data['procedure-provider_name'][$i];
//              $arr_procedures['procedure'][$y]['provider_address']         = $data['procedure-provider_address'][$i];
//              $arr_procedures['procedure'][$y]['provider_city']            = $data['procedure-provider_city'][$i];
//              $arr_procedures['procedure'][$y]['provider_state']           = $data['procedure-provider_state'][$i];
//              $arr_procedures['procedure'][$y]['provider_postalCode']      = $data['procedure-provider_postalCode'][$i];
//              $arr_procedures['procedure'][$y]['provider_telecom']         = $data['procedure-provider_telecom'][$i];
//              $arr_procedures['procedure'][$y]['represented_organization'] = $data['procedure-represented_organization'][$i];
//              $y++;  
            }
          }
          elseif($val[$i] == 'update'){ 
            if(substr($key,0,-8) == 'lists1'){ 
              if($data['lists1-upd_activity'][$i] == 'Active'){
                $activity = 1;
              }
              elseif($data['lists1-upd_activity'][$i] == 'Inactive'){
                $activity = 0;
              }
              $query = "UPDATE lists 
                        SET title=?,
                            diagnosis=?,
                            activity=? 
                        WHERE pid=? AND diagnosis=?";
              $appTable->zQuery($query,array($data['lists1-upd_title'][$i],
                                             $data['lists1-upd_diagnosis'][$i], 
                                             $activity, 
                                             $data['pid'], 
                                             $data['lists1-old-diagnosis'][$i]));
            }
          }
        }
      }
      else 
        if(substr($key,0,12) == 'patient_data'){ 
          if($val == 'update'){ 
            $var_name             = substr($key,0,-4); 
            $field_name           = substr($var_name,13);
            $patient_data_fields .= $field_name.'=?,';
            array_push($patient_data_values,$data[$var_name]);
          }
        }
      }
    }
      
    if(count($patient_data_values) > 0){
      array_push($patient_data_values,$data['pid']); 
      $patient_data_fields = substr($patient_data_fields,0,-1); 
      $query               = "UPDATE patient_data SET $patient_data_fields WHERE pid=?";
      $appTable->zQuery($query, $patient_data_values);
    }
    $appTable->zQuery("UPDATE documents 
                       SET foreign_id = ? 
                       WHERE id =? ", array($data['pid'], 
                                            $data['document_id']));    
    $appTable->zQuery("UPDATE audit_master 
                       SET approval_status = '2' 
                       WHERE id=?", array($data['amid']));
    $appTable->zQuery("UPDATE documents 
                       SET audit_master_approval_status=2 
                       WHERE audit_master_id=?", array($data['amid']));
    
    $this->InsertEncounter($arr_encounter['encounter'],$data['pid'],1);
    $this->InsertVitals($arr_vitals['vitals'],$data['pid'],1);
    //$this->InsertProcedures($arr_procedures['procedure'],$data['pid'],1); 
    $lab_results = $this->buildLabArray($arr_procedure_res['procedure_result']);   
    $this->InsertLabResults($lab_results,$data['pid']);
    $this->InsertImmunization($arr_immunization['immunization'],$data['pid'],1);
    $this->InsertPrescriptions($arr_prescriptions['lists3'],$data['pid'],1);
    $this->InsertAllergies($arr_allergies['lists2'],$data['pid'],1);
    $this->InsertMedicalProblem($arr_med_pblm['lists1'],$data['pid'],1);
  }
  
  public function discardCCDAData($data)
  {
    $appTable   = new ApplicationTable();
    $query      = "UPDATE audit_master 
                   SET approval_status = '3' 
                   WHERE id=?";
    $appTable->zQuery($query, array($data['audit_master_id']));
    $appTable->zQuery("UPDATE documents 
                      SET audit_master_approval_status='3' 
                      WHERE audit_master_id=?",array($data['audit_master_id']));
  }
  
  public function buildLabArray($lab_array) 
  {
    $lab_results = array();
    $j           = 0;
    foreach($lab_array as $key=>$value) { 
      $j = count($lab_results[$value['proc_extension']]['result']) + 1; 
      $lab_results[$value['proc_extension']]['proc_name']                   = $value['proc_name'];
      $lab_results[$value['proc_extension']]['proc_date']                   = $value['proc_date'];
      $lab_results[$value['proc_extension']]['proc_code']                   = $value['proc_code'];
      $lab_results[$value['proc_extension']]['proc_extension']              = $value['proc_extension'];
      $lab_results[$value['proc_extension']]['status']                      = $value['proc_status'];
      $lab_results[$value['proc_extension']]['result'][$j]['results_date']  = $value['proc_result_date'];
      $lab_results[$value['proc_extension']]['result'][$j]['results_text']  = $value['result'];
      $lab_results[$value['proc_extension']]['result'][$j]['results_value'] = $value['value'];
      $lab_results[$value['proc_extension']]['result'][$j]['results_range'] = $value['range'];
      $lab_results[$value['proc_extension']]['result'][$j]['results_code']  = $value['results_code'];
    }
    return $lab_results;
  }
  
  public function InsertLabResults($lab_results,$pid)
  {
    $appTable = new ApplicationTable();
    foreach($lab_results as $key=>$value) {
      $q              = "SELECT * 
                         FROM procedure_type 
                         WHERE `name`=?";
      $r              = $appTable->zQuery($q,array($value['proc_name']));
      $r_count        = $r->count();
      $procedure_code = preg_replace("/[^0-9]/", " ",$value['proc_code']);
      if($r_count == 0) {                
        $query_procedure_type = "INSERT INTO procedure_type
                                 ( name,
                                   lab_id,
                                   procedure_code,
                                   procedure_type
                                 )
                                 VALUES
                                 (
                                   ?,
                                   1005,
                                   ?,
                                   'ord'
                                 )";
        $appTable->zQuery($query_procedure_type,array($value['proc_name'],
                                                      $procedure_code));
      }
      $query     = "SELECT * 
                    FROM procedure_order 
                    WHERE external_id=?";
      $result    = $appTable->zQuery($query,array($value['proc_extension']));
      $res_count = $result->count();
      if($res_count == 0) {
        $enc     = $appTable->zQuery("SELECT encounter 
                                      FROM form_encounter 
                                      WHERE pid=? 
                                      ORDER BY id DESC LIMIT 1",array($pid));
        $enc_cur = $enc->current();
        $enc_id  = $enc_cur['encounter'];
        $query1  = "INSERT INTO procedure_order 
                   ( patient_id,
                     encounter_id,
                     date_collected,
                     date_ordered,
                     order_priority,
                     order_status,
                     lab_id,
                     external_id
                   ) 
                   VALUES
                   (
                     ?,
                     ?,
                     ?,
                     ?,
                     'normal',
                     ?,
                     1005,
                     ?
                   )";
        $result1 = $appTable->zQuery($query1,array($pid,
                                                   $enc_id,
                                                   \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),                     
                                                   \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                   $value['status'], 
                                                   $value['proc_extension']));
        $poc_id  = $result1->getGeneratedValue();


        $query2  = "INSERT INTO procedure_order_code
                   ( procedure_order_id,
                     procedure_order_seq,
                     procedure_code,
                     procedure_name
                   )
                   VALUES
                   (
                     ?,
                     1,
                     ?,
                     ?
                   )";
        $result2 = $appTable->zQuery($query2,array($poc_id,
                                                   $procedure_code,
                                                   $value['proc_name']));

        $query3  = "INSERT INTO procedure_report
                   ( procedure_order_id,
                     procedure_order_seq,
                     date_collected,
                     date_report
                   )
                   VALUES
                   (
                     ?,
                     1,
                     ?,
                     ?
                   )";
        $result3 = $appTable->zQuery($query3,array($poc_id,
                                                   \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                   \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy')));
        $proc_report_id = $result3->getGeneratedValue();
      }
      else {
        $appTable->zQuery("UPDATE procedure_order
                           SET date_ordered=?,
                           date_collected=?
                           WHERE external_id=?",array(\Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                      \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                      $value['proc_extension']));

        $res2          = $appTable->zQuery("SELECT procedure_order_id 
                           FROM procedure_order
                           WHERE external_id=?",array($value['proc_extension']));
        $res2_cur      = $res2->current();
        $proc_order_id = $res2_cur['procedure_order_id'];

        $appTable->zQuery("UPDATE procedure_order_code
                           SET procedure_name=?
                           WHERE procedure_order_id=?",array($value['proc_name'],
                                                             $proc_order_id));

        $appTable->zQuery("UPDATE procedure_report
                           SET date_collected=?,
                           date_report=?
                           WHERE procedure_order_id=?",array(\Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                             \Application\Model\ApplicationTable::fixDate($value['proc_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                             $proc_order_id));
        
        $res            = $appTable->zQuery("SELECT procedure_report_id
                                  FROM procedure_report
                                  WHERE procedure_order_id=?",array($proc_order_id));
        $res_cur        = $res->current();
        $proc_report_id = $res_cur['procedure_report_id'];
      }
      if(count($value['result']) > 1) {
        foreach($value['result'] as $key1=>$value1 ) {
          $proc_result       = explode(" ",$value1['results_value']);
          $proc_result_value = $proc_result[0] ? $proc_result[0] : 0;
          $proc_result_unit  = $proc_result[1] ? $proc_result[1] : '';
          $results_range     = $value1['results_range'] ? $value1['results_range'] : 0;
          $results_code      = $value1['results_code'] ? $value1['results_code'] : 0;
          $results_text      = $value1['results_text'] ? $value1['results_text'] : '';
          $query5 = "INSERT INTO procedure_subtest_result
                   ( procedure_report_id,
                     units,
                     result_value,
                     `range`,
                     subtest_code,
                     subtest_desc,
                     result_time,
                     order_title,
                     code_suffix
                   )
                   VALUES
                   (
                     ?,
                     ?,
                     ?,
                     ?,
                     ?,
                     ?,
                     ?,
                     ?,
                     ?
                   )";
        $result5 = $appTable->zQuery($query5,array($proc_report_id,
                                                   $proc_result_unit,
                                                   $proc_result_value,
                                                   $results_range,
                                                   $results_code,
                                                   $results_text,
                                                   \Application\Model\ApplicationTable::fixDate($value1['results_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                   $value['proc_name'],
                                                   $value['proc_code']));
        }    
      }
      else{
        foreach($value['result'] as $key1=>$value1 ) {
          $proc_result       = explode(" ",$value1['results_value']);
          $proc_result_value = $proc_result[0] ? $proc_result[0] : 0;
          $proc_result_unit  = $proc_result[1] ? $proc_result[1] : '';
          $results_range     = $value1['results_range'] ? $value1['results_range'] : 0;
          $results_code      = $value1['results_code'] ? $value1['results_code'] : 0;
          $results_text      = $value1['results_text'] ? $value1['results_text'] : '';
          $query6 = "INSERT INTO procedure_result
                     ( procedure_report_id,
                       date,
                       units,
                       result,
                       `range`,
                       result_code,
                       result_text,
                       order_title,
                       code_suffix
                     )
                     VALUES
                     (
                       ?,
                       ?,
                       ?,
                       ?,
                       ?,
                       ?,
                       ?,
                       ?,
                       ?
                     )";
          $result6 = $appTable->zQuery($query6,array($proc_report_id,
                                                     \Application\Model\ApplicationTable::fixDate($value1['results_date'],'yyyy-mm-dd','dd/mm/yyyy'),
                                                     $proc_result_unit,
                                                     $proc_result_value,
                                                     $results_range,
                                                     $results_code,
                                                     $results_text,
                                                     $value['proc_name'],
                                                     $value['proc_code']));
        }
      }        
    }
  }
  
  public function insert_patient($audit_master_id,$document_id)
  {
    require_once(dirname(__FILE__) . "/../../../../../../../../library/patient.inc");
    $pid = 0;
    $j   = 1;
    $k   = 1;
    $q   = 1;
    $y   = 1;
    $a   = 1;
    $b   = 1; 
    $c   = 1; 
    $d   = 1;
    
    $arr_procedure_res = array();
    $arr_encounter     = array();
    $arr_vitals        = array();
    //$arr_procedures    = array();
    $arr_immunization  = array();
    $arr_prescriptions = array(); 
    $arr_allergies     = array(); 
    $arr_med_pblm      = array();
    $appTable          = new ApplicationTable();
    
    $pres              = $appTable->zQuery("SELECT IFNULL(MAX(pid)+1,1) AS pid 
                                     FROM patient_data");
    foreach($pres as $prow){
      $pid      = $prow['pid'];
    }
    $res        = $appTable->zQuery("SELECT DISTINCT ad.table_name,
                                            entry_identification 
                                     FROM audit_master as am,audit_details as ad 
                                     WHERE am.id=ad.audit_master_id AND 
                                     am.approval_status = '1' AND 
                                     am.id=? AND am.type=12 
                                     ORDER BY ad.id", array($audit_master_id));
    $tablecnt   = $res->count();
    foreach($res as $row){
      $resfield = $appTable->zQuery("SELECT * 
                                     FROM audit_details 
                                     WHERE audit_master_id=? AND 
                                     table_name=? AND 
                                     entry_identification=?", array($audit_master_id,
                                                                    $row['table_name'],
                                                                    $row['entry_identification']));
      $table    = $row['table_name'];
      $newdata  = array();
      foreach($resfield as $rowfield){
        if($table == 'patient_data'){
          if($rowfield['field_name'] == 'DOB'){
             $dob = $this->formatDate($rowfield['field_value'],1); 
             $newdata['patient_data'][$rowfield['field_name']] = $dob;
          }
          else{
            if($rowfield['field_name'] == 'religion'){ 
              $religion_option_id = $this->getOptionId('religious_affiliation',$rowfield['field_value']);
              $newdata['patient_data'][$rowfield['field_name']] = $religion_option_id;
            }
            elseif($rowfield['field_name'] == 'race'){ 
              $race_option_id = $this->getOptionId('race',$rowfield['field_value']);
              $newdata['patient_data'][$rowfield['field_name']] = $race_option_id;
            }
            elseif($rowfield['field_name'] == 'ethnicity'){ 
              $ethnicity_option_id = $this->getOptionId('ethnicity',$rowfield['field_value']); 
              $newdata['patient_data'][$rowfield['field_name']] = $ethnicity_option_id;
            }
            else
              $newdata['patient_data'][$rowfield['field_name']] = $rowfield['field_value'];
          }
        }
        elseif($table == 'immunization'){
          $newdata['immunization'][$rowfield['field_name']]  = $rowfield['field_value'];
        }
        elseif($table == 'lists3'){
          $newdata['lists3'][$rowfield['field_name']]  = $rowfield['field_value'];
        }
        elseif($table == 'lists1'){
          $newdata['lists1'][$rowfield['field_name']] = $rowfield['field_value'];
        }
        elseif($table == 'lists2'){
          $newdata['lists2'][$rowfield['field_name']] = $rowfield['field_value'];
        }
        elseif($table == 'vital_sign') {
          $newdata['vital_sign'][$rowfield['field_name']] = $rowfield['field_value'];
        }
        elseif($table == 'social_history') {
          $newdata['social_history'][$rowfield['field_name']] = $rowfield['field_value'];
        }
        elseif($table == 'encounter') {
          $newdata['encounter'][$rowfield['field_name']] = $rowfield['field_value'];
        }
        elseif($table == 'procedure_result') {
          $newdata['procedure_result'][$rowfield['field_name']] = $rowfield['field_value'];  
        }
        elseif($table == 'procedure') {
          $newdata['procedure'][$rowfield['field_name']] = $rowfield['field_value'];  
        }
      }
      if($table == 'patient_data'){
        updatePatientData($pid,$newdata['patient_data'],true);
      }
      elseif($table == 'immunization'){
        $arr_immunization['immunization'][$a]['extension']                     = $newdata['immunization']['extension'];
        $arr_immunization['immunization'][$a]['administered_date']             = $newdata['immunization']['administered_date'];
        $arr_immunization['immunization'][$a]['route_code_text']               = $newdata['immunization']['route_code_text'];
        $arr_immunization['immunization'][$a]['cvx_code']                      = $newdata['immunization']['cvx_code'];
        $arr_immunization['immunization'][$a]['provider_npi']                  = $newdata['immunization']['provider_npi'];
        $arr_immunization['immunization'][$a]['provider_name']                 = $newdata['immunization']['provider_name'];
        $arr_immunization['immunization'][$a]['provider_address']              = $newdata['immunization']['provider_address'];
        $arr_immunization['immunization'][$a]['provider_city']                 = $newdata['immunization']['provider_city'];
        $arr_immunization['immunization'][$a]['provider_state']                = $newdata['immunization']['provider_state'];
        $arr_immunization['immunization'][$a]['provider_postalCode']           = $newdata['immunization']['provider_postalCode'];
        $arr_immunization['immunization'][$a]['provider_telecom']              = $newdata['immunization']['provider_telecom'];
        $arr_immunization['immunization'][$a]['represented_organization']      = $newdata['immunization']['represented_organization'];
        $arr_immunization['immunization'][$a]['represented_organization_tele'] = $newdata['immunization']['represented_organization_tele'];
        $a++; 
      }
      elseif($table == 'lists3'){
        $arr_prescriptions['lists3'][$b]['extension']           = $newdata['lists3']['extension'];
        $arr_prescriptions['lists3'][$b]['begdate']             = $newdata['lists3']['begdate'];
        $arr_prescriptions['lists3'][$b]['route']               = $newdata['lists3']['route'];
        $arr_prescriptions['lists3'][$b]['route_display']       = $newdata['lists3']['route_display'];
        $arr_prescriptions['lists3'][$b]['dose']                = $newdata['lists3']['dose'];
        $arr_prescriptions['lists3'][$b]['rate']                = $newdata['lists3']['rate'];
        $arr_prescriptions['lists3'][$b]['rate_unit']           = $newdata['lists3']['rate_unit'];
        $arr_prescriptions['lists3'][$b]['drug_code']           = $newdata['lists3']['drug_code'];
        $arr_prescriptions['lists3'][$b]['drug_text']           = $newdata['lists3']['drug_text'];
        $arr_prescriptions['lists3'][$b]['provider_npi']        = $newdata['lists3']['provider_npi'];
        $arr_prescriptions['lists3'][$b]['provider_name']       = $newdata['lists3']['provider_name'];
        $arr_prescriptions['lists3'][$b]['provider_address']    = $newdata['lists3']['provider_address'];
        $arr_prescriptions['lists3'][$b]['provider_city']       = $newdata['lists3']['provider_city'];
        $arr_prescriptions['lists3'][$b]['provider_state']      = $newdata['lists3']['provider_state'];
        $arr_prescriptions['lists3'][$b]['provider_postalCode'] = $newdata['lists3']['provider_postalCode'];
        $arr_prescriptions['lists3'][$b]['provider_telecom']    = $newdata['lists3']['provider_telecom'];
        $b++; 
      }
      elseif($table == 'lists1' && $newdata['lists1']['list_code'] !=0){
        $arr_med_pblm['lists1'][$d]['extension']        = $newdata['lists1']['extension'];
        $arr_med_pblm['lists1'][$d]['begdate']          = $newdata['lists1']['begdate'];
        $arr_med_pblm['lists1'][$d]['enddate']          = $newdata['lists1']['enddate'];
        $arr_med_pblm['lists1'][$d]['list_code']        = $newdata['lists1']['list_code'];
        $arr_med_pblm['lists1'][$d]['list_code_text']   = $newdata['lists1']['list_code_text'];
        $arr_med_pblm['lists1'][$d]['status']           = $newdata['lists1']['status'];
        $d++;
      }
      elseif($table == 'lists2' && $newdata['lists2']['list_code'] !=0){
        $arr_allergies['lists2'][$c]['extension']      = $newdata['lists2']['extension'];
        $arr_allergies['lists2'][$c]['begdate']        = $newdata['lists2']['begdate'];
        $arr_allergies['lists2'][$c]['enddate']        = $newdata['lists2']['enddate'];
        $arr_allergies['lists2'][$c]['list_code']      = $newdata['lists2']['diagnosis'];
        $arr_allergies['lists2'][$c]['list_code_text'] = $newdata['lists2']['title'];
        $arr_allergies['lists2'][$c]['severity_al']    = $newdata['lists2']['severity_al'];
        $arr_allergies['lists2'][$c]['status']         = $newdata['lists2']['activity'];
        $c++;
      }
      elseif($table == 'encounter') {
        $arr_encounter['encounter'][$k]['extension']                        = $newdata['encounter']['extension'];
        $arr_encounter['encounter'][$k]['date']                             = $newdata['encounter']['date'];
        $arr_encounter['encounter'][$k]['provider_npi']                     = $newdata['encounter']['provider_npi'];
        $arr_encounter['encounter'][$k]['provider_name']                    = $newdata['encounter']['provider_name'];
        $arr_encounter['encounter'][$k]['provider_address']                 = $newdata['encounter']['provider_address'];
        $arr_encounter['encounter'][$k]['provider_city']                    = $newdata['encounter']['provider_city'];
        $arr_encounter['encounter'][$k]['provider_state']                   = $newdata['encounter']['provider_state'];
        $arr_encounter['encounter'][$k]['provider_postalCode']              = $newdata['encounter']['provider_postalCode'];
        $arr_encounter['encounter'][$k]['represented_organization_name']    = $newdata['encounter']['represented_organization_name'];
        $arr_encounter['encounter'][$k]['represented_organization_telecom'] = $newdata['encounter']['represented_organization_telecom'];
        $k++; 
      }
      elseif($table == 'vital_sign') {
        $arr_vitals['vitals'][$q]['extension']         = $newdata['vital_sign']['extension'];
        $arr_vitals['vitals'][$q]['date']              = $newdata['vital_sign']['date'];
        $arr_vitals['vitals'][$q]['temperature']       = $newdata['vital_sign']['temperature'];
        $arr_vitals['vitals'][$q]['bpd']               = $newdata['vital_sign']['bpd'];
        $arr_vitals['vitals'][$q]['bps']               = $newdata['vital_sign']['bps'];
        $arr_vitals['vitals'][$q]['head_circ']         = $newdata['vital_sign']['head_circ'];
        $arr_vitals['vitals'][$q]['pulse']             = $newdata['vital_sign']['pulse'];
        $arr_vitals['vitals'][$q]['height']            = $newdata['vital_sign']['height'];
        $arr_vitals['vitals'][$q]['oxygen_saturation'] = $newdata['vital_sign']['oxygen_saturation'];
        $arr_vitals['vitals'][$q]['respiration']       = $newdata['vital_sign']['respiration'];
        $arr_vitals['vitals'][$q]['weight']            = $newdata['vital_sign']['weight'];
        $q++; 
      }
      elseif($table == 'social_history') {
        $tobacco_status = array(
          '449868002'       => 'Current'	,
          '8517006'         => 'Quit' ,
          '266919005'       => 'Never' 
        );
        $alcohol_status = array(
          '219006'          => 'Current',
          '82581004'        => 'Quit',
          '228274009'       => 'Never'  
        );
        $alcohol = explode("|",$newdata['social_history']['alcohol']);
        if($alcohol[2] !=0) {
          $alcohol_date = $this->formatDate($alcohol[2],1);
        }
        else {
          $alcohol_date = $alcohol[2];
        }
        $alcohol_date_value = fixDate($alcohol_date);
        foreach($alcohol_status as $key=>$value) {
          if($alcohol[1] == $key)
            $alcohol[1] = strtolower($value)."alcohol";
        }
        $alcohol_value = $alcohol[0]."|".$alcohol[1]."|".$alcohol_date_value;
        
        $tobacco = explode("|",$newdata['social_history']['smoking']);
        if($tobacco[2] != 0) {
          $smoking_date = $this->formatDate($tobacco[2],1);
        }
        else {
          $smoking_date = $tobacco[2];
        }
        $smoking_date_value = fixDate($smoking_date);
        foreach($tobacco_status as $key=>$value2) {
          if($tobacco[1] == $key)
            $tobacco[1] = strtolower($value2)."tobacco";
        }
        $smoking_value = $tobacco[0]."|".$tobacco[1]."|".$smoking_date_value;
        
        $query_insert = "INSERT INTO history_data
                         (
                          pid,
                          alcohol,
                          tobacco
                         )
                         VALUES
                         (
                          ?,
                          ?,
                          ?
                         )";
        $appTable->zQuery($query_insert,array($pid,
                                              $alcohol_value,
                                              $smoking_value));
      }
      elseif($table == 'procedure_result') {
        if($newdata['procedure_result']['date'] != 0) {
          $proc_date = $this->formatDate($newdata['procedure_result']['date'],0);
        }
        else {
          $proc_date =  $newdata['procedure_result']['date'];
        }
        
        if($newdata['procedure_result']['results_date'] !=0) {
          $proc_result_date = $this->formatDate($newdata['procedure_result']['results_date'],0);
        }
        else {
          $proc_result_date = $newdata['procedure_result']['results_date'];
        }
        
        $arr_procedure_res['procedure_result'][$j]['proc_name']        = $newdata['procedure_result']['proc_text'];
        $arr_procedure_res['procedure_result'][$j]['proc_code']        = $newdata['procedure_result']['proc_code'];
        $arr_procedure_res['procedure_result'][$j]['proc_extension']   = $newdata['procedure_result']['extension'];
        $arr_procedure_res['procedure_result'][$j]['proc_date']        = $proc_date;
        $arr_procedure_res['procedure_result'][$j]['proc_status']      = $newdata['procedure_result']['status'];
        $arr_procedure_res['procedure_result'][$j]['result']           = $newdata['procedure_result']['results_text'];
        $arr_procedure_res['procedure_result'][$j]['results_code']     = $newdata['procedure_result']['results_code'];
        $arr_procedure_res['procedure_result'][$j]['range']            = $newdata['procedure_result']['results_range'];
        $arr_procedure_res['procedure_result'][$j]['value']            = $newdata['procedure_result']['results_value'];
        $arr_procedure_res['procedure_result'][$j]['proc_result_date'] = $proc_result_date;
        $j++;  
      }
//      elseif($table == 'procedure') {
//        $arr_procedures['procedure'][$y]['extension']                = $newdata['procedure']['extension'];
//        $arr_procedures['procedure'][$y]['code']                     = $newdata['procedure']['code'];
//        $arr_procedures['procedure'][$y]['code_text']                = $newdata['procedure']['code_text'];
//        $arr_procedures['procedure'][$y]['date']                     = $newdata['procedure']['date'];
//        $arr_procedures['procedure'][$y]['provider_npi']             = $newdata['procedure']['provider_npi'];
//        $arr_procedures['procedure'][$y]['provider_name']            = $newdata['procedure']['provider_name'];
//        $arr_procedures['procedure'][$y]['provider_address']         = $newdata['procedure']['provider_address'];
//        $arr_procedures['procedure'][$y]['provider_city']            = $newdata['procedure']['provider_city'];
//        $arr_procedures['procedure'][$y]['provider_state']           = $newdata['procedure']['provider_state'];
//        $arr_procedures['procedure'][$y]['provider_postalCode']      = $newdata['procedure']['provider_postalCode'];
//        $arr_procedures['procedure'][$y]['provider_telecom']         = $newdata['procedure']['provider_telecom'];
//        $arr_procedures['procedure'][$y]['represented_organization'] = $newdata['procedure']['represented_organization'];
//        $y++;  
//      }
    }
    $this->InsertEncounter($arr_encounter['encounter'],$pid,0);
    $this->InsertVitals($arr_vitals['vitals'],$pid,0);
    //$this->InsertProcedures($arr_procedures['procedure'],$pid,0); 
    $lab_results = $this->buildLabArray($arr_procedure_res['procedure_result']);   
    $this->InsertLabResults($lab_results,$pid); 
    $this->InsertImmunization($arr_immunization['immunization'], $pid, 0);
    $this->InsertPrescriptions($arr_prescriptions['lists3'],$pid,0);
    $this->InsertAllergies($arr_allergies['lists2'],$pid,0);
    $this->InsertMedicalProblem($arr_med_pblm['lists1'],$pid,0);
    
    $appTable->zQuery("UPDATE audit_master 
                       SET approval_status=2 
                       WHERE id=?", array($audit_master_id));
    $appTable->zQuery("UPDATE documents 
                       SET audit_master_approval_status=2 
                       WHERE audit_master_id=?", array($audit_master_id));
    $appTable->zQuery("UPDATE documents 
                       SET foreign_id = ? 
                       WHERE id =? ", array($pid, 
                                            $document_id));
  }
  
  public function formatDate($unformatted_date,$ymd=1)
  {
    $day   = substr($unformatted_date,6,2);
    $month = substr($unformatted_date,4,2);
    $year  = substr($unformatted_date,0,4);
    if($ymd == 1) {     
      $formatted_date = $year."/".$month."/".$day;
    }
    else {
      $formatted_date = $day."/".$month."/".$year;
    }
    return $formatted_date;
  }
  
  public function getOptionId($list_id,$title)
  {
    $appTable = new ApplicationTable();
    $query   = "SELECT option_id 
                FROM list_options 
                WHERE list_id=? AND title=?";
    $result  = $appTable->zQuery($query,array($list_id,$title));
    $res_cur = $result->current();
    return $res_cur['option_id'];
  }
  
  public function InsertEncounter($enc_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($enc_array as $key=>$value) {
      $encounter_id          = $appTable->generateSequenceID('sequences');
      $query_sel_users     = "SELECT * 
                              FROM users 
                              WHERE abook_type='external_provider' AND npi=?";
      $res_query_sel_users = $appTable->zQuery($query_sel_users,array($value['provider_npi']));
      if($res_query_sel_users->count() > 0) {
        foreach($res_query_sel_users as $value1) {
          $provider_id = $value1['id'];
        }
      }
      else {
        $query_ins_users     = "INSERT INTO users
                                ( fname,
                                  npi,
                                  organization,
                                  street,
                                  city,
                                  state,
                                  zip,
                                  abook_type
                                )
                                VALUES
                                (
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  'external_provider'
                                )";
        $res_query_ins_users = $appTable->zQuery($query_ins_users,array($value['provider_name'],
                                                                        $value['provider_npi'],
                                                                        $value['represented_organization_name'],
                                                                        $value['provider_address'],
                                                                        $value['provider_city'],
                                                                        $value['provider_state'],
                                                                        $value['provider_postalCode']));
        $provider_id         = $res_query_ins_users->getGeneratedValue();
      }
      //facility
      $query_sel_fac     = "SELECT * 
                            FROM users 
                            WHERE abook_type='external_org' AND organization=?";
      $res_query_sel_fac = $appTable->zQuery($query_sel_fac,array($value['represented_organization_name']));
      if($res_query_sel_fac->count() > 0) {
        foreach($res_query_sel_fac as $value2) {
          $facility_id = $value2['id'];
        }
      }
      else {
        $query_ins_fac     = "INSERT INTO users
                              ( organization,
                                phonecell,
                                abook_type
                              )
                              VALUES
                              (
                                ?,
                                ?,
                                'external_org'
                              )";
        $res_query_ins_fac = $appTable->zQuery($query_ins_fac,array($value['represented_organization_name'],
                                                                    $value['represented_organization_telecom']));
        $facility_id       = $res_query_ins_fac->getGeneratedValue();
      }
      if($value['date'] != 0 && $revapprove==0) { 
        $encounter_date = $this->formatDate($value['date'],1);
        $encounter_date_value = fixDate($encounter_date); 
      }
      elseif($value['date'] != 0 && $revapprove==1){
        $encounter_date_value = \Application\Model\ApplicationTable::fixDate($value['date'], 'yyyy-mm-dd', 'dd/mm/yyyy');
      }
      elseif($value['date'] == 0) { 
        $encounter_date = $value['date'];
        $encounter_date_value = fixDate($encounter_date); 
      }
//        $encounter_date_value = fixDate($encounter_date); 
      $q_sel_encounter      = "SELECT * 
                               FROM form_encounter
                               WHERE external_id=?";
      $res_q_sel_encounter  = $appTable->zQuery($q_sel_encounter,array($value['extension']));
      if($res_q_sel_encounter->count() == 0) {
        $query_insert1 = "INSERT INTO form_encounter
                           (
                            pid,
                            encounter,
                            date,
                            facility,
                            facility_id,
                            provider_id,
                            external_id
                           )
                           VALUES
                           (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?
                           )";
          $appTable->zQuery($query_insert1,array($pid,
                                                 $encounter_id,
                                                 $encounter_date_value,
                                                 $value['represented_organization_name'],
                                                 $facility_id,
                                                 $provider_id,
                                                 $value['extension']));
      }
      else {
        $q_upd_encounter = "UPDATE form_encounter
                            SET pid=?,
                                encounter=?,
                                date=?,
                                facility=?,
                                facility_id=?,
                                provider_id=?
                            WHERE external_id=?";
        $appTable->zQuery($q_upd_encounter,array($pid,
                                                 $encounter_id,
                                                 $encounter_date_value,
                                                 $value['represented_organization_name'],
                                                 $facility_id,
                                                 $provider_id,
                                                 $value['extension']));
      }
    }
  }
  
  public function InsertVitals($vitals_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($vitals_array as $key=>$value) {
      if($value['date'] !=0 && $revapprove == 0) { 
        $vitals_date = $this->formatDate($value['date'],1);
        $vitals_date_value = fixDate($vitals_date);
      }
      elseif($value['date'] !=0 && $revapprove == 1) { 
        $vitals_date_value =  \Application\Model\ApplicationTable::fixDate($value['date'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['date'] == 0){
        $vitals_date = $value['date'];
        $vitals_date_value = fixDate($vitals_date);
      }
        
      $q_sel_vitals     = "SELECT * 
                           FROM form_vitals
                           WHERE external_id=?";
      $res_q_sel_vitals = $appTable->zQuery($q_sel_vitals,array($value['extension']));
      if($res_q_sel_vitals->count() == 0) {
        $query_insert = "INSERT INTO form_vitals
                         (
                          pid,
                          date,
                          bps,
                          bpd,
                          height,
                          weight,
                          temperature,
                          pulse,
                          respiration,
                          head_circ,
                          oxygen_saturation,
                          activity,
                          external_id
                         )
                         VALUES
                         (
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?,
                          1,
                          ?
                         )";
        $res          = $appTable->zQuery($query_insert,array($pid,
                                                              $vitals_date_value,
                                                              $value['bps'],
                                                              $value['bpd'],
                                                              $value['height'],
                                                              $value['weight'],
                                                              $value['temperature'],
                                                              $value['pulse'],
                                                              $value['respiration'],
                                                              $value['head_circ'],
                                                              $value['oxygen_saturation'],
                                                              $value['extension']));
        $vitals_id    = $res->getGeneratedValue();
      }
      else {
        $q_upd_vitals = "UPDATE form_vitals
                         SET pid=?,
                             date=?,
                             bps=?,
                             bpd=?,
                             height=?,
                             weight=?,
                             temperature=?,
                             pulse=?,
                             respiration=?,
                             head_circ=?,
                             oxygen_saturation=?
                         WHERE external_id=?";
        $appTable->zQuery($q_upd_vitals,array($pid,
                                              $vitals_date_value,
                                              $value['bps'],
                                              $value['bpd'],
                                              $value['height'],
                                              $value['weight'],
                                              $value['temperature'],
                                              $value['pulse'],
                                              $value['respiration'],
                                              $value['head_circ'],
                                              $value['oxygen_saturation'],
                                              $value['extension']));
        foreach($res_q_sel_vitals as $row_vitals) {
          $vitals_id = $row_vitals['id'];
        }
      }
        
      $query_sel         = "SELECT date FROM form_vitals WHERE id=?";
      $res_query_sel     = $appTable->zQuery($query_sel,array($vitals_id));
      $res_cur           = $res_query_sel->current();
      $vitals_date_forms = $res_cur['date'];

      $query_sel_enc     = "SELECT encounter 
                            FROM form_encounter 
                            WHERE date=? AND pid=?";
      $res_query_sel_enc = $appTable->zQuery($query_sel_enc,array($vitals_date_forms,$pid));

      if($res_query_sel_enc->count() == 0) { 
       $res_enc             = $appTable->zQuery("SELECT encounter 
                                                 FROM form_encounter
                                                 WHERE pid=?
                                                 ORDER BY id DESC
                                                 LIMIT 1",array($pid));
       $res_enc_cur         = $res_enc->current();
       $encounter_for_forms = $res_enc_cur['encounter'];
      }
      else {   
        foreach($res_query_sel_enc as $value2) { 
          $encounter_for_forms = $value2['encounter'];
        }
      }
        
      $query = "INSERT INTO forms
                (
                  date,
                  encounter,
                  form_name,
                  form_id,
                  pid,
                  user,
                  formdir
                )
                VALUES
                (
                  ?,
                  ?,
                  'Vitals',
                  ?,
                  ?,
                  ?,
                  'vitals'
                )";
      $appTable->zQuery($query,array($vitals_date_forms,
                                     $encounter_for_forms,
                                     $vitals_id,
                                     $pid,
                                     $_SESSION[authUser]));
    }
  }
  
//  public function InsertProcedures($proc_array,$pid,$revapprove=1)
//  {
//    $appTable = new ApplicationTable();
//    foreach($proc_array as $key=>$value) {
//      if($value['date'] != 0 && $revapprove == 0) {
//        $procedure_date = $this->formatDate($value['date'],1);
//        $procedure_date_value = fixDate($procedure_date);
//      }
//      elseif($value['date'] != 0 && $revapprove == 1) {
//        $procedure_date_value = \Application\Model\ApplicationTable::fixDate($value['date'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
//      }
//      elseif($value['date'] == 0) {
//        $procedure_date = $value['date'];
//        $procedure_date_value = fixDate($procedure_date);
//      }
//       //provider
//      $query_sel_users     = "SELECT * 
//                              FROM users 
//                              WHERE abook_type='external_provider' AND npi=?";
//      $res_query_sel_users = $appTable->zQuery($query_sel_users,array($value['provider_npi']));
//      if($res_query_sel_users->count() > 0) {
//        foreach($res_query_sel_users as $value1) {
//          $provider_id = $value1['id'];
//        }
//      }
//      else {
//        $query2      = "INSERT INTO users
//                        ( fname,
//                          npi,
//                          organization,
//                          street,
//                          city,
//                          state,
//                          zip,
//                          phone,
//                          abook_type
//                        )
//                        VALUES
//                        (
//                          ?,
//                          ?,
//                          ?,
//                          ?,
//                          ?,
//                          ?,
//                          ?,
//                          ?,
//                          'external_provider'
//                        )";
//        $res2        = $appTable->zQuery($query2,array($value['provider_name'],
//                                                       $value['provider_npi'],
//                                                       $value['represented_organization'],
//                                                       $value['provider_address'],
//                                                       $value['provider_city'],
//                                                       $value['provider_state'],
//                                                       $value['provider_postalCode'],
//                                                       $value['provider_telecom']));
//        $provider_id = $res2->getGeneratedValue();
//      }
//      //facility
//      $query3 = "SELECT * 
//                 FROM users 
//                 WHERE abook_type='external_org' AND organization=?";
//      $res3   = $appTable->zQuery($query3,array($value['represented_organization']));
//      if($res3->count() > 0) {
//        foreach($res3 as $value3) {
//          $facility_id = $value3['id'];
//        }
//      }
//      else {
//        $query4      = "INSERT INTO users
//                        ( organization,
//                          abook_type
//                        )
//                        VALUES
//                        (
//                          ?,
//                          'external_org'
//                        )";
//        $res4        = $appTable->zQuery($query4,array($value['represented_organization']));
//        $facility_id = $res4->getGeneratedValue();
//      }      
//        
//      $query_sel_enc     = "SELECT encounter 
//                            FROM form_encounter 
//                            WHERE date=? AND pid=?";
//      $res_query_sel_enc = $appTable->zQuery($query_sel_enc,array($procedure_date_value,$pid));
//
//      if($res_query_sel_enc->count() == 0) { 
//       $res_enc               = $appTable->zQuery("SELECT encounter 
//                                                   FROM form_encounter
//                                                   WHERE pid=?
//                                                   ORDER BY id DESC
//                                                   LIMIT 1",array($pid));
//       $res_enc_cur           = $res_enc->current();
//       $encounter_for_billing = $res_enc_cur['encounter'];
//      }
//      else {     
//        foreach($res_query_sel_enc as $val) { 
//          $encounter_for_billing = $val['encounter'];
//        }
//      }
//
//      $code_text            = preg_replace('/^[0-9]+\-/', '', $value['code_text']);
//      $q_sel_procedures     = "SELECT * 
//                               FROM billing
//                               WHERE external_id=?";
//      $res_q_sel_procedures = $appTable->zQuery($q_sel_procedures,array($value['extension']));
//      if($res_q_sel_procedures->count() == 0) {
//        $query5 = "INSERT INTO billing
//                  (
//                    date,
//                    code_type,
//                    code,
//                    pid,
//                    provider_id,
//                    user,
//                    encounter,
//                    code_text,
//                    billed,
//                    activity,
//                    external_id
//                  )
//                  VALUES
//                  (
//                    ?,
//                    'CPT4',
//                    ?,
//                    ?,
//                    ?,
//                    ?,
//                    ?,
//                    ?,
//                    0,
//                    1,
//                    ?
//                  )";
//        $appTable->zQuery($query5,array($procedure_date_value,
//                                        $value['code'],
//                                        $pid,
//                                        $provider_id,
//                                        $_SESSION['authUserID'],
//                                        $encounter_for_billing,
//                                        $code_text,
//                                        $value['extension']));
//      }
//      else {
//        $q_upd_procedures = "UPDATE billing
//                             SET date=?,
//                                 code=?,
//                                 pid=?,
//                                 provider_id=?,
//                                 encounter=?,
//                                 code_text=?
//                             WHERE external_id=?";
//        $appTable->zQuery($q_upd_procedures,array($procedure_date_value,
//                                                  $value['code'],
//                                                  $pid,
//                                                  $provider_id,
//                                                  $encounter_for_billing,
//                                                  $code_text,
//                                                  $value['extension']));
//      }
//    }
//  }
  
  public function InsertImmunization($imm_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($imm_array as $key=>$value) {
      //provider
      $query_sel_users     = "SELECT * 
                              FROM users 
                              WHERE abook_type='external_provider' AND npi=?";
      $res_query_sel_users = $appTable->zQuery($query_sel_users,array($value['provider_npi']));
      if($res_query_sel_users->count() > 0) {
        foreach($res_query_sel_users as $value1) {
          $provider_id = $value1['id'];
        }
      }
      else {
        $query_ins_users = "INSERT INTO users
                            ( fname,
                              npi,
                              organization,
                              street,
                              city,
                              state,
                              zip,
                              phone,
                              abook_type
                            )
                            VALUES
                            (
                              ?,
                              ?,
                              ?,
                              ?,
                              ?,
                              ?,
                              ?,
                              ?,
                              'external_provider')";
        $res_query_ins_users = $appTable->zQuery($query_ins_users,array($value['provider_name'],
                                                                        $value['provider_npi'],
                                                                        $value['represented_organization'],
                                                                        $value['provider_address'],
                                                                        $value['provider_city'],
                                                                        $value['provider_state'],
                                                                        $value['provider_postalCode'],
                                                                        $value['provider_telecom']));
        $provider_id        = $res_query_ins_users->getGeneratedValue();
      }
      //facility
      $query_sel_fac     = "SELECT * 
                            FROM users 
                            WHERE abook_type='external_org' AND organization=?";
      $res_query_sel_fac = $appTable->zQuery($query_sel_fac,array($value['represented_organization']));
      if($res_query_sel_fac->count() > 0) {
        foreach($res_query_sel_fac as $value2) {
          $facility_id = $value2['id'];
        }
      }
      else {
        $query_ins_fac     = "INSERT INTO users
                              ( organization,
                                phonecell,
                                abook_type
                              )
                              VALUES
                              (
                                ?,
                                ?,
                                'external_org'
                              )";
        $res_query_ins_fac = $appTable->zQuery($query_ins_fac,array($value['represented_organization'],
                                                                    $value['represented_organization_tele']));
        $facility_id       = $res_query_ins_fac->getGeneratedValue();
      }
      if($value['administered_date'] != 0 && $revapprove == 0) {
        $immunization_date = $this->formatDate($value['administered_date'],1);
        $immunization_date_value = fixDate($immunization_date);
      }
      elseif($value['administered_date'] != 0 && $revapprove == 1) {
        $immunization_date_value = \Application\Model\ApplicationTable::fixDate($value['administered_date'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['administered_date'] != 0) {
        $immunization_date = $value['administered_date'];
        $immunization_date_value = fixDate($immunization_date);
      }
      
      $q_sel_imm     = "SELECT * 
                        FROM immunizations
                        WHERE external_id=?";
      $res_q_sel_imm = $appTable->zQuery($q_sel_imm,array($value['extension']));
      if($res_q_sel_imm->count() == 0) {
        $query = "INSERT INTO immunizations 
                  ( patient_id,
                    administered_date,
                    cvx_code,
                    route,
                    administered_by_id,
                    external_id
                  ) 
                  VALUES 
                  (
                   ?,
                   ?,
                   ?,
                   ?,
                   ?,
                   ?
                  )";
        $appTable->zQuery($query,array($pid,
                                       $immunization_date_value,
                                       $value['cvx_code'],
                                       $value['route_code_text'],
                                       $provider_id,
                                       $value['extension']));
      }
      else {
        $q_upd_imm = "UPDATE immunizations
                      SET patient_id=?,
                          administered_date=?,
                          cvx_code=?,
                          route=?,
                          administered_by_id=?
                      WHERE external_id=?";
        $appTable->zQuery($q_upd_imm,array($pid,
                                           $immunization_date_value,
                                           $value['cvx_code'],
                                           $value['route_code_text'],
                                           $provider_id,
                                           $value['extension']));
      }
    }
  }
  
  public function InsertPrescriptions($pres_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($pres_array as $key=>$value) {
      $active = 1;             
       //provider
      $query_sel_users     = "SELECT * 
                              FROM users 
                              WHERE abook_type='external_provider' AND npi=?";
      $res_query_sel_users = $appTable->zQuery($query_sel_users,array($value['provider_npi']));
      if($res_query_sel_users->count() > 0) {
        foreach($res_query_sel_users as $value1) {
          $provider_id = $value1['id'];
        }
      }
      else {
        $query_ins_users     = "INSERT INTO users
                                ( fname,
                                  npi,
                                  street,
                                  city,
                                  state,
                                  zip,
                                  phone,
                                  abook_type
                                )
                                VALUES
                                (
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  ?,
                                  'external_provider'
                                )";
        $res_query_ins_users = $appTable->zQuery($query_ins_users,array($value['provider_name'],
                                                                        $value['provider_npi'],
                                                                        $value['provider_address'],
                                                                        $value['provider_city'],
                                                                        $value['provider_state'],
                                                                        $value['provider_postalCode'],
                                                                        $value['provider_telecom']));
        $provider_id         = $res_query_ins_users->getGeneratedValue();
      }
//      $route_title  = $this->getListTitle($value['route'],'drug_route','');
//      $units_title  = $this->getListTitle($value['units'],'drug_units','');
      if($revapprove == 0) {
        if($value['rate_unit'] == 0) {
          $value['rate_unit'] = '';
        }
        $unit_option_id = $this->getOptionId('drug_units', $value['rate_unit']);
      }
      else {
        $unit_option_id = $value['units'];
      }

      $q1_route     = "SELECT *  
                       FROM list_options
                       WHERE list_id='drug_route' AND option_id=?";
      $res_q1_route = $appTable->zQuery($q1_route,array($value['route']));
      if($res_q1_route->count() == 0) {
        $q_insert_route = "INSERT INTO list_options
                           (
                            list_id,
                            option_id,
                            title,
                            activity
                           )
                           VALUES
                           (
                            'drug_route',
                            ?,
                            ?,
                            1
                           )";
        $appTable->zQuery($q_insert_route,array($value['route'],
                                                $value['route_display']));
      }

      $q1_units     = "SELECT *  
                       FROM list_options
                       WHERE list_id='drug_units' AND option_id=?";
      $res_q1_units = $appTable->zQuery($q1_units,array($unit_option_id));
      if($res_q1_units->count() == 0) {
        $q_insert_units = "INSERT INTO list_options
                           (
                            list_id,
                            option_id,
                            title,
                            activity
                           )
                           VALUES
                           (
                            'drug_units',
                            ?,
                            ?,
                            1
                           )";
        $appTable->zQuery($q_insert_units,array($unit_option_id,
                                                $value['rate_unit']));
      }

      $pres_date = \Application\Model\ApplicationTable::fixDate($value['begdate'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 

      $q_sel_pres     = "SELECT * 
                         FROM prescriptions
                         WHERE external_id=?";
      $res_q_sel_pres = $appTable->zQuery($q_sel_pres,array($value['extension']));
      if($res_q_sel_pres->count() == 0) {
        $query = "INSERT INTO prescriptions 
                  ( patient_id,
                    date_added,
                    active,
                    drug,
                    size,
                    dosage,
                    route,
                    unit,
                    rxnorm_drugcode,
                    provider_id,
                    external_id
                 ) 
                 VALUES 
                 (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                 )";
        $appTable->zQuery($query, array($pid,
                                        $pres_date,
                                        $active, 
                                        $value['drug_text'], 
                                        $value['rate'], 
                                        $value['dose'],
                                        $value['route'],
                                        $unit_option_id,
                                        $value['drug_code'],
                                        $provider_id,
                                        $value['extension']));
      }
      else {
        $q_upd_pres = "UPDATE prescriptions
                       SET patient_id=?,
                           date_added=?,
                           drug=?,
                           size=?,
                           dosage=?,
                           route=?,
                           unit=?,
                           rxnorm_drugcode=?,
                           provider_id=?
                       WHERE external_id=?";
        $appTable->zQuery($q_upd_pres,array($pid,
                                            $pres_date,
                                            $value['drug_text'], 
                                            $value['rate'], 
                                            $value['dose'],
                                            $value['route'],
                                            $unit_option_id,
                                            $value['drug_code'],
                                            $provider_id,
                                            $value['extension']));
      }
    }
  }
  
  public function InsertAllergies($allergy_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($allergy_array as $key=>$value) {
      if($value['status'] == 'Active'){
        $active = 1;
      }elseif($value['status'] == 'Inactive'){
        $active = 0;
      }

      if($value['begdate'] !=0 && $revapprove == 0) {
        $allergy_begdate = $this->formatDate($value['begdate'],1);
        $allergy_begdate_value = fixDate($allergy_begdate);
      }
      elseif($value['begdate'] !=0 && $revapprove == 1) {
        $allergy_begdate_value = \Application\Model\ApplicationTable::fixDate($value['begdate'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['begdate'] == 0) {
        $allergy_begdate = $value['begdate'];
        $allergy_begdate_value = fixDate($allergy_begdate);
      }

      if($value['enddate'] !=0 && $revapprove == 0) {
        $allergy_enddate = $this->formatDate($value['enddate'],1);
        $allergy_enddate_value = fixDate($allergy_enddate);
      }
      elseif($value['enddate'] !=0 && $revapprove == 1) {
        $allergy_enddate_value = \Application\Model\ApplicationTable::fixDate($value['enddate'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['enddate'] == 0) {
        $allergy_enddate = $value['enddate'];
        $allergy_enddate_value = fixDate($allergy_enddate);
      }

      $q_sel_allergies     = "SELECT * 
                              FROM lists
                              WHERE external_id=? AND type='allergy'";
      $res_q_sel_allergies = $appTable->zQuery($q_sel_allergies,array($value['extension']));
      if($res_q_sel_allergies->count() == 0) {
        $query = "INSERT INTO lists 
                  ( pid, 
                    begdate, 
                    enddate,
                    type, 
                    title, 
                    diagnosis, 
                    severity_al,
                    activity,
                    external_id
                  ) 
                  VALUES 
                  ( 
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                  )";
        $appTable->zQuery($query, array($pid, 
                                        $allergy_begdate_value,
                                        $allergy_enddate_value,
                                        'allergy', 
                                        $value['list_code_text'],
                                        $value['list_code'], 
                                        $value['severity_al'],
                                        $active,
                                        $value['extension']));
      }
      else {
        $q_upd_allergies = "UPDATE lists
                            SET pid=?, 
                                begdate=?, 
                                enddate=?,
                                title=?, 
                                diagnosis=?, 
                                severity_al=?
                            WHERE external_id=? AND type='allergy'";
        $appTable->zQuery($q_upd_allergies,array($pid, 
                                                 $allergy_begdate_value,
                                                 $allergy_enddate_value,
                                                 $value['list_code_text'],
                                                 $value['list_code'], 
                                                 $value['severity_al'],
                                                 $value['extension']));
      }
    }
  }
  
  public function InsertMedicalProblem($med_pblm_array,$pid,$revapprove=1)
  {
    $appTable = new ApplicationTable();
    foreach($med_pblm_array as $key=>$value) {
      if($value['status'] == 'Active'){
        $activity = 1;
      }
      elseif($value['status'] == 'Inactive'){
        $activity = 0;
      }

      if($value['begdate'] !=0 && $revapprove == 0) {
        $med_pblm_begdate = $this->formatDate($value['begdate'],1);
        $med_pblm_begdate_value = fixDate($med_pblm_begdate);
      }
      elseif($value['begdate'] !=0 && $revapprove == 1) {
        $med_pblm_begdate_value = \Application\Model\ApplicationTable::fixDate($value['begdate'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['begdate'] == 0) {
        $med_pblm_begdate = $value['begdate'];
        $med_pblm_begdate_value = fixDate($med_pblm_begdate);
      }

      if($value['enddate'] !=0 && $revapprove == 0) {
        $med_pblm_enddate = $this->formatDate($value['enddate'],1);
        $med_pblm_enddate_value = fixDate($med_pblm_enddate);
      }
      elseif($value['enddate'] !=0 && $revapprove == 1) {
        $med_pblm_enddate_value = \Application\Model\ApplicationTable::fixDate($value['enddate'], 'yyyy-mm-dd', 'dd/mm/yyyy'); 
      }
      elseif($value['enddate'] == 0) {
        $med_pblm_enddate = $value['enddate'];
        $med_pblm_enddate_value = fixDate($med_pblm_enddate);
      }

      $q_sel_med_pblm     = "SELECT * 
                             FROM lists
                             WHERE external_id=? AND type='medical_problem'";
      $res_q_sel_med_pblm = $appTable->zQuery($q_sel_med_pblm,array($value['extension']));
      if($res_q_sel_med_pblm->count() == 0) {
        $query = "INSERT INTO lists 
                  ( pid, 
                    diagnosis, 
                    activity, 
                    title, 
                    begdate, 
                    enddate,
                    type,
                    external_id
                  ) 
                  VALUES 
                  ( ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                  )";
        $appTable->zQuery($query, array($pid, 
                                        $value['list_code'], 
                                        $activity, 
                                        $value['list_code_text'],
                                        $med_pblm_begdate_value, 
                                        $med_pblm_enddate_value,
                                        'medical_problem',
                                        $value['extension']));
      }
      else {
        $q_upd_med_pblm = "UPDATE lists
                           SET pid=?, 
                               diagnosis=?, 
                               title=?, 
                               begdate=?, 
                               enddate=?
                           WHERE external_id=? AND type='medical_problem'";
        $appTable->zQuery($q_upd_med_pblm,array($pid, 
                                                $value['list_code'], 
                                                $value['list_code_text'],
                                                $med_pblm_begdate_value, 
                                                $med_pblm_enddate_value,
                                                $value['extension']));
      }
    }
  }
}


