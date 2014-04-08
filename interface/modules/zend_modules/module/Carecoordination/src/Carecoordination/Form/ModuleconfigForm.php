<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2014 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
*    @author  Vinish K <vinish@zhservices.com>
* +------------------------------------------------------------------------------+
*/

namespace Carecoordination\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use \Application\Model\ApplicationTable;

class ModuleconfigForm extends Form
{	
    public function __construct(AdapterInterface $dbAdapter)
    {
		$this->application 	= new ApplicationTable;
		parent::__construct('configuration');
        $this->setAttribute('method', 'post');
        
		/*
		* Automatic SignOff settings
		*/
		$this->add(array(
				'name' 		=> 'hie_auto_sign_off_id',
				'attributes' 	=> array(
						'type'  	=> 'text',
						'id'    	=> 'hie_auto_sign_off_id'
					),
				'options' => array(
						'label' 	=> \Application\Listener\Listener::z_xlt('Auto Sign-Off [days]'),
					),
			));
		
		/*
		* Automatic Tranfer settings
		*/
		$this->add(array(
			'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'hie_auto_send_id',
				'attributes' 	=> array(
						'id'    	=> 'hie_auto_send_id'
					),
				'options' => array(
						'label' 		=> \Application\Listener\Listener::z_xlt('Auto Send'),
				'checked_value' 	=> 'yes',
						'unchecked_value' 	=> 'no'
					),
			));
		
		/*
		* Author settings
		*/
		$this->add(array(
				'name' 	=> 'hie_author_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_author_fname'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Author'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Data Enterer settings
		*/
		$this->add(array(
				'name' 		=> 'hie_data_enterer_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_data_enterer_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Data Enterer'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Informant settings
		*/
		$this->add(array(
				'name' 	=> 'hie_informant_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_informant_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Informant'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Personal Informant settings
		*/
		$this->add(array(
				'name' 	=> 'hie_personal_informant_id',
				'attributes'=> array(
						'type'  => 'text',
						'id'    => 'hie_personal_informant_id'
					),
				'options' => array(
						'label' => \Application\Listener\Listener::z_xlt('Informant'),
					),
			));
		
		/*
		* Custodian settings
		*/
		$this->add(array(
				'name' 	=> 'hie_custodian_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_custodian_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Custodian'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Recipient settings
		*/
		$this->add(array(
				'name' 	=> 'hie_recipient_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_recipient_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Recipient'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Legal Authenticator settings
		*/
		$this->add(array(
				'name' 	=> 'hie_legal_authenticator_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_legal_authenticator_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Legal Authenticator'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Authenticator settings
		*/
		$this->add(array(
				'name' 	=> 'hie_authenticator_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_authenticator_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Authenticator'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* Primary Care Provider settings
		*/
		$this->add(array(
				'name' 	=> 'hie_primary_care_provider_id',
				'type'  	=> 'Zend\Form\Element\Select',
				'attributes'=> array(
					'class' 	=> '',
					'data-options' 	=> 'required:true',
					'editable' 	=> 'false',
					'required' 	=> 'required',
					'id' 		=> 'hie_primary_care_provider_id'
				),
				'options' => array(
					'label' 	=> \Application\Listener\Listener::z_xlt('Primary Care Provider'),
					'value_options' => $this->getUsers(),
				),
			));
		
		/*
		* MIRTH IP settings
		*/
		$this->add(array(
				'name' 	=> 'hie_mirth_ip',
				'attributes'=> array(
						'type'  => 'text',
						'id'    => 'hie_mirth_ip'
					),
				'options' => array(
						'label' => \Application\Listener\Listener::z_xlt('Mirth IP'),
					),
			));
		
		/*
		* MIRTH Username
		*/
		$this->add(array(
				'name' 	=> 'hie_mirth_username',
				'attributes'=> array(
						'type'  => 'text',
						'id'    => 'hie_mirth_username'
					),
				'options' => array(
						'label' => \Application\Listener\Listener::z_xlt('Username'),
					),
			));
		
		/*
		* MIRTH Password
		*/
		$this->add(array(
				'name' 	=> 'hie_mirth_password',
				'attributes'=> array(
						'type'  => 'password',
						'id'    => 'hie_mirth_password'
					),
				'options' => array(
						'label' => \Application\Listener\Listener::z_xlt('Password'),
					),
			));
    }
	
	/**
	* Function getOptions
	* Get Select Options 
	* 
	* @return array
	*/
    public function getUsers()
    {
		$users = array('0' => '');
		$res = $this->application->zQuery(("SELECT id, fname, lname, street, city, state, zip  FROM users WHERE abook_type='ccda'"));
		foreach($res as $row){
			$users[$row['id']] = $row['fname']." ".$row['lname'];
		}
		return $users;
    }
}
