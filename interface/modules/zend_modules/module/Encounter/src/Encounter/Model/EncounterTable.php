<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Remesh Babu  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Encounter\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class EncounterTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function listIssue($issue)
    {
				return sqlQuery("SELECT * FROM lists WHERE id = ?",array($issue));
    }
    
    public function saveEncounter($post)
    {	
				global $userauthorized;
				global $phpgacl_location;
				global $pid;
				global $encounter;
				require_once ($phpgacl_location.'/../library/forms.inc');
				require_once ($phpgacl_location.'/../library/encounter.inc');
				
				$visitCategory 		= $post['visitCategory'];
				$facility		 	= $post['facility'];
				$billingFacility 	= $post['billingFacility'];
				$sensitivity 		= $post['sensitivity'];
				$description 		= $post['description'];
				$dtService			= $post['dtService'];
				$dtOnset			= $post['dtOnset'];
				$provider_id		= $post['provider'];
				
				if ($post['mode'] != 'Edit') {
						$conn = $GLOBALS['adodb']['db'];
						$encounter = $conn->GenID("sequences");
						
						//$provider_id = $userauthorized ? $_SESSION['authUserID'] : 0;
						//$dt = date('Y-m-d');
						
						$facilityresult = sqlQuery("select name FROM facility WHERE id =", array($facility));
						$facilityName = $facilityresult['name'];
						 
						$sql = "INSERT INTO form_encounter
						SET date = ?,
						onset_date = ?,
						reason = ?,
						facility = ?,
						pc_catid = ?,
						facility_id = ?,
						billing_facility = ?,
						sensitivity = ?,
						pid = ?,
						encounter = ?,
						provider_id = ?";
						$lastId = sqlInsert($sql, array(
								$dtService,
								$dtOnset,
								$description,
								$facilityName,
								$visitCategory,
								$facility,
								$billingFacility,
								$sensitivity,
								$pid,
								$encounter,
								$provider_id
						));
						addForm($encounter, "New Patient Encounter", $lastId, "newpatient", $pid, $userauthorized, $dtService);
				} else if ($post['mode'] == 'Edit') {
						$id = $post['id'];
						$datepart = acl_check('encounters', 'date_a') ? "date = '$dtService', " : "";
						$sql = "UPDATE form_encounter SET $datepart
										onset_date = ?, 
										reason = ?, 
										facility = ?, 
										pc_catid = ?, 
										facility_id = ?, 
										billing_facility = ?, 
										sensitivity = ?,
										provider_id = ? 
									WHERE id = ?";
						sqlStatement($sql, array(
								$dtOnset,
								$description,
								$facilityName,
								$visitCategory,
								$facility,
								$billingFacility,
								$sensitivity,
								$provider_id,
								$id
						));
				}
				
				// Update the list of issues associated with this encounter.
				sqlStatement("DELETE FROM issue_encounter WHERE pid = ? AND encounter = ?", array($pid, $encounter));
				if (is_array($_POST['issues'])) {
						foreach ($_POST['issues'] as $issue) {
							$sql = "INSERT INTO issue_encounter SET	pid=?,
							list_id=?,
							encounter=?";
							sqlInsert($sql, array($pid, $issue, $encounter));
						}
				}
				setencounter($encounter);
    }
    
    public function saveEncounterNote($post)
    {
				global $pid;
				global $encounter;
				global $userauthorized;
				$dt = date('Y-m-d');

				// Save vitals to Table form_vitals
				$sql = "INSERT INTO form_vitals SET pid = ?, 
													weight = ?, 
													height = ?, 
													bps = ?, 
													temperature = ?, 
													pulse = ?, 
													BMI = ?, 
													respiration = ?, 
													date = ?";
				
				$lastId = sqlInsert($sql, array(
										$pid, 
										$post['weight'], 
										$post['height'], 
										$post['bp'], 
										$post['tmp'], 
										$post['pl'], 
										$post['bmi'], 
										$post['rrate'],
										$dt
						));
				addForm($encounter, "Vitals", $lastId, "vitals", $pid, $userauthorized, $dt);
			
				// Encounter Table update if Notes edited
				$description = $post['ccomplaint'];
				$resultId = $post['resultId'];
				if (!empty($post['form_1'])) {
						$nNotes = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($post['form_1']))))));						
				} else {
						$nNotes = '';
				}
				//$nNotes = (!empty($post['form_1'])) ? post['form_1'] : '';
				$sql = "UPDATE form_encounter SET reason = ?,
										n_notes = ? 
									WHERE id = ?";
				sqlStatement($sql, array(
								$description,
								$nNotes,
								$resultId
						));
    }
    
		// List encounter data
    public function listEncounter($encounter)
    {
				$arr = array();
				$sql = "SELECT * FROM form_encounter WHERE encounter=?";
				$result = sqlStatement($sql, array($encounter));
				while ($row = sqlFetchArray($result)) {
					array_push($arr, $row);
				}
				return $arr;
    }
		
		// Delete Encunter Notes
		public function deleteEncounterNote($post)
		{
				$resultId = $post['resultId'];
				$vitalsId = $post['vitalsId'];
				$sql = "UPDATE form_encounter SET reason = '' WHERE id = ?";
				sqlStatement($sql, array($resultId));
				sqlStatement("DELETE FROM form_vitals WHERE id = ?", array($vitalsId));
		}
}


