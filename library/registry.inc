<?php
//these are the functions used to access the forms registry database
//
//include_once("../../globals.php");
include_once("{$GLOBALS['srcdir']}/sql.inc");

/*function registerForm ( $directory , $sql_run=0 , $unpackaged=1 , $state=0 )
{
	$check = sqlQuery("select state from registry where directory='$directory'");
	if ($check == false)
	{
		$lines = @file($GLOBALS['srcdir']."/../interface/forms/$directory/info.txt");
		if ($lines)
			$name = $lines[0];
		else
			$name = $directory;
		return sqlInsert("insert into registry set
			name='$name',
			state='$state',
			directory='".mysql_escape_string($directory)."',
			sql_run='$sql_run',
			unpackaged='$unpackaged',
			date=NOW()
		");
	}
	return false;
}*/
function registerFormspec ( $directory ,$specialty, $sql_run=0 , $unpackaged=1 , $state=0 )
{
	$check = sqlQuery("select state from registry where directory='$directory' and specialty='$specialty' ");
	if ($check == false)
	{
		if($specialty){
			$path = $GLOBALS['srcdir']."/../interface/forms/$specialty/$directory/info.txt";
		}else{
			$path = $GLOBALS['srcdir']."/../interface/forms/$directory/info.txt";
		}
		$lines = @file($path);
		if ($lines)
			$name = $lines[0];
		else
			$name = $directory;
		return sqlInsert("insert into registry set
			name='$name',
			state='$state',
			directory='".mysql_escape_string($directory)."',
			sql_run='$sql_run',
			unpackaged='$unpackaged',
			date=NOW(),
			specialty='$specialty'
		");
	}
	return false;
}
function updateRegistered ( $id, $mod )
{
	return sqlInsert("update registry set
		$mod,
		date=NOW() 
	where
		id='$id'
	");
}

function getRegistered ( $state="1", $limit="unlimited", $offset="0")
{
	$sql = "select * from registry where state like \"$state\" order by priority, name";
	if ($limit != "unlimited")
		$sql .= " limit $limit, $offset";
	$res = sqlStatement($sql);
	if ($res)
	for($iter=0; $row=sqlFetchArray($res); $iter++)
	{
		$all[$iter] = $row;
	}
	else
		return false;
	return $all;
}

function getRegisteredSpecialty ( $state="1",$specialty=null, $limit="unlimited", $offset="0")
{
	//$common .= $specialty ? "and specialty=\"$specialty\"" : "and (specialty=\"\" OR specialty IS NULL)";
	if($specialty == 'common'){
		$common .= "and specialty=\"\"";
	}elseif($specialty){
		$common .= "and specialty=\"$specialty\"";
	}else{
		$common .= "and (1)";
	}
	$sql = "select * from registry where state like \"$state\" $common order by priority, name";
	if ($limit != "unlimited")
		$sql .= " limit $limit, $offset";
	$res = sqlStatement($sql);
	if ($res)
	for($iter=0; $row=sqlFetchArray($res); $iter++)
	{
		$all[$iter] = $row;
	}
	else
		return false;
	return $all;
}


function getRegistryEntry ( $id, $cols = "*" )
{
	$sql = "select $cols from registry where id='$id'";
	return sqlQuery($sql);
}

function installSQL ( $dir )
{
	$sqltext = $dir."/table.sql";
	if ($sqlarray = @file($sqltext))
	{
		$sql = implode("", $sqlarray);
		//echo "<br>$sql<br><br>";
		$sqla = split(";",$sql);
		foreach ($sqla as $sqlq) {
		  if (strlen($sqlq) > 5) {
		   sqlStatement(rtrim("$sqlq"));
		  }
		}
			
		return true;
	}else
		return false;
}


/* 
 * is a form registered
 *  (optional - and active)
 * in the database?
 * 
 * NOTE - sometimes the Name of a form has a line-break at the end, thus this function might be better
 *
 *  INPUT =   directory => form directory
 *            state => 0=inactive / 1=active
 *  OUTPUT = true or false
 */
function isRegistered ( $directory, $state = 1)
{
    $sql = "select id from registry where ".
            "directory='".$directory.
            "' and state=".$state;
    $result = sqlQuery($sql);
    if ($result['id'] != '') return true;
    return false;
}

?>
