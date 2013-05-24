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
// Author:   Remesh Babu  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Encounter\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Encounter implements InputFilterAwareInterface
{
	public $visitCategory;
	public $facility;
	public $billingFacility;
	public $sensitivity;
	public $description;
	protected $inputFilter;

    public function exchangeArray($data)
    {
		//$this->id 			= (isset($data['id'])) 				? $data['id'] 				: null;
		//$this->pid 				= (isset($data['patient_id'])) 		? $data['patient_id'] 		: null;
		$this->visitCategory 	= (isset($data['visitCategory'])) 	?  $data['visitCategory'] 	: null;
		$this->facility 		= (isset($data['facility'])) 		?  $data['facility'] 		: null;
		$this->billingFacility 	= (isset($data['billingFacility'])) ?  $data['billingFacility'] : null;
		$this->sensitivity 		= (isset($data['sensitivity'])) 	?  $data['sensitivity'] 	: null;
		$this->description 		= (isset($data['description'])) 	?  $data['description'] 	: null;
		$this->issue[] 			= (isset($data['issue[]'])) 		?  $data['issue[]'] 		: null;
		$this->provEduRes 		= (isset($data['provEduRes'])) 		?  $data['provEduRes'] 		: null;
		$this->provCliSum 		= (isset($data['provCliSum'])) 		?  $data['provCliSum'] 		: null;
		$this->transTrandCare 	= (isset($data['transTrandCare'])) 	?  $data['transTrandCare'] 	: null;
		$this->medReconcPerf 	= (isset($data['medReconcPerf'])) 	?  $data['medReconcPerf'] 	: null;
	
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
			
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'visitCategory',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
           /* $inputFilter->add($factory->createInput(array(
            		'name'     => 'facility',
            		//'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'billingFacility',
            		//'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'sensitivity',
            		//'required' => true,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 0,
            								'max'      => 30,
            						),
            				),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'description',
            		//'required' => true,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            				//array('name' => 'LONGTEXT'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 0,
            								'max'      => 30,
            						),
            				),
            		),

            )));*/


            $this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
    }
}