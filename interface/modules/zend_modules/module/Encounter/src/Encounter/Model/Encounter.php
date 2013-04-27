<?php
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