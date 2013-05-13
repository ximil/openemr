<?php
namespace Zend\View\Helper\Openemr;
use Zend\View\Helper\AbstractHelper;

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
    
    /*
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
}