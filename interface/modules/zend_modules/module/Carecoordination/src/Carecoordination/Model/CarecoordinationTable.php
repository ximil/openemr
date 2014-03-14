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
*
*    @author  Vinish K <vinish@zhservices.com>
* +------------------------------------------------------------------------------+
*/
namespace Carecoordination\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Model\ApplicationTable;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\XmlRpc\Generator;

use DOMDocument;
use DOMXpath;

use Document;
use CouchDB;

class CarecoordinationTable extends AbstractTableGateway
{
	public function fetch_cat_id($title)
	{
		$appTable   = new ApplicationTable();
		$query      = "select * from categories where name = ?";
		$result     = $appTable->zQuery($query, array($title));
		$records    = array();
		foreach($result as $row){
			$records[] = $row;
		}
		return $records;
	}
	
	public function document_fetch($data)
	{
		$query      = "SELECT cat.name, u.fname, u.lname, d.imported, d.size, d.date, d.couch_docid, d.couch_revid, d.url AS file_url, d.id AS document_id
					FROM documents AS d
					JOIN categories AS cat ON cat.name = 'CCDA'
					JOIN categories_to_documents AS cd ON cd.document_id = d.id AND cd.category_id = cat.id
					LEFT JOIN users AS u ON u.id = d.owner
					WHERE d.audit_master_approval_status = 1
					ORDER BY date DESC";
		$appTable   = new ApplicationTable();
		$result     = $appTable->zQuery($query, array($data['cat_title']));
		$records    = array();
		foreach($result as $row){
			$records[] = $row;
		}
		return $records;
	}
	
	public function retrieve_action($patient_id="", $document_id) {
		require_once(dirname(__FILE__) . "/../../../../../../../../library/classes/Document.class.php");
		require_once(dirname(__FILE__) . "/../../../../../../../../library/classes/CouchDB.class.php");
		$d 				= new Document($document_id);
		$url 			=  $d->get_url();
		$storagemethod 	= $d->get_storagemethod();
		$couch_docid 	= $d->get_couch_docid();
		$couch_revid 	= $d->get_couch_revid();
		
		if($couch_docid && $couch_revid){
			$couch 		= new CouchDB();
			$data 		= array($GLOBALS['couchdb_dbase'],$couch_docid);
			$resp 		= $couch->retrieve_doc($data);
			$content 	= $resp->data;			
			return $content;
		}
		//strip url of protocol handler
		$url = preg_replace("|^(.*)://|","",$url);
		
		//change full path to current webroot.  this is for documents that may have
		//been moved from a different filesystem and the full path in the database
		//is not current.  this is also for documents that may of been moved to
		//different patients
		// NOTE that $from_filename and basename($url) are the same thing
		$from_all 		= explode("/",$url);
		$from_filename 	= array_pop($from_all);
		$from_patientid = array_pop($from_all);
		if($couch_docid && $couch_revid){
			//for couchDB no URL is available in the table, hence using the foreign_id which is patientID
			$temp_url = $GLOBALS['OE_SITE_DIR'] . '/documents/temp/' . $d->get_foreign_id() . '_' . $from_filename;		
		}
		else{
			$temp_url = $GLOBALS['OE_SITE_DIR'] . '/documents/' . $from_patientid . '/' . $from_filename;
		}
	
		if (file_exists($temp_url)) {
			$url = $temp_url;
		}
		
		if (!file_exists($url)) {
			echo xl('The requested document is not present at the expected location on the filesystem or there are not sufficient permissions to access it.','','',' ') . $url;		
		}
		else{
			$f12 = fopen($url,"r");
			$content_of_file = fread($f12,filesize($url));
			fclose($f12);
			return base64_encode($content_of_file);
		}		
	}
}


