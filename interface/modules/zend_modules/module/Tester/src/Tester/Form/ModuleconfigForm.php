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
*    @author  Remesh Babu S <remesh@zhservices.com>
* +------------------------------------------------------------------------------+
*/

namespace Tester\Form;

use Zend\Form\Form;

class ModuleconfigForm extends Form
{
    public function __construct($name = null)
    {
      parent::__construct('configuration');
      $this->setAttribute('method', 'post');
	
      $this->add(array(
                  'name' => 'username',
                  'attributes' => array(
                        'type'  => 'text',
                        'id'    => 'username'
                      ),
                  'options' => array(
                    'label' => 'Username',
                ),
            ));
        
        $this->add(array(
                    'name' => 'test',
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                          'class' => '',
                          'data-options' => 'required:true',
                          'editable' => 'false',
                          'required' => 'required',
                          'id' => 'test'
                        ),
                    'options' => array(
                        'label' => 'Test Options',
                        'value_options' => array(
                                ''  => '--Select--',
                                '1' => 'Testing 1',
                                '2' => 'Testing 2',
                              ),
                            ),
            ));
        
        $this->add(array(
            'name' => 'test_path',
            'attributes' => array(
                    'type'  => 'text',
                    'id'    => 'test_path'
                ),
            'options' => array(
                    'label' => '
                        Test Path',
                ),
            ));
    }
}
