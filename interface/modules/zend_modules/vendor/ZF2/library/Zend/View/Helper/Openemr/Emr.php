<?php
namespace Zend\View\Helper\Openemr;
use Zend\View\Helper\AbstractHelper;

class Emr extends AbstractHelper
{
    public function getList($list_id)
    {
        $res = sqlStatement("SELECT * FROM list_options WHERE list_id=? ORDER BY seq, title",array($list_id));
        $rows[0] = array (
		'value' => '',
		'label' => xlt('Unassigned'),
		'selected' => TRUE,
		'disabled' => FALSE
	);
	$i = 1;
	
	while($row=sqlFetchArray($res)) {
		$rows[$i] = array (
			'value' => htmlspecialchars($row['option_id'],ENT_QUOTES),
			'label' => xlt($row['title']),
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
    
    public function getLabs()
    {
	$res = sqlStatement("SELECT ppid, name FROM procedure_providers ORDER BY name, ppid"); 
	$rows[0] = array (
		'value' => '0',
		'label' => xlt('Local Lab'),
		'selected' => TRUE,
		'disabled' => FALSE
	);
	$i = 1;
	
	while($row=sqlFetchArray($res)) {
		$rows[$i] = array (
			'value' => $row['ppid'],
			'label' => $row['name'],
		);
		$i++;
	}
	return $rows;
    }
}