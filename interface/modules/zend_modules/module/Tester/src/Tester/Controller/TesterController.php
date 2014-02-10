<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Remesh Babu S  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Tester\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Listener\Listener;


class TesterController extends AbstractActionController
{
    protected $testerTable;
    
    public function indexAction()
    {
      /** Common Plugin Example */
      //echo $this->CommonPlugin()->example();
      
      $btn_1 = $this->getTesterTable()->acl_check($_SESSION['authUserID'],"btn_1");
      $btn_2 = $this->getTesterTable()->acl_check($_SESSION['authUserID'],"btn_2");
      return new ViewModel(array(
          'acl_bitton_1' => $btn_1,
          'acl_bitton_2' => $btn_2,

      ));
         
    }
    public function getTesterTable()
    {	
      if (!$this->testerTable) {
        $sm = $this->getServiceLocator();
        $this->testerTable = $sm->get('Tester\Model\TesterTable');
      }
      return $this->testerTable;
    }
    
    public function testingAction()
    {
      //$sql = "SELECT * FROM users";
      //$result = sqlStatement($sql);
      //$sql = "INSERT INTO  patient_data (username, password) values(?, ?)";
      //$result = sqlStatement($sql, array('remesh2', 'test2'));echo '<pre>'; var_dump($result);
      //$sql = "DELETE  FROM patient_data  WHERE id=?";
      //$result = sqlStatement($sql, array('Ms', 2));echo '<pre>'; var_dump($result);
      //while ($row = sqlFetchArray($result)) {
        //echo '123<pre>'; print_r($row); echo '</pre>';
      //}
      echo $statement = "INSERT INTO modules SET mod_id = '1',  mod_name = 'Tester',
                                      mod_active = '0', 
                                      mod_ui_name = 'Tester', 
                                      mod_relative_link = 'public/tester/',
                                      type=1, 
                                      mod_directory = 'Tester', 
                                      date=NOW()";
      $tokens = preg_split("/[\s,(\'\"]+/", $statement);
      
      echo '<pre>'; print_r($tokens); echo '</pre>'; 
      
           if((strcasecmp($tokens[0],"INSERT")==0) || (strcasecmp($tokens[0],"REPLACE")==0)){
        $table = $tokens[2];
        $rid = mysql_insert_id($GLOBALS['dbh']);
	/* For handling the table that doesn't have auto-increment column */
        if ($rid === 0 || $rid === FALSE) {
          if($table == "gacl_aco_map" || $table == "gacl_aro_groups_map" || $table == "gacl_aro_map" || $table == "gacl_axo_groups_map" || $table == "gacl_axo_map")
           $id="acl_id";
          else if($table == "gacl_groups_aro_map" || $table == "gacl_groups_axo_map")
          $id="group_id";
          else
           $id="id";
	  /* To handle insert statements */
          if($tokens[3] == $id){
             for($i=4;$i<count($tokens);$i++){
		 if(strcasecmp($tokens[$i],"VALUES")==0){
                  $rid=$tokens[$i+1];
                     break;
                }// if close
              }//for close
            }//if close
	/* To handle replace statements */
          else if(strcasecmp($tokens[3],"SET")==0){
		 if((strcasecmp($tokens[4],"ID")==0) || (strcasecmp($tokens[4],"`ID`")==0)){
                  $rid=$tokens[6];
           }// if close
        }

	else {		
            return "";
          }
        }
    }
      
      echo 'rid is ' . $rid;
      
      
      
      #############################
      exit;
      $this->getTesterTable()->testingDb();
      exit;
    }
}