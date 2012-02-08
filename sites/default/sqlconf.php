<?php
//  OpenEMR
//  MySQL Config

$host	= 'localhost';
$port	= '3306';
$login	= 'openemr';
$pass	= 'openemr';
$dbase	= 'openemrCDB';

//Added ability to disable
//utf8 encoding - bm 05-2009
global $disable_utf8_flag;
$disable_utf8_flag = false;

$sqlconf = array();
global $sqlconf;
$sqlconf["host"]= $host;
$sqlconf["port"] = $port;
$sqlconf["login"] = $login;
$sqlconf["pass"] = $pass;
$sqlconf["dbase"] = $dbase;

//couchDB Config

$CDBhost	= 'localhost';
$CDBport	= '5985';
$CDBlogin	= 'jacob';
$CDBpass	= 'jacob';
$CDBdbase	= 'openemrCDBNDW';

$CDBconf = array();
global $CDBconf;
$CDBconf["host"] = $CDBhost;
$CDBconf["port"] = $CDBport;
$CDBconf["user"] = $CDBlogin;
$CDBconf["pass"] = $CDBpass;
$CDBconf["dbase"]= strtolower($CDBdbase);
//////////////////////////
//////////////////////////
//////////////////////////
//////DO NOT TOUCH THIS///
$config = 1; /////////////
//////////////////////////
//////////////////////////
//////////////////////////
?>
