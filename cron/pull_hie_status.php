<?php
require_once(dirname(__FILE__)."/config.php");
$mirth_url      = $mirth_server_url."/carecoordination/status_update.php";
$ignoreAuth     = true;
$webserver_root = dirname(dirname(__FILE__));
if (IS_WINDOWS) {
    //convert windows path separators
    $webserver_root = str_replace("\\","/",$webserver_root); 
}
$web_root = substr($webserver_root, strlen($_SERVER['DOCUMENT_ROOT']));
// Ensure web_root starts with a path separator
if (preg_match("/^[^\/]/",$web_root)) {
    $web_root = "/".$web_root;
}
$GLOBALS['webroot'] = $web_root;
$sites = array();
$dir = dirname(__FILE__).'/../sites/';
if (is_dir($dir)){
    if ($dh = opendir($dir)){
        while (($file = readdir($dh)) !== false){
            if($file == '.' || $file == '..') continue;
            $sites[] = $file;
        }
    }
}

$client = new SoapClient(null, array(
        'location' => "$mirth_url",
        'uri'      => "urn://hie_status/req"));

foreach($sites as $key => $value){
    echo "<br>--------------------------------------------------<br>";
    require($dir.$value.'/sqlconf.php');
    $GLOBALS['OE_SITE_DIR'] = dirname(__FILE__)."/../sites/".$value;
    $connection = mysql_connect("{$sqlconf['host']}:{$sqlconf['port']}", $sqlconf['login'], $sqlconf['pass']);
    if(!$connection){
        echo "Database connection failed - ".$sqlconf['dbase']." <==> ".$value."<br>";
        continue;
    }
    else{
        echo "Database connection successfull - ".$sqlconf['dbase']." <==> ".$value."<br>";
    }
    if(!mysql_selectdb($sqlconf['dbase'])){
        echo "Cannot select database ".$sqlconf['dbase']."<br>";
        continue;
    }
    include_once(dirname(__FILE__)."/../library/sql.inc");
    require_once(dirname(__FILE__) . "/../library/adodb/adodb.inc.php");
    
    $database               = NewADOConnection("mysql");
    $database->PConnect($host, $login, $pass, $dbase);
    $GLOBALS['adodb']['db'] = $database;
    $GLOBALS['dbh']         = $database->_connectionID;
    
    $to_pull = array();
    $count   = 0;
    $check_table = sqlStatement("SHOW TABLES LIKE 'ccda'");
    if(sqlNumRows($check_table) <= 0) continue;
    $res_pending = sqlStatement("select pid, encounter, time from ccda where status = 0");
    while($row_pending = sqlFetchArray($res_pending)){
        $to_pull[$count]['pid']       = $row_pending['pid'];
        $to_pull[$count]['time']      = $row_pending['time'];
        $to_pull[$count]['encounter'] = $row_pending['encounter'];
        $count++;
    }
    
    $result = $client->getStatusUpdate($value, $to_pull);
    
    foreach($result as $row){
        if(!$row['encounter']){
            $query = "update ccda set status = ? where pid = ? and time = ?";
            sqlQuery($query, array($row['status'], $row['pid'], $row['time']));
        }else{
            $query = "update ccda set status = ? where pid = ? and encounter = ? and time = ?";
            sqlQuery($query, array($row['status'], $row['pid'], $row['encounter'], $row['time']));
        }
    }
}
?>