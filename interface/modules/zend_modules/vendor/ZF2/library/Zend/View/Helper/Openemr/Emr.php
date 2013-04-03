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
}