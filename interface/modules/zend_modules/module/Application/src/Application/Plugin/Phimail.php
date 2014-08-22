<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*    @author  Riju KP <rijukp@zhservices.com>
* +------------------------------------------------------------------------------+
*/
namespace Application\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Model\ApplicationTable;
use Application\Listener\Listener;

class Phimail extends AbstractPlugin 
{
  protected $application;
  /**
   * 
   * Application Table Object 
   * Listener Oblect
   * @param type $sm Service Manager
   */
  public function __construct($sm)
  { 
    $sm->get('Zend\Db\Adapter\Adapter');
    $this->application    = new ApplicationTable();
    $this->listenerObject = new Listener;
    
  }
  /**
 * Connect to a phiMail Direct Messaging server 
 */

  public function phimail_connect(&$phimail_error) 
  {
       if ($GLOBALS['phimail_enable'] == false) {
          $phimail_error = 'C1';
          return false; //for safety
       }

       $phimail_server   = @parse_url($GLOBALS['phimail_server_address']);
       $phimail_username = $GLOBALS['phimail_username'];
       $phimail_password = $GLOBALS['phimail_password'];
       $phimail_cafile   = dirname(__FILE__) . '/../sites/' . $_SESSION['site_id']. '/documents/phimail_server_pem/phimail_server.pem';
       if (!file_exists($phimail_cafile)) $phimail_cafile='';
       

       $phimail_secure = true;
       switch ($phimail_server['scheme']) {
           case "tcp"  :
           case "http" : $server = "tcp://".$phimail_server['host'];
                         $phimail_secure = false;
                         break;
           case "https": $server = "ssl://" . $phimail_server['host']. ':' . $phimail_server['port'];
                         break;
           case "ssl"  :
           case "sslv3":
           case "tls"  : $server = $GLOBALS['phimail_server_address'];
                         break;
           default     : $phimail_error = 'C2';
                         return false;
       }
       if ($phimail_secure) {
          $context = stream_context_create();
          if ($phimail_cafile != '' &&
                (!stream_context_set_option($context, 'ssl', 'verify_peer', true) ||
                 !stream_context_set_option($context, 'ssl', 'cafile', $phimail_cafile))) {
             $phimail_error = 'C3';
             return false;
          }
          $socket_tries = 0;
          $fp = false;
          while ($socket_tries < 3 && !$fp) {
             $socket_tries++;
             $fp = @stream_socket_client($server, $err1, $err2, 10, STREAM_CLIENT_CONNECT, $context);
          }
          if (!$fp) {
             if ($err1 == '111') $err2 = $this->listenerObject->z_xlt('Server may be offline');
             if ($err2 == '') $err2 = $this->listenerObject->z_xlt('Connection error');
             $phimail_error = "C4 $err1 ($err2)";
          }
       } else {
          $fp = @fsockopen($server,$phimail_server['port']);
       }
       return $fp;
    }
    
      /**
     * Helper functions
     */
    public function phimail_write($fp,$text) 
    {
       fwrite($fp,$text);
       fflush($fp);
    }
    public function phimail_write_expect_OK($fp,$text)
    {     
       \Application\Plugin\Phimail::phimail_write($fp,$text);
       $ret = fgets($fp,256);
       if($ret!="OK\n") { //unexpected error
          \Application\Plugin\Phimail::phimail_close($fp);
          return $ret;
       }
       return TRUE;
    }

    public function phimail_close($fp) 
    {
       fwrite($fp,"BYE\n");
       fflush($fp);
       fclose($fp);
    }
    /**
     * Connect to a phiMail Direct Messaging server and check for any incoming status
     * messages related to previously transmitted messages or any new messages received.
     */

    public function phimail_check() 
    {
       $appTable   = new ApplicationTable();
       $fp = $this->phimail_connect($err);
       if ($fp===false) {
          $this->phimail_logit(0,'could not connect to server').' '.$err;
          return;
       }
       $phimail_username = $GLOBALS['phimail_username'];
       $phimail_password = $GLOBALS['phimail_password'];

       $ret = $this->phimail_write_expect_OK($fp,"AUTH $phimail_username $phimail_password\n");
       if($ret!==TRUE) return; //authentication error

       if(!($notifyUsername = $GLOBALS['phimail_notify'])) $notifyUsername='admin'; //fallback

       while (1) {
          $this->phimail_write($fp,"CHECK\n");
          $ret=fgets($fp,512);

          if($ret=="NONE\n") { //nothing to process
              $this->phimail_close($fp);
        $this->phimail_logit(1,"message check completed");
              return;
          }
          else if(substr($ret,0,6)=="STATUS") {
        //Format STATUS message-id status-code [additional-information]
              $val=explode(" ",trim($ret),4);
        $sql='SELECT * from direct_message_log WHERE msg_id = ?';
        $res = $appTable->zQuery($sql,array($val[1]));
        if ($res===FALSE) { //database problem
           $this->phimail_close($fp);
           $this->phimail_logit(0,"database problem");
           return;
        }
        if (($msg=sqlFetchArray($res))===FALSE) {
           //no match, so log it and move on (should never happen)
           $this->phimail_logit(0,"NO MATCH: ".$ret);
           $ret = $this->phimail_write_expect_OK($fp,"OK\n"); 
           if($ret!==TRUE) return; else continue; 
        }

              //if we get here, $msg contains the matching outgoing message record
        if($val[2]=='failed') {
           $success=0;
           $status='F';
        } else if ($val[2]=='dispatched') {
           $success=1;
           $status='D';
              } else {
           //unrecognized status, log it and move on (should never happen)
           $ret = "UNKNOWN STATUS: ".$ret;
           $success=0;
           $status='U';
              }

        $this->phimail_logit($success,$ret,$msg['patient_id']);

        if (!isset($val[3])) $val[3]="";
        $sql = "UPDATE direct_message_log SET status=?, status_ts=NOW(), status_info=? WHERE msg_type='S' AND msg_id=?";
        $res = $appTable->zQuery($sql,array($status,$val[3],$val[1]));
        if ($res===FALSE) { //database problem
           $this->phimail_close($fp);
           $this->phimail_logit(0,"database problem updating: ".$val[1]);
           return;
        }

        if (!$success) {
                 //notify local user of failure
                 $sql = "SELECT username FROM users WHERE id = ?";
                 $res2 = $appTable->zQuery($sql, array($msg['user_id']));
                 $fail_user = ($res2 === FALSE || ($user_row = sqlFetchArray($res2)) === FALSE) ?
                    'unknown (see log)' : $user_row['username'];
                 $fail_notice = 'Sent by:' . ' ' . $fail_user . '(' . $msg['user_id'] . ') ' . 'on' . ' ' . $msg['create_ts']
                    . "\n" . 'Sent to:' . ' ' . $msg['recipient'] . "\n" . 'Server message:' . ' ' . $ret;
           $this->phimail_notify( 'Direct Messaging Send Failure.', $fail_notice);
                 $pnote_id = addPnote($msg['patient_id'], 
                    "FAILURE NOTICE: Direct Message Send Failed." . "\n\n$fail_notice\n",
                    0, 1, "Unassigned", $notifyUsername, "", "New", "phimail-service");
              }

        //done with this status message
        $ret = $this->phimail_write_expect_OK($fp,"OK\n");
        if($ret!==TRUE) {
                 $this->phimail_close($fp);
           return;
              } 

          }

          else if(substr($ret,0,4)=="MAIL") {

             $val = explode(" ",trim($ret),5); // MAIL recipient sender #attachments msg-id
             $recipient=$val[1];
       $sender=$val[2];
       $att=(int)$val[3];
       $msg_id=$val[4];

             //request main message
       $ret2 = $this->phimail_write_expect_OK($fp,"SHOW 0\n");
             if($ret2!==TRUE) {
                phimail_close($fp);
                return;
             }

       //get message headers
             $hdrs="";
             while (($next_hdr = fgets($fp,1024)) != "\n") 
                $hdrs .= $next_hdr;

       $mime_type=fgets($fp,512);
       $mime_info=explode(";",$mime_type);
             $mime_type_main=strtolower($mime_info[0]);

       //get main message body
             $body_len=fgets($fp,256);
             $body=phimail_read_blob($fp,$body_len);
       if ($body===FALSE) {
         $this->phimail_close($fp);
         return;
             }

             $att2=fgets($fp,256);
       if($att2!=$att) { //safety for mismatch on attachments
          $this->phimail_close($fp);
                return;
             }

       //get attachment info
       if($att>0) {
          for ($attnum=0;$attnum<$att;$attnum++) {
        if(  ($attinfo[$attnum]['name']=fgets($fp,1024)) === FALSE
          || ($attinfo[$attnum]['mime']=fgets($fp,1024)) === FALSE
          || ($attinfo[$attnum]['desc']=fgets($fp,1024)) === FALSE) {
             $this->phimail_close($fp);
             return;
          }
           }
        }

        //main part gets stored as document if not plain text content
        //(if plain text it will be the body of the final pnote)
              $all_doc_ids = array();
        $doc_id=0;
              $att_detail="";
        if ($mime_type_main != "text/plain") {
           $name = uniqid("dm-message-") . $this->phimail_extension($mime_type_main);
                 $doc_id = $this->phimail_store($name,$mime_type_main,$body);
                 if (!$doc_id) { 
        $this->phimail_close($fp);
        return;
           }
           $idnum=$doc_id['doc_id']; 
                 $all_doc_ids[] = $idnum;
           $url=$doc_id['url']; 
           $url=substr($url,strrpos($url,"/")+1);
                 $att_detail = "\n" . "Document" . " $idnum (\"$url\"; $mime_type_main; " . 
        filesize($body) . " bytes) Main message body";
        }

        //download and store attachments
              for($attnum=0;$attnum<$att;$attnum++) {
           $ret2 = $this->phimail_write_expect_OK($fp,"SHOW " . ($attnum+1) . "\n");
           if ($ret2!==TRUE) {
        phimail_close($fp);
        return;
           }

           //we can ignore next two lines (repeat of name and mime-type)
           if ( ($a1=fgets($fp,512))===FALSE || ($a2=fgets($fp,512))===FALSE ) {
        $this->phimail_close($fp);
        return;
           }

           $att_len = fgets($fp,256); //length of file
           $attdata = $this->phimail_read_blob($fp,$att_len);
           if ($attdata===FALSE) {
        $this->phimail_close($fp);
        return;
           }
                 $attinfo[$attnum]['file']=$attdata;

           $req_name = trim($attinfo[$attnum]['name']);
           $req_name = (empty($req_name) ? $attdata : "dm-") . $req_name;
           $attinfo[$attnum]['mime'] = explode(";",trim($attinfo[$attnum]['mime']));
           $attmime = strtolower($attinfo[$attnum]['mime'][0]);
           $att_doc_id = $this->phimail_store($req_name, $attmime, $attdata);
                 if (!$att_doc_id) { 
        $this->phimail_close($fp);
        return;
           }
           $attinfo[$attnum]['doc_id']=$att_doc_id;
           $idnum=$att_doc_id['doc_id']; 
                 $all_doc_ids[] = $idnum;
           $url=$att_doc_id['url']; 
           $url=substr($url,strrpos($url,"/")+1);
                 $att_detail = $att_detail . "\n" . "Document" . " $idnum (\"$url\"; $attmime; " . 
         filesize($attdata) . " bytes) " . trim($attinfo[$attnum]['desc']);
       }

       if ($att_detail != "") 
         $att_detail = "\n\n" . "The following documents were attached to this Direct message:" . $att_detail;

       $ret2 = $this->phimail_write_expect_OK($fp,"DONE\n"); //we'll check for failure after logging.

       //logging only after succesful download, storage, and acknowledgement of message
             $sql = "INSERT INTO direct_message_log (msg_type,msg_id,sender,recipient,status,status_ts,user_id) " .
                "VALUES ('R', ?, ?, ?, 'R', NOW(), ?)";
             $res = $appTable->zQuery($sql,array($msg_id,$sender,$recipient,phimail_service_userID()));

       $this->phimail_logit(1,$ret);

       //alert appointed user about new message
       switch($mime_type_main) {

         case "text/plain":
           $body_text = @file_get_contents($body); //this was not uploaded as a document
           unlink($body);
                 $pnote_id = addPnote(0,"Direct Message Received." . "\n$hdrs\n$body_text$att_detail",
                   0, 1, "Unassigned", $notifyUsername, "", "New", "phimail-service");
           break;

               default:
           $note = "Direct Message Received." . "\n$hdrs\n"
        . "Message content is not plain text so it has been stored as a document." . $att_detail;
           $pnote_id = addPnote(0, $note, 0, 1, "Unassigned", $notifyUsername, "", "New", "phimail-service");
           break;

             }

       foreach ($all_doc_ids as $doc_id) setGpRelation(1, $doc_id, 6, $pnote_id);

       if ($ret2!==TRUE) { 
          $this->phimail_close();
          return; 
             }

          }
          else { //unrecognized or FAIL response
              $this->phimail_close($fp);
              return;
          } 
       }
    }

  

    public function phimail_logit($success,$text,$pid=0,$event="direct-message-check") 
    {
       newEvent($event,"phimail-service",0,$success,$text,$pid);
    }

    /**
     * Read a blob of data into a local temporary file
     * @param $len number of bytes to read
     * @return the temp filename, or FALSE if failure
     */
    public function phimail_read_blob($fp,$len) 
    {

       $fpath=$GLOBALS['temporary_files_dir'];
       if(!@file_exists($fpath)) {
         return FALSE;
       }
       $name = uniqid("direct-");
       $fn = $fpath . "/" . $name . ".dat";
       $dup = 1;
       while(file_exists($fn)) {
         $fn = $fpath . "/" . $name . "." . $dup++ . ".dat";
       }

       $ff = @fopen ($fn, "w");
       if(!$ff) return FALSE;

       $bytes_left=$len;
       $chunk_size=1024;
       while (!feof($fp) && $bytes_left>0) {
          if ($bytes_left < $chunk_size ) $chunk_size = $bytes_left;
          $chunk = fread($fp,$chunk_size);
          if($chunk===FALSE || @fwrite($ff,$chunk)===FALSE) {
       @fclose($ff);
       @unlink($fn);
             return FALSE;
          }
          $bytes_left -= strlen($chunk);
       }
       @fclose($ff);
       return($fn);
    }

    /**
     * Return a suitable filename extension based on MIME-type
     * (very limited, default is .dat)
     */
    public function phimail_extension($mime) 
    {
      $m=explode("/",$mime);
      switch($mime) {
      case 'text/plain': 
        return (".txt");
      default:
      }
      switch($m[1]) {
      case 'html':
      case 'xml':
      case 'pdf':
        return (".".$m[1]);
      default:
        return (".dat");
      }
    }

    public function phimail_service_userID($name='phimail-service') 
    { 
       $appTable   = new ApplicationTable();
       $sql = "SELECT id FROM users WHERE username=?";
       if (($r = $appTable->zQuery($sql,array($name))) === FALSE ||
           ($u = sqlFetchArray($r)) === FALSE) {
          $user=1; //default if we don't have a service user
       } else {
          $user = $u['id'];
       }
       return ($user);
    }


    /**
     * Registers an attachment or non-text message file using the existing Document structure
     * @return Array(doc_id,URL) of the file as stored in documents table, false = failure
     */
    public function phimail_store($name,$mime_type,$fn) 
    {

        // Collect phimail user id
        $user = $this->phimail_service_userID();

        // Import the document
        $return = addNewDocument($name,$mime_type,$fn,0,filesize($fn),$user,'direct');

        // Remove the temporary file
        @unlink($fn);

        // Return the result
        return $return;
    }

    /**
     * Send an error notification or other alert to the notification address specified in globals.
     * (notification email function modified from interface/drugs/dispense_drug.php)
     * @return true if notification successfully sent, false otherwise
     */
    public function phimail_notify($subj,$body) 
    {
      $recipient = $GLOBALS['practice_return_email_path'];
      if (empty($recipient)) return false;
      $mail = new PHPMailer();
      $mail->SetLanguage("en", $GLOBALS['fileroot'] . "/library/" );
      $mail->From = $recipient;
      $mail->FromName = 'phiMail Gateway';
      $mail->isMail();
      $mail->Host = "localhost";
      $mail->Mailer = "mail";
      $mail->Body = $body;
      $mail->Subject = $subject;
      $mail->AddAddress($recipient);
      return ($mail->Send());
    }
}