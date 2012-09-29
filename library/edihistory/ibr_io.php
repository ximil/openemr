<?php
/*
 * ibr_io.php
 * 
 * Copyright 2012 Kevin McCormick Longview, Texas
 * 
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 or later.  You should have 
 * received a copy of the GNU General Public License along with this program; 
 * if not, write to the Free Software Foundation, Inc., 
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *  <http://opensource.org/licenses/gpl-license.php>
 * 
 * 
 * @author Kevin McCormick
 * @link: http://www.open-emr.org
 * @package OpenEMR
 * @subpackage ediHistory
 */
 
/**
 * Get some values from php ini functions for interface
 * 
 * @return array     json
 */
function ibr_inivals() {
    $ival = array();
    $td = basename(sys_get_temp_dir());
    $ival['mfilesize'] = ini_get('upload_max_filesize');
    $ival['mfuploads'] = ini_get('max_file_uploads');
    $ival['pmsize'] = ini_get('post_max_size');
    $ival['tmpdir'] = $td;
    $json = json_encode($ival); 
    //
    return $json;
}

/**
 * read or write simple notes to a text file
 * 
 * @uses csv_notes_file()
 * @return string
 */
function ibr_history_notes() {
	//
	if (isset($_GET['getnotes']) && $_GET['getnotes'] == 'yes') {
		$out_text = csv_notes_file();
		$str_html = str_replace('|:|', PHP_EOL, $out_text);
	} elseif (isset($_POST['putnotes']) && $_POST['putnotes'] == 'yes') {
		$notetext = $_POST['tnotes'];
		$notetext = str_replace(PHP_EOL, '|:|', $notetext);
		$filtered = filter_var($notetext, FILTER_SANITIZE_STRING);
		//echo $filtered .PHP_EOL;
		$str_html = csv_notes_file($filtered, false);
	}
	return $str_html;
}

/**
 * generate the heading string for an html page
 * 
 * @return string     html heading stanza
 */
function ibr_html_heading($option, $title='') {
	//
	//if (!is_string($title)) { $title=''; }
    $title = (is_string($title)) ? $title : '';
    $srcdir = $GLOBALS['srcdir'];
    $webdir = $GLOBALS['webroot'];
    
	$str_html = "<!DOCTYPE html>".PHP_EOL."<html>".PHP_EOL."<head>".PHP_EOL;
	$str_html .= "  <meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" />".PHP_EOL;
	$str_html .= "  <title>##TITLE##</title>".PHP_EOL;
	//$str_html .= "  <link rel='stylesheet' href='jscript/style/csv_new.css' type='text/css' media='print, projection, screen' />".PHP_EOL;
    $str_html .= "<link rel=\"stylesheet\" href=\"$webdir/library/css/edi_history.css\" type=\"text/css\" />".PHP_EOL;
    //$str_html .= " <link rel='stylesheet' href='../css/edi_history.css' type='text/css' />".PHP_EOL;
	$str_html .= "</head>".PHP_EOL."<body>".PHP_EOL;
		
	if (!strpos("|newfiles|eradisplay|x12display|csvtable|textdisplay|readme", $option)) {
		$str_html = str_replace('##TITLE##', 'Error', $str_html);
		return $str_html;
	} elseif ($option == 'newfiles') {
		$str_html = str_replace('##TITLE##', 'Process New Files '.$title, $str_html);
	} elseif ($option == 'eradisplay') {
		$str_html = str_replace('##TITLE##', 'ERA Display '.$title, $str_html);
	} elseif ($option == 'claimstatus') {
		$str_html = str_replace('##TITLE##', 'Claim Status '.$title, $str_html);
	} elseif ($option == 'x12display') {
		$str_html = str_replace('##TITLE##', 'x12 File '.$title, $str_html);
	} elseif ($option == 'csvtable') {
		$str_html = str_replace('##TITLE##', 'CSV Table '.$title, $str_html);
	} elseif ($option == 'textdisplay') {
		$str_html = str_replace('##TITLE##', 'Text '.$title, $str_html);
	} elseif ($option == 'readme') {
		$str_html = str_replace('##TITLE##', 'Readme '.$title, $str_html);
	} else {
		$str_html = str_replace('##TITLE##', 'Unknown '.$title, $str_html);
	}
	//
	return $str_html;
}

/**
 * generate the trailing tags for html page
 * 
 * @return string
 */
function ibr_html_tail() {
	$str_html = PHP_EOL."</body></html>";
	return $str_html;
}

/**
 * call new uploaded files process functions
 * 
 * @todo    save the newfiles lists to file so they can
 *          be re-displayed if user has to close app before
 *          finishing review (need to have csv_write option)
 * 
 * @uses csv_newfile_list()
 * @uses ibr_batch_process_new()
 * @uses ibr_ack_process_new()
 * @uses ibr_997_process_new()
 * @uses ibr_277_process_new()
 * @uses ibr_ebr_process_new()
 * @uses ibr_dpr_process_new()
 * @uses ibr_era_process_new()
 * 
 * @return string  html format
 */
function ibr_disp_newfiles() {
	//
	if (!isset($_POST['NewFiles']) ) {
		// should only be called with this value existing
		$str_html = "Error: invalid value for Process New <br />".PHP_EOL;
		return $str_html;
	}
	$htm = $er = false;
	if (isset($_POST['htmlout'])) {
		$htmval = filter_input(INPUT_POST, 'htmlout', FILTER_SANITIZE_STRING);
		$htm = ($htmval == 'on') ? true : false;
	}
	if (isset($_POST['erronly'])) {
		$errval = filter_input(INPUT_POST, 'erronly', FILTER_SANITIZE_STRING);
		$er = ($errval == 'on') ? true : false;
	}
	$str_html = "<p>Process new files</p>".PHP_EOL;
	//
    $p = csv_parameters();
    $ftype = array_keys($p);
    //
	foreach($ftype as $tp) { 
        $checkdir = false;
        // check for directory contents
        $ckdir = $p[$tp]['directory'];
        if (is_dir($ckdir)) {
            $dh = opendir($ckdir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..') { 
                        $checkdir = true;
                        break;
                    }
                }
                closedir($dh);
            }
        }
        // if false, no files in directory
        if (!$checkdir) { continue; }
        //
		$upload_ar = csv_newfile_list($tp);
		//
		if (count($upload_ar) > 0) {
			if ($tp == 'batch') { 
				$str_html .= ibr_batch_process_new($upload_ar, $htm); 
			} elseif ($tp == 'ack') { 
				$str_html .= ibr_ack_process_new($upload_ar, $htm); 
			} elseif ($tp == 'ta1') { 
				$str_html .= ibr_ta1_process_new($upload_ar, $htm); 
			} elseif ($tp == 'f997') { 
				$str_html .= ibr_997_process_new($upload_ar, $htm, $er); 
			} elseif ($tp == 'f277') { 
				$str_html .= ibr_277_process_new($upload_ar, $htm, $er); 
			} elseif ($tp == 'ibr') { 
				$str_html .= ibr_ebr_process_new_files($upload_ar, 'ibr', $htm, $er);
			} elseif ($tp == 'ebr') { 
				$str_html .= ibr_ebr_process_new_files($upload_ar, 'ebr', $htm, $er);
			} elseif ($tp == 'dpr') { 
				$str_html .= ibr_dpr_process_new($upload_ar, $htm, $er); 
			} elseif ($tp == 'era') { 
				$str_html .= ibr_era_process_new($upload_ar, $htm, $er);
			} elseif ($tp == 'text') { 
				// do nothing 
				continue; 
			} else {
				$str_html .= "unknown type $tp <br />".PHP_EOL;
			}
		} else {
			$str_html .= "No new files for type $tp <br />".PHP_EOL;
		}
	}
	
	return $str_html;
}

/**
 * display the ST...SE segments for a claim from a batch file
 * 
 * @uses csv_file_with_pid_enctr()
 * @uses csv_file_by_controlnum()
 * @uses ibr_batch_get_st_block()
 * @return string
 */
function ibr_disp_claimst() {
	//
	$str_html = '';
	$filename = ''; $clmid = ''; $st02 = '';
	//
	$filename = isset($_GET['fvbatch']) ? filter_input(INPUT_GET, 'fvbatch', FILTER_SANITIZE_STRING) : '';
	//
	$st02 = isset($_GET['stnum']) ? filter_input(INPUT_GET, 'stnum', FILTER_SANITIZE_STRING) : '';
	//
	if (isset($_GET['btpid'])) {
		$clmid = filter_input(INPUT_GET, 'btpid', FILTER_SANITIZE_STRING);	
	} elseif (isset($_POST['enctrbatch'])) {
		$clmid = filter_input(INPUT_POST, 'enctrbatch', FILTER_SANITIZE_STRING);
	} else {
		$clmid = '';
	}
	//
	if ( $clmid == NULL || $clmid === FALSE || !$clmid ) {
		$str_html .= "Invalid claim ID <br />";
		return $str_html;
	}
	//
	if (strlen($filename) >= 13 && strlen($filename) <= 14) {
		// assume we have a bht03 number batch_icn + stnum
		$isa13 = substr($filename, 0, 9);
		$st02 = substr($filename, -4);
        $btname = csv_file_by_controlnum('batch', $isa13);
		if (!$btname) {
			$str_html .= "Failed to identify batch file <br />";
			return $str_html;
		}
		$str_html .= ibr_batch_get_st_block ($btname, $st02, $clmid);
	} elseif (strlen($filename) == 9) {	
		// assume we have a batch_icn
        $btname = csv_file_by_controlnum('batch', $filename);
		if (!$btname) {
			$str_html .= "Failed to identify batch file <br />";
			return $str_html;
		}
		$str_html .= ibr_batch_get_st_block ($btname, $st02, $clmid);
	} elseif (!$filename || strlen($filename) <= 9) {
		// search for file with the claim id
		// (encounter, number, filename,)
		$enc_ar = csv_file_with_pid_enctr($clmid, 'batch', 'ptctln');
		if (is_array($enc_ar) && count($enc_ar) ) { 
			foreach($enc_ar as $enc) { 			
				$str_html .= ibr_batch_get_st_block ( $enc[2], $enc[1] );  
			}
		} elseif( is_string($enc_ar) && count($enc_ar) ) {
			$str_html .= $enc_ar;
		} else {
			$str_html .= "Failed to find the batch file for $clmid <br />";
		} 
		
	} else {
		$str_html .= ibr_batch_get_st_block($filename, '', $clmid );  
	}
	return $str_html;
}

/**
 * display the x12 segments for an era claim remittance advice
 * 
 * @uses csv_file_with_pid_enctr()
 * @uses ibr_era_get_clp_text()
 * @return string
 */
function ibr_disp_eraClp() {
	// get the clp and related segments 
	$str_html = '';
	$era_enc = filter_input(INPUT_POST, 'enctrEra', FILTER_SANITIZE_STRING);	
	$enc_ar = ($era_enc) ? csv_file_with_pid_enctr($era_enc, 'era', 'encounter') : false;
	// $enc_ar is an array [i](pid, encounter, filename)	
	if (is_array($enc_ar) && count($enc_ar) ) {  		
		foreach($enc_ar as $enc) { 
			//$pe = $enc[0] . "-" . $enc[1];
			$pe = $enc[0];
			$str_html .= ibr_era_get_clp_text($pe, $enc[2]); 
		}
	} elseif( count($enc_ar) && is_string($enc_ar) ) {
		$str_html .= $enc_ar;
	} else {
		$str_html .= "Failed to find the remittance for encounter ". strval($era_enc) ."<br />".PHP_EOL;
	}
	return $str_html;
}

/**
 * csv tables filter input and generate table
 * 
 * @uses csv_to_html()
 * @return string
 */
function ibr_disp_csvtable() {
	// 
	$rowp = filter_input(INPUT_POST, 'csvpctrows', FILTER_VALIDATE_INT);
	$ds = filter_input(INPUT_POST, 'csv_date_start', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	$de = filter_input(INPUT_POST, 'csv_date_end', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);  
	$csvfile = filter_input(INPUT_POST, 'csvtables', FILTER_SANITIZE_STRING);	
	//
	$row_pct = ($rowp) ? $rowp/100 : 1;
	if ($ds == NULL || $ds === FALSE ) { $ds = ''; }
	if ($de == NULL) { $e = ($ds) ? date("Y/M/D", time()) : ''; } 
	if ($csvfile == NULL || $csvfile === FALSE ) { 
		// here we have an error and must quit
		$str_html= "<p>Error in CSV table name </p>".PHP_EOL; 
		return $str_html;
	} else {
		$tp_ar = explode('_', $csvfile);
		$tbl_type = ($tp_ar[0] == 'claims') ? 'claim' : 'file';
		$f_type = strval($tp_ar[1]);
		if ($f_type == '999' || $f_type == '997' || $f_type == '277') {
			$f_type = 'f'.$f_type;
		}
	}
	$str_html = csv_to_html($f_type, $tbl_type, $row_pct, $ds, $de);
	//
	return $str_html;
}

/**
 * links for RA info from POST for trace, patient ID, or encounter
 * 
 * @uses csv_file_by_controlnum(
 * @uses csv_file_with_pid_enctr()
 * @uses ibr_era_html_page()
 * @return string
 */
function ibr_disp_era_post() {
	//
	$str_html = '';
	// POST request from ERA tab -- era by pid, encounter, or trace	
	// there will not be a filename supplied with these values
	$trace = ''; $pid = ''; $enctr = ''; $search = '';
	//ibr_era_html_page ( $file_path, $trn_trace=0, $pid_enctr=0, $searchtype='ALL', $fname='835 Remittance Advice')
	if ( isset($_POST['subtrace835']) && isset($_POST['trace835']) && $_POST['trace835'] ) {
		$istrc = true;
		$search = 'trace';
		$trace = filter_input(INPUT_POST, 'trace835', FILTER_SANITIZE_STRING);
        $eft = csv_file_by_controlnum('era', $trace);
        if ($eft) {
            $str_html .= ibr_era_html_page($eft, $trace, 0, $search, $eft);
        } else {
			$str_html .= "Did not find $trace in data <br />".PHP_EOL;
		}
	} elseif ( isset($_POST['subpid835']) && isset($_POST['pid835']) && $_POST['pid835']) {
		$ispid = true; $isenc = false;
		$search = 'pid';
		$pid = filter_input(INPUT_POST, 'pid835', FILTER_SANITIZE_STRING);	
		$ef = ($pid) ? csv_file_with_pid_enctr ($pid, 'era', 'pid') : false;	
	} elseif (isset($_POST['subenctr835']) &&  isset($_POST['enctr835']) && $_POST['enctr835'] ) {
		$isenc = true; $ispid = false;
		$search = 'encounter';
		$enctr = filter_input(INPUT_POST, 'enctr835', FILTER_SANITIZE_STRING);
		$ef = ($enctr) ? csv_file_with_pid_enctr ($enctr, 'era', 'encounter') : false;	
	}
	//
	if ($isenc || $ispid) {
		if ( is_array($ef) && count($ef) ) {
			$fn = array();
			foreach($ef as $val) { 
				// do not repeat filename since all occurences in a file are found
				if ( in_array($val[2], $fn) ) { 
					continue; 
				} else {
					$fn[] = $val[2];
					$str_html .= ibr_era_html_page($val[2], 0, $val[0], $search, $val[2]);
				}
			}
		} else {
			$str_html .= "Did not find $pid $enctr in data <br />".PHP_EOL;
		}
	} 
	//
	return $str_html;
}


/**
 * links for RA info from GET for patient ID or encounter
 * 
 * @uses csv_file_with_pid_enctr()
 * @uses ibr_era_claim_summary()
 * @uses ibr_era_html_page()
 * @return string
 */
function ibr_disp_era_get() {
	//
	$str_html = '';
	//
	// all these should open in a new window or tab (target='_blank') except summary
	$fname = isset($_GET['erafn']) ? filter_input(INPUT_GET, 'erafn', FILTER_SANITIZE_STRING) : '';
	$pe = isset($_GET['pidenc']) ? filter_input(INPUT_GET, 'pidenc', FILTER_SANITIZE_STRING) : '';
	$trace = isset($_GET['trace']) ? filter_input(INPUT_GET, 'trace', FILTER_SANITIZE_STRING) : '';
	$stype = isset($_GET['srchtp']) ? filter_input(INPUT_GET, 'srchtp', FILTER_SANITIZE_STRING) : '';
	$smy = isset($_GET['summary']) ? filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_STRING) : '';
	//
	if (!$fname && $pe && $stype=='encounter') {
		// all RA's for this encounter
		$ef = csv_file_with_pid_enctr($pe, 'era', $stype);	
		if (is_array($ef) && count($ef) ) {
			foreach($ef as $val) {
				//  -- do not repeat filename since all occurences in a file are found
				// (pid-enctr, trace, filename) 
				if ( in_array($val[2], $fn) ) { 
					continue; 
				} else {
					$fn[] = $val[2];
					$pe = $val[0];
					$str_html .= ibr_era_html_page($val[2], 0, $pe, 'encounter', $val[2]);
				}
			}
		} else {
			$str_html .= "Did not find $pe in data <br />".PHP_EOL;
		}
    } elseif (!$fname && $trace) {
        $fname = csv_file_by_controlnum('era', $trace);
        if ($fname) {
            $str_html .=  ibr_era_html_page($fname, $trace, 0, 'trace', $fname);
        } else {
            $str_html .= "Did not find file for trace $trace <br />".PHP_EOL;
        } 
	} elseif ($fname && $pe && $smy=='yes') {
		// payment summary for popup dialog
		$str_html .= ibr_era_claim_summary($fname, $pe);
	} elseif ($fname && $pe && !$smy) {
		// all RA's for this patient ID in transaction
		$str_html .= ibr_era_html_page($fname, 0, $pe, 'encounter', $fname);
	} elseif ($fname && $trace) {
		// RA for this trace number
		$str_html .= ibr_era_html_page($fname, $trace, 0, 'trace', $fname);
	}
	//
	return $str_html;		 
}

function ibr_disp_clmhist() {
	//
	if (isset($_GET['chenctr']) && strlen($_GET['chenctr'])) {
		$pe = filter_input(INPUT_GET, 'chenctr', FILTER_SANITIZE_STRING);
		$str_html = csv_claim_history($pe);
	} else {
		$str_html = "Error in processing request.<br />".PHP_EOL;
	}
	return $str_html;
}
			
/**
 * filter input and generate display for claim status response
 * 
 * @uses ibr_277_response_html()
 * @return string
 */
function ibr_disp_status_resp() {
	//
	$fname = filter_input(INPUT_GET, 'rspfile', FILTER_SANITIZE_STRING);
	$st = ''; $pe = '';
	if (isset($_GET['pidenc']) && strlen($_GET['pidenc'])) {
		$pe = filter_input(INPUT_GET, 'pidenc', FILTER_SANITIZE_STRING);
	} elseif (isset($_GET['rspstnum']) && strpos($_GET['rspstnum'], '_')) {
		// the rspstnum is the 277 ISA13_ST02
		$st = filter_input(INPUT_GET, 'rspstnum', FILTER_SANITIZE_STRING);
	} 
	
	if (!$pe && !$st) {
		$str_html = "No claim identification information for claim status.<br />".PHP_EOL;
	} else {
		$str_html = ibr_277_response_html($fname, '', '', $pe, $st);
	}
	return $str_html;
}

/**
 * display the message part of a DPR response
 * 
 * @uses ibr_dpr_message()
 * @return string
 */ 
function ibr_disp_dpr_message() {
	//
	$fname = filter_input(INPUT_GET, 'dprfile', FILTER_SANITIZE_STRING);
	if (isset($_GET['dprclm']) && strlen($_GET['dprclm'])) {
		$pe = filter_input(INPUT_GET, 'dprclm', FILTER_SANITIZE_STRING);
	}
	if (!$fname || !$pe) {
		$str_html = "Missing file or claim ID.<br />".PHP_EOL;
	} else {
		$str_html = ibr_dpr_message($fname, $pe);
	}
	return $str_html; 
}

/**
 * display the message part of a EBR or IBR response
 * 
 * @uses ibr_ebr_message()
 * @return string
 */ 
function ibr_disp_ebr_message() {
	//
	$fname = ''; $pe = ''; $btfnm = '';
	$fname = filter_input(INPUT_GET, 'ebrfile', FILTER_SANITIZE_STRING);
	if (isset($_GET['ebrclm']) && strlen($_GET['ebrclm'])) {
		$pe = filter_input(INPUT_GET, 'ebrclm', FILTER_SANITIZE_STRING);
	}
	if (isset($_GET['batchfile']) && strlen($_GET['batchfile'])) {
		$btfnm = filter_input(INPUT_GET, 'batchfile', FILTER_SANITIZE_STRING);
	} 	
	if (!$fname || !$pe) {
		$str_html = "Missing file or claim ID.<br />".PHP_EOL;
	} else {
		$str_html = ibr_ebr_message($fname, $pe, $btfnm);
	}
	return $str_html; 
}
	
/**
 * display the message part of a 999 response
 * 
 * @uses ibr_997_errscan()
 * @return string
 */ 
function ibr_disp_997_message() {
	//
	$fname = ''; $akval = ''; $errval = '';
	$fname = filter_input(INPUT_GET, 'fv997', FILTER_SANITIZE_STRING);
	if (isset($_GET['aknum'])) { $akval = filter_input(INPUT_GET, 'aknum', FILTER_SANITIZE_STRING); }
	if (isset($_GET['err997'])) { $errval = filter_input(INPUT_GET, 'err997', FILTER_SANITIZE_STRING); }
	if (!$fname) {
		$str_html = "Missing file name.<br />".PHP_EOL;
	} else {
		$str_html = ibr_997_errscan($fname, $akval);
	}
	return $str_html; 
}

/**
 * display the message part of a ACK or TA1 response
 * 
 * @uses ibr_ack_error()
 * @return string
 */ 
function ibr_disp_ta1_message() {
	//
	$fname = ''; $code = '';
	$fname = filter_input(INPUT_GET, 'ackfile', FILTER_SANITIZE_STRING);
	if (isset($_GET['ackcode'])) $code = filter_input(INPUT_GET, 'ackcode', FILTER_SANITIZE_STRING);
	if ($fname && $code) {
		$str_html = ibr_ack_error($fname, $code);
	} else {
		$str_html = 'Code value invalid <br />'.PHP_EOL;
	}
	return $str_html;
}


/**
 * filter input and display local era file
 * 
 * @uses ibr_upload_files()
 * @uses ibr_era_html_page()
 * @return string
 */
function ibr_disp_erafileUpl() {
	// file uploads; single file controls
	$str_html = '';
	//
	if ( count($_FILES) ) {	
		$f_array = ibr_upload_files($str_html);
		if ( is_array($f_array) && count($f_array) ) { 
			$f_name = basename($f_array['era'][0]);
			$str_html .= ibr_era_html_page($f_array['era'][0], 0, 0, 0, $f_name); 
		} else {
			$str_html .= "no files accepted <br />" . PHP_EOL;
		}
	} else {
		$str_html .= "no file submitted <br />" . PHP_EOL;
	}
	return $str_html;
}

/**
 * uploading of new files
 * 
 * @uses ibr_upload_files()
 * @uses ibr_sort_upload()
 * @return string
 */
function ibr_disp_fileMulti() {
	// multiple file upload
	$str_html = '';
	if ( count($_FILES) ) {	
		$f_array = ibr_upload_files($str_html); 
		if ( is_array($f_array) && count($f_array) ) {
			$str_html .= "sending ".count($f_array)." type for sorting <br />" .PHP_EOL;
			$str_html .= ibr_sort_upload($f_array, $htm, $er);
		} else {
			$str_html .= "no files accepted <br />".PHP_EOL;
		}
	} else {
		$str_html .= "no files submitted <br />" . PHP_EOL;
	}
	$str_html .= PHP_EOL."<form>".PHP_EOL;
	$str_html .= "<input type='button' id='closepopup' value='Close' onclick='self.close()'>".PHP_EOL;
	$str_html .= "</form>".PHP_EOL;	
	//
	return $str_html;
}


/**
 * filter input and generate display of x12 file
 * 
 * @uses csv_filetohtml()
 * @uses ibr_upload_files()
 * @uses ibr_ebr_ebt_name()
 * @return string
 */
function ibr_disp_fileText() {
	//
	$str_html = '';
	//isset($_POST['fileX12']) && isset($_FILES['fileUplx12'])
	if ( count($_FILES) && isset($_FILES['fileUplx12']) ) {	
		$str_html = ibr_html_heading('newfiles', $bn);
		$fn = htmlentities($_FILES['fileUplx12']['name']);
		$f_array = ibr_upload_files($str_html);
		if ( is_array($f_array) && count($f_array) ) { 
			$str_html .= csv_filetohtml($f_array); 
		} else {
			$str_html = ibr_html_heading('error');
			$str_html .= "no files accepted <br />" . PHP_EOL;
		}
	} elseif ( isset($_GET['fvkey']) ) {	
		$fn =  filter_input(INPUT_GET, 'fvkey', FILTER_SANITIZE_STRING);
		// Availity 'readable' versions ibr, ebr, dpr
		$ishr = (isset($_GET['readable']) && $_GET['readable']=='yes') ? true : false;
		if (!$fn) {
			$str_html = ibr_html_heading('error');
		} elseif ($ishr && $fn) {
			$ftxt = ibr_ebr_ebt_name($fn);
			$str_html = ibr_html_heading('textdisplay', $bn);
			$str_html .= csv_filetohtml($ftxt);
		} else {
			$bn = basename($fn);
			$str_html = ibr_html_heading('textdisplay', $bn);
			$str_html .= csv_filetohtml($fn);
		}
	} elseif (isset($_GET['btctln']) ) {
		$btisa13 = filter_input(INPUT_GET, 'btctln', FILTER_SANITIZE_STRING);
		if ($btisa13) {
			//$btname = ibr_batch_find_file_with_controlnum($btisa13);
            $btname = csv_file_by_controlnum('batch', $btisa13);
			$str_html = ibr_html_heading('textdisplay', $bn);
			if ($btname) {
				$str_html .=   csv_filetohtml($btname);
			} else {
				$str_html .= "Failed to identify file with control number $btisa13 <br />".PHP_EOL;
			}
		} else {
			$str_html .= "error in file display <br />";
		}
	} else {
		$str_html = ibr_html_heading('error');
		$str_html .= "error in file display <br />";
	}
	return $str_html;
}

	
/**
 * jQuery adds a special HTTP header for ajax requests
 * 
 * @return bool
 */
function is_xhr() {
  return @ $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] === 'XMLHttpRequest';
}


?>
