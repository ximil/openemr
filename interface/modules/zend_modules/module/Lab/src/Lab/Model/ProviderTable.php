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
// Author:   Remesh Babu S <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Lab\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ProviderTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function saveProcedureProvider($post)
		{
				$return = array();
				if ($post['ppid'] && $post['ppid'] > 0) {
						$sql = "UPDATE procedure_providers SET name = ?, 
																										npi = ?,           
																										send_app_id = ?, 
																										send_fac_id = ?, 
																										recv_app_id = ?, 
																										recv_fac_id = ?, 
																										DorP = ?, 
																										protocol = ?, 
																										remote_host = ?, 
																										login = ?, 
																										password = ?, 
																										orders_path = ?, 
																										results_path = ?, 
																										notes = ?   
																						WHERE ppid = ?";
						$return = sqlQuery($sql, array($post['name'],
																								$post['npi'],
																								$post['send_app_id'],
																								$post['send_fac_id'],
																								$post['recv_app_id'],
																								$post['recv_fac_id'],
																								$post['DorP'],
																								$post['protocol'],
																								$post['remote_host'],
																								$post['login'],
																								$post['password'],
																								$post['orders_path'],
																								$post['results_path'],
																								$post['notes'],
																								$post['ppid'])
																		);
				} else {
						$sql = "INSERT INTO procedure_providers SET name = ?, 
																										npi = ?,           
																										send_app_id = ?, 
																										send_fac_id = ?, 
																										recv_app_id = ?, 
																										recv_fac_id = ?, 
																										DorP = ?, 
																										protocol = ?, 
																										remote_host = ?, 
																										login = ?, 
																										password = ?, 
																										orders_path = ?, 
																										results_path = ?, 
																										notes = ?";
						$return = sqlQuery($sql, array($post['name'],
																								$post['npi'],
																								$post['send_app_id'],
																								$post['send_fac_id'],
																								$post['recv_app_id'],
																								$post['recv_fac_id'],
																								$post['DorP'],
																								$post['protocol'],
																								$post['remote_host'],
																								$post['login'],
																								$post['password'],
																								$post['orders_path'],
																								$post['results_path'],
																								$post['notes'])
																		);
				}
				return $return;
		}
		
		// Delete Procedure Provider
		public function deleteProcedureProvider($post)
		{
				if ($post['ppid'] && $post['ppid'] > 0) {
						$sql = "DELETE FROM procedure_providers
														WHERE ppid=?";
						$return = sqlQuery($sql, array($post['ppid']));
				}
		}
}


