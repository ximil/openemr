<?php
namespace Zend\View\Helper\Openemr;
use Zend\View\Helper\AbstractHelper;
use gacl_api;
use Document;

class Emr extends AbstractHelper
{
    public function getList($list_id,$selected='',$opt='')
    {
        $res = sqlStatement("SELECT * FROM list_options WHERE list_id=? ORDER BY seq, title",array($list_id));
        $i = 0;
	if ($opt == 'search') {
	    $rows[$i] = array (
			'value' => 'all',
			'label' => xlt('All'),
			'selected' => TRUE,
		    );
	    $i++;
	} elseif ($opt == '') {
	    $rows[$i] = array (
		    'value' => '',
		    'label' => xlt('Unassigned'),
		    'disabled' => FALSE
	    );
	    $i++;
	}
	while($row=sqlFetchArray($res)) {
            $sel = ($row['option_id']==$selected) ? TRUE : FALSE;
            $rows[$i] = array (
                    'value' => htmlspecialchars($row['option_id'],ENT_QUOTES),
                    'label' => xlt($row['title']),
                    'selected' => $sel,
            );
            $i++;
	}
	return $rows;
    }
    
    public function getProviders()
    {
		global $encounter;
		global $pid;
					
		$sqlSelctProvider = "SELECT * FROM form_encounter WHERE encounter=? AND pid=?";
		$resultSelctProvider = sqlQuery($sqlSelctProvider, array($encounter, $pid));
		$provider = $resultSelctProvider['encounter_provideID'];
		$res = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
					"WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
					"AND authorized = 1 " .
					"ORDER BY lname, fname"); 
		$rows[0] = array (
			'value' => '',
			'label' => xlt('Unassigned'),
			'selected' => TRUE,
			'disabled' => FALSE
		);
		$i = 1;
	
	while($row=sqlFetchArray($res)) {
		if ($row['id'] == $provider) {
			$select =  TRUE;
		} else {
			$select = FALSE;
		}
		$rows[$i] = array (
			'value' => $row['id'],
			'label' => $row['fname']." ".$row['lname'],
			'selected' => $select,
		);
		$i++;
	}
	return $rows;
    }
    
    /**
    * function getLabs
    * @param $type
    * @value 'y' - for type of Labs (Loacal or External)
    */
    public function getLabs($type='')
    {
	$res = sqlStatement("SELECT ppid,name,remote_host,login,password,mirth_lab_id FROM procedure_providers ORDER BY name, ppid"); 
	//$rows[0] = array (
	//	'value' => '0',
	//	'label' => xlt('Local Lab'),
	//	'selected' => TRUE,
	//	'disabled' => FALSE
	//);
	$i = 0;
	//$select =  TRUE;
	while($row=sqlFetchArray($res)) {
	    $value = '';
	    if ($type == 'y') {
		if ($row['remote_host'] != '' && $row['login'] != '' && $row['password'] != '') {
			$value = $row['ppid'] . '|' . 1 . '|' . $row['mirth_lab_id']; // 0 - Local Lab and 1 - External Lab
		} else {
			$value = $row['ppid'] . '|' . 0 . '|' . $row['mirth_lab_id'];
		}
	    } else {
		$value = $row['ppid']. '|' . $row['mirth_lab_id'];
	    }
		if ($row['name'] == 'Quest') {
			$select =  TRUE;
		} else {
			$select = FALSE;
		}
		
		/*if ($id != '' && $id == $value) { 
			$select = 'TRUE'; 
		}*/
	    $rows[$i] = array (
		'value' => $value,
		'label' => $row['name'],
		'selected' => $select,
	    );
	    $i++;
	}
	return $rows;
    }
		
    public function getDropdownValAsText($list_id,$option_id)
    {
        $res = sqlQuery("SELECT title FROM list_options WHERE list_id = ? AND option_id = ?",array($list_id,$option_id)); 
        return $res['title'];
    }
    
    /**
     * Encounter Module Functions
     * function getEncounter
     * if viewmode is true
     */
    public function getEncounter($id)
    {
    	$result = sqlQuery("SELECT * FROM form_encounter WHERE id = ?", array($id));
    }
    
    /**
     * function getVisitCategory
     * List Visit Category in the combobox
     */
    public function getVisitCategory($data)
    {
    	$viewmode 	= isset($data['viewmode']) ? $data['viewmode'] : '';
    	$pcId	 	= isset($data['pcId']) ? $data['pcId'] : '';
    	$sql = "SELECT pc_catid, pc_catname
    				FROM openemr_postcalendar_categories
    				ORDER BY pc_catname";
    	$result = sqlStatement($sql);
    	
    	$rows[0] = array (
    			'value' => '',
    			'label' => xlt('Select One'),
    			'selected' => true
    	);
    	 
    	$i = 1;
    	 
    	while($row = sqlFetchArray($result)) {
    		if ($row['pc_catid'] < 9 && $row['pc_catid'] != 5) continue;
    		if ($viewmode && $row['pc_catid'] == $pcID) {
    			$select =  TRUE;
    		} else {
    			$select = FALSE;
    		}
    		$rows[$i] = array (
    				'value' => $row['pc_catid'],
    				'label' => xlt($row['pc_catname']),
    				'selected' => $select,
    		);
    		$i++;
    	}
    	return $rows;
    }
    
    /**
     * function getDefaultFacility
     * Get Default Facility
     */
    public function getDefaultFacility($authUser)
    {
    	$result = sqlStatement("SELECT facility_id FROM  users WHERE username = ?", array($authUser));
    	$row = sqlFetchArray($result);
    	return $row['facility_id'];
    }
    
    /**
     * function getFacility
     * List Facility in the combobox
     */
    public function getFacility($data)
    {
    	$defaultFacility = isset($data['defaultFacility']) ? $data['defaultFacility'] : '';
    	$sql = "SELECT * FROM facility WHERE service_location != 0 ORDER BY name";
    	$result = sqlStatement($sql);
    	$i = 0;
    
    	while($row = sqlFetchArray($result)) {
    		if ($row['id'] == $defaultFacility) {
    			$select =  TRUE;
    		} else {
    			$select = FALSE;
    		}
    		$rows[$i] = array (
    				'value' => $row['id'],
    				'label' => xlt($row['name']),
    				'selected' => $select,
    		);
    		$i++;
    	}
    	return $rows;
    }
    
    /**
     * function getBillingFacility
     * List Billing Facility in the combobox
     */
    public function getBillingFacility($data)
    {
    	$billingFacility = isset($data['billingFacility']) ? $data['billingFacility'] : '';
    	$sql = "SELECT * FROM facility WHERE service_location != 0 ORDER BY name";
    	$result = sqlStatement($sql);
    	$i = 0;
    
    	while($row = sqlFetchArray($result)) {
    		if ($row['id'] == $billingFacility) {
    			$select =  TRUE;
    		} else {
    			$select = FALSE;
    		}
    		$rows[$i] = array (
    				'value' => $row['id'],
    				'label' => xlt($row['name']),
    				'selected' => $select,
    		);
    		$i++;
    	}
    	return $rows;
    }
    
    /**
     * function getSensitivities
     * get all Sensitivities
     */
    public function getSensitivities($data)
    {
    	$viewmode 	= isset($data['viewmode']) ? $data['viewmode'] : '';
    	$sensitivity = isset($data['sensitivity']) ? $data['sensitivity'] : '';
    	$sensitivities = $this->getOptions($data);

    	if ($sensitivities && count($sensitivities)) {
    		usort($sensitivities, "sensitivity_compare");
    	}
    	
    	$i = 0;
    	foreach ($sensitivities as $value) {
    		if (acl_check('sensitivities', $value[1])) {
    			if ($viewmode && $sensitivity == $value[1]) {
    				$select =  TRUE;
    			} else {
    				$select = FALSE;
    			}
    	
	    		$rows[$i] = array (
	    				'value' => $value[1],
	    				'label' => xlt($value[3]),
	    				'selected' => $select,
	    		);
	    		$i++;
    		}
    	}
    	if ($viewmode && !$sensitivity) {
    		$select =  TRUE;
    	} else {
    		$select = FALSE;
    	}
    	$rows[$i] = array (
    			'value' => '',
    			'label' => xlt('None'),
    			'selected' => $select,
    	);
    	return $rows;
    }
    
    /**
     * function getOptions
     * List all values for nessearry functions
     */
    public function getOptions($data) 
    {
    	global $phpgacl_location;
    	$section 			= $data['opt'];
    	if ($phpgacl_location) {
    		include_once("$phpgacl_location/gacl_api.class.php");
				$gacl = new gacl_api;
    		$arr1 = $gacl->get_objects($section, 1, 'ACO');
    		$arr = array();
    		if (!empty($arr1[$section])) {
    			foreach ($arr1[$section] as $value) {
    				$odata = $gacl->get_object_data($gacl->get_object_id($section, $value, 'ACO'), 'ACO');
    				$arr[$value] = $odata[0];
    			}
    		}
    		return $arr;
    	}
    	return 0;
    }
    
    /**
     * function getIssues
     * Get all issues (Injuries/Medical/Allergy) of a patient
     */
    public function getIssues($data,$ISSUE_TYPES)
    {
    	global $phpgacl_location;
    	$pid 			= isset($data['pid']) ? $data['pid'] : 0;
    	$viewmode 		= isset($data['viewmode']) ? $data['viewmode'] : '';
    	$encounter 		= isset($data['encounter']) ? $data['encounter'] : '';
    	$requestIssue	= isset($data['requestIssue']) ? $data['requestIssue'] : '';
    	$rows = array();
    	$sql = "SELECT id, type, title, begdate 
    					FROM lists 
    					WHERE pid = ? 
    					AND enddate IS NULL 
    					ORDER BY type, begdate";
    	$result = sqlStatement($sql, array($pid));
    	$i = 0;
    	
    	while ($row = sqlFetchArray($result)) {
    		$list_id 	= $row['id'];
    		$tcode		= $row['type'];
    		
    		if ($ISSUE_TYPES[$tcode]) $tcode = $ISSUE_TYPES[$tcode][2];
    		
    		if ($viewmode) {
    			$query = "SELECT count(*) AS count 
    									FROM issue_encounter 
    									WHERE pid = ? 
    									AND encounter = ? 
    									AND list_id = ?";
    			$tmp = sqlQuery($query, array($pid, $encounter, $list_id));
    			if ($tmp['count']) {
    				$select =  TRUE;
    			} else {
    				$select = FALSE;
    			}
    		} else {
    			// For new encounters the invoker may pass an issue ID.
    			if (!empty($requestIssue) && $requestIssue == $list_id) {
    				$select =  TRUE;
    			} else {
    				$select = FALSE;
    			}
    		}
    		
    		$label = $tcode . ":" . $row['begdate'] . " " . htmlspecialchars(substr($row['title'], 0, 40));
    		$rows[$i] = array (
    				'value' => $row['id'],
    				'label' => $label,
    				'selected' => $select,
    		);
    		$i++;
    	}
    	return $rows;
    }
    
    /**
     * Sort comparison for sensitivities 
     * by their order attribute.
     */
    public function sensitivity_compare($a, $b)
    {
    		return ($a[2] < $b[2]) ? -1 : 1;
    }
    
    /**
     * Charting Menu List
     */
    public function getChartingMenuList($data)
    {
    	global $pid;
    	global $encounter;
    	$type	= $data['type'];
    	$arr = array();
    	
				if ($type == 1) {
						$state	= $data['state'];
						$limit	= $data['limit'];
						$offset = $data['offset'];
						
						$sql = "SELECT category, 
										nickname, 
										name, 
										state, 
										directory, 
										id, 
										sql_run, 
										unpackaged, 
										date
									FROM registry
									WHERE state
									LIKE ?
									ORDER BY category, priority, name";
						if ($limit != "unlimited") $sql .= " limit $limit, $offset";
						$result = sqlStatement($sql, array($state));
						
						$i = 0;
						
						while ($row = sqlFetchArray($result)) {
								$formId = 0;
								if ($pid && $encounter && $row['directory']) {
										$query 			= "SELECT form_id
																				FROM forms
																				WHERE pid=?
																				AND encounter=?
																				AND formdir=?
																				AND deleted=0";
										$resultQuery 	= sqlStatement($query, array($pid, $encounter, $row['directory']));
										while ($tmp = sqlFetchArray($resultQuery)) {
											$formId = $tmp['form_id'];
										}
								}
								
								$arr[$i]['category'] 		= htmlspecialchars($row['category'],ENT_QUOTES);
								$arr[$i]['nickname'] 		= htmlspecialchars($row['nickname'],ENT_QUOTES);
								$arr[$i]['name'] 				= htmlspecialchars($row['name'],ENT_QUOTES);
								$arr[$i]['state'] 			= htmlspecialchars($row['state'],ENT_QUOTES);
								$arr[$i]['directory'] 	= htmlspecialchars($row['directory'],ENT_QUOTES);
								$arr[$i]['id'] 					= htmlspecialchars($row['id'],ENT_QUOTES);
								$arr[$i]['sql_run'] 		= htmlspecialchars($row['sql_run'],ENT_QUOTES);
								$arr[$i]['unpackaged']	= htmlspecialchars($row['unpackaged'],ENT_QUOTES);
								$arr[$i]['date'] 				= htmlspecialchars($row['date'],ENT_QUOTES);
								if ($formId > 0) {
										$arr[$i]['form_id'] 	= htmlspecialchars($formId,ENT_QUOTES);
								} else {
										$arr[$i]['form_id'] 	= 0;
								}
								$i++;
						}
				}
    	
				// LBF 
				if ($type == 2) {
						$sql = "SELECT list_id, option_id, title
												FROM list_options
												WHERE list_id = 'lbfnames'
												ORDER BY seq, title";
						$result = sqlStatement($sql);
						$arr = array();
						$i = 0;
						while ($row = sqlFetchArray($result)) {
								$formId = 0;
								if ($pid && $encounter && $row['option_id']) {
										$query 			= "SELECT form_id
																				FROM forms
																				WHERE pid=?
																				AND encounter=?
																				AND formdir=?
																				AND deleted=0";
										$resultQuery 	= sqlStatement($query, array($pid, $encounter, $row['option_id']));
										while ($tmp = sqlFetchArray($resultQuery)) {
												$formId = $tmp['form_id'];
										}
								}
								$arr[$i]['list_id'] 		= htmlspecialchars($row['list_id'],ENT_QUOTES);
								$arr[$i]['option_id'] 	= htmlspecialchars($row['option_id'],ENT_QUOTES);
								$arr[$i]['title'] 			= htmlspecialchars($row['title'],ENT_QUOTES);
								if ($formId > 0) {
										$arr[$i]['form_id'] = htmlspecialchars($formId,ENT_QUOTES);
								} else {
										$arr[$i]['form_id'] = 0;
								}
								$i++;
						}
				}
				
				// Modules
				if ($type == 3) {
						/*$sql = "SELECT 
										mod_name,
										mod_nick_name,
										mod_relative_link,
										type 
									FROM modules 
									WHERE mod_active = 1 
										AND sql_run= 1 
										AND mod_enc_menu='yes' 
									ORDER BY mod_ui_order ASC";*/
						$sql = "SELECT msh.*,
														ms.menu_name,
														ms.path,
														m.mod_ui_name,
														m.type 
												FROM modules_hooks_settings AS msh 
												LEFT OUTER JOIN modules_settings AS ms 
														ON obj_name=enabled_hooks 
																AND ms.mod_id=msh.mod_id 
												LEFT OUTER JOIN modules AS m 
														ON m.mod_id=ms.mod_id 
												WHERE ms.fld_type=3 
														AND m.mod_active=1 
														AND m.sql_run=1 
														AND msh.attached_to='encounter' 
												ORDER BY mod_id";
						$result = sqlStatement($sql);
						while ($row = sqlFetchArray($result)) {
								$modulePath = "";
								$added      = "";
								if($row['type'] == 0) {
										$modulePath = $GLOBALS['customDir'];
										$added		= "";
										$relative_link = "../../modules/".$modulePath."/".$row['mod_relative_link'].$added;
								}
								else{
										$added		= "index";
										$modulePath = $GLOBALS['zendModDir'];
										//$relative_link = strtolower($row['mod_name']) . '/' . $added;
										//$relative_link = "../../".$modulePath."/".$row['mod_relative_link'].$added;
										$relative_link = "../../".$modulePath."/".$row['path'];
										//$relative_link = strtolower($row['mod_ui_name']) . '/' . $added;
										//$relative_link .= $added;
								}
								$row['relative_link'] = $relative_link;
								array_push($arr, $row);
						}
				}
				return $arr;
    }
    
		// List Patient details
    public function getPatientDetails($data)
    {
				$patient = $data['patient'];
				$arr = array();
				$sql = "SELECT * FROM patient_data WHERE id=?";
				$result = sqlStatement($sql, array($patient));
				while ($row = sqlFetchArray($result)) {
					array_push($arr, $row);
				}
				return $arr;
    }
		
		
		// Get Patient Image (Photo)
		public function getPatientImage($data)
		{
				global $phpgacl_location;
				global $web_root;
				require_once($phpgacl_location . "/../library/classes/Document.class.php");
				
				$pid 					= $data['pid'];
				$picDirectory = $data['photoCatName'];
				$arr = array();
				$images = array();
				$docID = 0;
				
				$sql = "SELECT documents.id
												FROM documents
												JOIN categories_to_documents 
														ON documents.id = categories_to_documents.document_id
												JOIN categories on categories.id = categories_to_documents.category_id
												WHERE categories.name LIKE ?
														AND documents.foreign_id = ?";
				if ($result = sqlStatement($sql, array($picDirectory,$pid))) {
						$i = 0;
						while($tmp = sqlFetchArray($result)) {
								$docID 			= $tmp['id'];
								$obj 				= new Document($docID);
								$imageFile 	= $obj->get_url_file();
								$images[$i]['image'] = '<img src=' . $web_root .	'/controller.php?document&retrieve&patient_id=' .$pid . '&document_id=' .$docID . 'width="100px" style="width:100px;height:80px;" align="middle" alt=' . $picDirectory . ':' . $imageFile . '/>';	
						$i++;
						}
				}
				return $images;
		}
		
		// Employer Details
		public function getEmployerDetails($data)
		{
				$pid = $data['pid'];
				$sql = "SELECT * FROM employer_data WHERE pid=? ORDER BY date DESC LIMIT 0,1";
				$result  = sqlStatement($sql, array($pid));
				$row = sqlFetchArray($result);
				return $row;
		}
	
		// List Vitals
    public function getVitals($pid)
    {
				$arr = array();
				$sql = "SELECT * FROM form_vitals WHERE pid=?";
				$result = sqlStatement($sql, array($pid));
				while ($row = sqlFetchArray($result)) {
					array_push($arr, $row);
				}
				return $arr;
    }
		
		// Preview Report (Preview Tab)
		public function getPreviewForms($data)
		{
				$pid = $data['pid'];
				$arr = array();
				$billData = array();
				require_once($GLOBALS['srcdir'] . '/formatting.inc.php');
				require_once($GLOBALS['srcdir'] . '/forms.inc');
				require_once($GLOBALS['srcdir'] . '/patient.inc');
				$sql = "SELECT DISTINCT formdir
												FROM forms
												WHERE pid = ?
														AND deleted=0";
				$result = sqlStatement($sql, array($pid));
				while($tmp = sqlFetchArray($result)) {
						$formDir = $tmp['formdir'];
						if (substr($formDir,0,3) == 'LBF') {
								include_once($GLOBALS['incdir'] . "/forms/LBF/report.php");
						} else {
								include_once($GLOBALS['incdir'] . "/forms/$formDir/report.php");
						}
				}
				$sql = "SELECT
												forms.encounter,
												forms.form_id,
												forms.form_name,
												forms.formdir,
												forms.date AS fdate,
												form_encounter.date,
												form_encounter.reason
										FROM forms, form_encounter
										WHERE forms.pid = ?
												AND form_encounter.pid = ?
												AND form_encounter.encounter = forms.encounter
												AND forms.deleted=0
										ORDER BY form_encounter.date DESC, fdate ASC";
				$result = sqlStatement($sql, array($pid, $pid));
				$i = 0;
				while($row = sqlFetchArray($result)) {
						// Get Form Name
						$formDir	= $row['formdir'];
						$formID		= $row['form_id'];
						$sqlForm = "SELECT form_name
														FROM forms
														WHERE formdir='$formDir' 
																AND form_id='$formID'";
						$resultForm = sqlQuery($sqlForm);
						// Get Encounter Date
						$encounter 	= $row['encounter'];
						$sqlDate 		= "SELECT date
														FROM form_encounter
														WHERE encounter='$encounter'
														ORDER BY date";
						$resultDate = sqlQuery($sqlDate);
						
						$arr[$i]['formDir'] 	= $formDir;
						$arr[$i]['form_name'] = htmlspecialchars($resultForm['form_name'],ENT_QUOTES);
						$arr[$i]['date'] 			= oeFormatSDFT(strtotime($resultDate['date']));
						$arr[$i]['provider'] 	= getProviderName(getProviderIdOfEncounter($encounter));
						$N = 6;
						if (substr($formDir, 0, 3) == 'LBF') {
								$arr[$i]['REPORTS'] = 'lbf_report' . ',' . $pid . ',' . $encounter . ',' . $N . ',' . $formID . ',' . $formDir;
								//call_user_func("lbf_report", $pid, $encounter, $N, $formID, $formDir);
						} else {
								$arr[$i]['REPORTS'] = $formDir . '_report' . ',' . $pid . ',' . $encounter . ',' . $N . ',' . $formID;
								//call_user_func($formDir . "_report", $pid, $encounter, $N, $formID);
						}
						$arr[$i]['BILLINFO'] 	= $this->getPreviewBillInfo($pid, $encounter);
						$arr[$i]['NOTES'] 		= $this->getPreviewComplaintNotes($pid, $encounter);
						$i++;
				}
				//echo '<pre>'; print_r($arr); echo '</pre>';
				return $arr;
		}
		
		// Preview Tab (Billing Information)
		public function getPreviewBillInfo($pid, $encounter)
		{
				$arr 		= array();
				$sql		= "SELECT b.date, 
														b.code, 
														b.code_text 
												FROM billing AS b, 
														code_types AS ct 
												WHERE b.pid = ? 
														AND b.encounter = ? 
														AND b.activity = 1 
														AND b.code_type = ct.ct_key 
														AND ct.ct_diag = 0 
														ORDER BY b.date"; 
				$result	= sqlStatement($sql, array($pid, $encounter));
				$i = 0;
				while ($row = sqlFetchArray($result)) {
						$arr[$i] = $row['code'] . '|' . $row['code_text'];
						$i++;
				}
				return $arr;
		}
		
		// Preview Tab (Chief Complaint and Nation Notes)
		public function getPreviewComplaintNotes($pid, $encounter)
		{
				$sql = "SELECT reason, n_notes
												FROM form_encounter
												WHERE pid = ?
												AND encounter = ?";
				$result	= sqlStatement($sql, array($pid, $encounter));
				$i = 0;
				while ($row = sqlFetchArray($result)) {
						$arr[$i] = $row['reason'] . '|' . $row['n_notes'];
						$i++;
				}
				return $arr;								
		}
		
		// Facility Details
		public function getFacilityDetails($data)
		{
				$facilityID = $data['facilityID'];
				if ($facilityID) {
						$sql 		= "SELECT * FROM facility WHERE id= ?";
						$result	= sqlStatement($sql, array($facilityID));
				} else {
						$sql 		= "SELECT * FROM facility ORDER BY billing_location DESC LIMIT 1";
						$result	= sqlStatement($sql);
				}
				$row = sqlFetchArray($result);
				return $row;
		}
		
		// Lay Out Options for Preview report
		/*public function getReportLayout($data, $patient, $employer)
		{
				$fh = fopen("D:/test.txt", "a"); fwrite($fh, "\n in function start");
				//$fh = fopen("D:/test.txt", "a"); fwrite($fh, print_r($patient, 1));
				$arr 	= array();
				$i 		= 0;
				foreach ($data as $k => $v) {
						if ($v == "Demographics") {
								$value[$i] = "DEM";
								$i++;
						}
						if ($v == "History") {
								$value[$i] = "HIS";
								$i++;
						}
				}
				$layOut = $this->getLayOut($value);
				foreach($layOut as $key => $value) {
						$groupName	= $value['group_name'];
						$formType		= $value['form_id'];
						$titleCols	= $value['titlecols'];
						$dataCols		= $value['datacols'];
						$dataType		= $value['data_type'];
						$fieldID		= $value['field_id'];
						$listID			= $value['list_id'];
						$currValue  = '';
						if ($formType == 'DEM') {
								if ($GLOBALS['athletic_team']) {
										// Skip fitness level and return-to-play date because those appear
										if ($fieldID === 'fitness' || $fieldID === 'userdate1') continue;
								}
								if (strpos($fieldID, 'em_') === 0) {
										// Skip employer related fields, if it's disabled.
										if ($GLOBALS['omit_employers']) continue;
										$tmp = substr($fieldID, 3);
										if (isset($employer[$tmp])) $currValue = $employer[$tmp];
								}	else {
										if (isset($patient[0][$fieldID])) $currValue = $patient[0][$fieldID];
								}
						}else {
								if (isset($patient[0][$fieldID])) $currValue = $patient[0][$fieldID];
						}
						
						$row = $this->getReportDetails($value, $currValue);//$fh = fopen("D:/test.txt", "a"); fwrite($fh, print_r($row,1));
						//if (count($row) > 0) {
								array_push($arr, $row);
						//}
						//$fh = fopen("D:/test.txt", "a");
						//fwrite($fh, "\n inside loop ".print_r($arr,1));
						//array_merge($arr, $row);
						
						
				}
				
			
				
				//$fh = fopen("D:/test.txt", "a");
				fwrite($fh, "\n inside loop ".print_r($arr,1));
				return $arr;
    }*/

		
		// Lay Out Options (display)
		// param. form_id
		public function getLayOut($data)
		{
				$arr = array();
				foreach ($data as $key => $value) {
						$formType = $value;
						$sql 		= "SELECT * FROM layout_options 
												WHERE form_id = ? AND uor > 0 
												ORDER BY group_name, seq";
						$result = sqlStatement($sql, array($formType));
						$i 			= 0;
						while ($row = sqlFetchArray($result)) {
								array_push($arr, $row);
								//$fh = fopen("D:/test.txt", "a"); fwrite($fh,print_r($value,1));
						}
				}
				return $arr;
		}
		
		// Get Report Details
		/*public function getReportDetails($value, $currValue)
		{
				$dataType = $value['data_type'];
				$fieldID  = isset($value['field_id'])  ? $value['field_id'] : null;
				$listID   = $value['list_id'];
				$s 				= '';
				$data 		= array();
				
				if ($dataType == 1 || $dataType == 26 || $dataType == 27 || $dataType == 33) {
						$sql 		= "SELECT title
														FROM list_options
														WHERE list_id = ?
														AND option_id = ?";
						$result = sqlQuery($sql, array($listID, $currValue));
						//$s = htmlspecialchars(xl_list_label($lrow['title']),ENT_NOQUOTES);
						if (!empty($result['title'])) $data[$fieldID] = $result['title'];
						
						//For lists Race and Ethnicity if there is no matching value in the corresponding lists check ethrace list
						if ($result == 0 && $dataType == 33) {
								$listID = 'ethrace';
								$sqlEthrace 		= "SELECT title
																		FROM list_options
																		WHERE list_id = ?
																		AND option_id = ?";
								$resultEthrace 	= sqlQuery($sqlEthrace, array($listID, $currValue));
								//$s = htmlspecialchars(xl_list_label($lrow_ethrace['title']),ENT_NOQUOTES);
								if (!empty($resultEthrace['title'])) $data[$fieldID] = $resultEthrace['title'];
						}
				} else if ($dataType == 2) { // simple text field
						//$s = htmlspecialchars($currvalue,ENT_NOQUOTES);
						$data[$fieldID] = $currValue;
				} else if ($dataType == 3) { // long or multi-line text field
						//$s = nl2br(htmlspecialchars($currvalue,ENT_NOQUOTES));
						$data[$fieldID] = $currValue;
				} else if ($data_type == 4) { // date
						//$s = htmlspecialchars(oeFormatShortDate($currvalue),ENT_NOQUOTES);
						$data[$fieldID] = $currValue;
				} else if ($data_type == 10 || $data_type == 11) {
								$sqlUser	= "SELECT fname,
																		lname,
																		specialty
														FROM users
														WHERE id = ?";
						$resultUser 	= sqlQuery($sqlUser, array($currValue));
						$data['username'] = htmlspecialchars(ucwords($resultUser['fname'] . " " . $resultUser['lname']),ENT_NOQUOTES);
				} else if ($data_type == 12) { // pharmacy list
						$pharmacies = $this->getPharmacies();
						$key = $pharmacies['id'];
						if ($currValue == $key) {
								//$s .= htmlspecialchars($prow['name'] . ' ' . $prow['area_code'] . '-' .
								//$prow['prefix'] . '-' . $prow['number'] . ' / ' .
								//$prow['line1'] . ' / ' . $prow['city'],ENT_NOQUOTES);
								$data['pharmacies'] = htmlspecialchars($pharmacies['name'] . ' ' . $pharmacies['area_code']  . '-' . $pharmacies['prefix'] . '-' . $pharmacies['number'] . ' / ' . $pharmacies['line1'] . ' / ' . $pharmacies['city'],ENT_NOQUOTES);
							}

				} else if ($data_type == 13) { // squads
						global $srcdir;
						require_once($srcdir . "/acl.inc");
						$squads = acl_get_squads();
						if ($squads) {
								foreach ($squads as $key => $value) {
										if ($currValue == $key) {
												//$s .= htmlspecialchars($value[3],ENT_NOQUOTES);
												$data[$fieldID] = htmlspecialchars($value[3],ENT_NOQUOTES);
										}
								}
						}
				} else if ($data_type == 14) { // address book
						$sqlUser = "SELECT fname, lname, specialty FROM users WHERE id = ?";
						$resultUser = sqlQuery(array($currValue));
						$userName = $resultUser['lname'];
						if ($resultUser['fname']) $userName .= ", " . $urow['fname'];
						//$s = htmlspecialchars($uname,ENT_NOQUOTES);
						$data[$fieldID] = htmlspecialchars($userName,ENT_NOQUOTES);
				} else if ($data_type == 15) { // billing code
						//$s = htmlspecialchars($currvalue,ENT_NOQUOTES);
						$data[$fieldID] = htmlspecialchars($currValue,ENT_NOQUOTES);
				} else if ($data_type == 21) { // a set of labeled checkboxes
						$avalue = explode('|', $currValue);
						$sqlListOption = "SELECT * FROM list_options
																		WHERE list_id = ?
																		ORDER BY seq, title";
						$resultListOption = sqlStatement($sqlListOption, array($listID) );
						$count = 0;
						while ($listRow = sqlFetchArray($lres)) {
								$option_id = $listRow['option_id'];
								if (in_array($option_id, $avalue)) {
										if ($count++) $s .= "<br />";
										// Added 5-09 by BM - Translate label if applicable
										$s .= htmlspecialchars(xl_list_label($listRow['title']),ENT_NOQUOTES);
								}
						}
				}
				
				return $data;
		}*/
		
		// Pharmacies Details
		/*public function getPharmacies()
		{
				$sql = "SELECT d.id,
												d.name,
												a.line1,
												a.city,
												p.area_code,
												p.prefix,
												p.number
										FROM pharmacies AS d
										LEFT OUTER JOIN addresses AS a
										ON a.foreign_id = d.id
										LEFT OUTER JOIN phone_numbers AS p
										ON p.foreign_id = d.id
										AND p.type = 2 
										ORDER BY name, area_code, prefix, number";
				$result = sqlStatement($sql);
				$row = sqlFetchArray($result);
				return $row;
		}*/
		
		/**
		 * Procedure Providers for Lab
		 * function getProcedureProviders
		 * List all Procedure Providers
		 */
		public function getProcedureProviders()
		{
				$arr = array();
				$sql = "SELECT pp.*
										FROM procedure_providers AS pp 
										ORDER BY pp.name";
				$result = sqlStatement($sql);
				$i = 0;
				while ($row = sqlFetchArray($result)) {
						$arr[$i]['ppid']					= $row['ppid'];
						$arr[$i]['name'] 					= htmlspecialchars($row['name'],ENT_QUOTES);
						$arr[$i]['npi'] 					= htmlspecialchars($row['npi'],ENT_QUOTES);
						$arr[$i]['protocol'] 			= htmlspecialchars($row['protocol'],ENT_QUOTES);
						$arr[$i]['DorP'] 					= htmlspecialchars($row['DorP'],ENT_QUOTES);
						$arr[$i]['send_app_id'] 	= htmlspecialchars($row['send_app_id'],ENT_QUOTES);
						$arr[$i]['send_fac_id'] 	= htmlspecialchars($row['send_fac_id'],ENT_QUOTES);
						$arr[$i]['recv_app_id'] 	= htmlspecialchars($row['recv_app_id'],ENT_QUOTES);
						$arr[$i]['recv_fac_id'] 	= htmlspecialchars($row['recv_fac_id'],ENT_QUOTES);
						$arr[$i]['remote_host'] 	= htmlspecialchars($row['remote_host'],ENT_QUOTES);
						$arr[$i]['login'] 				= htmlspecialchars($row['login'],ENT_QUOTES);
						$arr[$i]['password'] 			= htmlspecialchars($row['password'],ENT_QUOTES);
						$arr[$i]['orders_path'] 	= htmlspecialchars($row['orders_path'],ENT_QUOTES);
						$arr[$i]['results_path'] 	= htmlspecialchars($row['results_path'],ENT_QUOTES);
						$arr[$i]['notes'] 				= htmlspecialchars($row['notes'],ENT_QUOTES);

						if ($row['remote_host'] != '' && $row['login'] != '' && $row['password'] != '') {
								$arr[$i]['labtype']	= 'External';
						} else {
								$arr[$i]['labtype']	= 'Local';
						}
						$i++;
				}
				return $arr;
		}
}