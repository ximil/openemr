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
		$rows[$i] = array (
			'value' => $row['id'],
			'label' => $row['fname']." ".$row['lname'],
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
	$res = sqlStatement("SELECT ppid,name,remote_host,login,password FROM procedure_providers ORDER BY name, ppid"); 
	//$rows[0] = array (
	//	'value' => '0',
	//	'label' => xlt('Local Lab'),
	//	'selected' => TRUE,
	//	'disabled' => FALSE
	//);
	$i = 0;
	
	while($row=sqlFetchArray($res)) {
	    $value = '';
	    if ($type == 'y') {
		if ($row['remote_host'] != '' && $row['login'] != '' && $row['password'] != '') {
			$value = $row['ppid'] . '|' . 1; // 0 - Local Lab and 1 - External Lab
		} else {
			$value = $row['ppid'] . '|' . 0;
		}
	    } else {
		$value = $row['ppid'];
	    }
	    $rows[$i] = array (
		'value' => $value,
		'label' => $row['name'],
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
}