<?php
namespace Lab\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Pull implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function exchangeArray($data)
    {
	$fh = fopen("d:/POST.txt","w");
	fwrite($fh,print_r($data,1));
	$this->id	     		= (isset($data['id']))   	  	? $data['id']  			: null;
	$this->pid	    	 	= (isset($data['patient_id']))     	? $data['patient_id']  		: null;
	$this->encounter    	 	= (isset($data['encounter_id']))     	? $data['encounter_id']  	: null;
	$this->lab_id   		= (isset($data['lab_id']))   		? $data['lab_id'] 		: null;
	
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
                'name'     => 'lab_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            

            $this->inputFilter = $inputFilter;
}
return $this->inputFilter;
    }
}