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

class SetupForm extends Form
{
  public function __construct($name = null)
  {
    // we want to ignore the name passed
    parent::__construct('setup');
    $this->setAttribute('method', 'post');
    $this->setAttribute('enctype','multipart/form-data');
    $this->add(array(
        'name' => 'id',
        'attributes' => array(
            'type'  => 'hidden',
        ),
    ));

    $this->add(array(
      'name' => 'title',
      'type' => 'Zend\Form\Element\Select',
      'options' => array(
        'label' => '',
        'value_options' => array(
          ''  => '--Select--',
          '1' => 'Mr',
          '2' => 'Mrs',
        ),
      ),
    ));

    $this->add(array(
        'name' => 'fname',
        'attributes' => array(
            'type'  => 'text',
        ),
        'options' => array(
            'label' => '',
        ),
    ));
    
    $this->add(array(
        'type' => 'Zend\Form\Element\Textarea',
        'name' => 'comments',
        'attributes' => array(
            'class' => 'easyui-validatebox combo',
            'style' => 'height:80px',
            'id' => 'comments',
          ),
        'options' => array(
            'label' => '',
        ),
   ));

    $this->add(array(
        'name' => 'photo_upload',
        'attributes' => array(
            'type'  => 'file',
        ),
        'options' => array(
            'label' => '',
        ),
    )); 

    $this->add(array(
        'name' => 'submit',
        'attributes' => array(
            'type'  => 'submit',
            'value' => 'Submit',
            'id' => 'submitbutton',
        ),
    ));
  }
}
