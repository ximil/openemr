<?php
namespace Lab\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
//use Zend\Validator\InArray;

class Lab implements InputFilterAwareInterface
{
    public $id;
    public $patienName;
    public $PatientInstructions;
	public $pid;
    public $labName;
    public $locationName;
  	public $testGroupName;
  	public $BillTo;
  	public $OrderingProvider;
  	public $Priority;
  	public $Status;
  	
  	protected $inputFilter;

    public function exchangeArray($data)
    {
	$this->id	     			= (isset($data['id']))   	  			? $data['id']  						: null;
	$this->pid	    	 		= (isset($data['pid']))     			? $data['pid']  					: null;
	$this->patienName			= (isset($data['patientName']))  		? $data['patientName']  			: null;
	$this->labName   			= (isset($data['labName']))   			? $data['labName'] 					: null;
	$this->locationName  		= (isset($data['locationName']))  		? $data['locationName']  			: null;
	$this->testGroupName 		= (isset($data['testGroupName']))  		? $data['testGroupName']			: null;
	$this->BillTo  				= (isset($data['BillTo']))  			? $data['BillTo']  					: null;
	$this->OrderingProvider		= (isset($data['OrderingProvider'])) 	? $data['OrderingProvider']  		: null;
	$this->Priority  			= (isset($data['Priority']))  			? $data['Priority']  				: null;
	$this->Status  				= (isset($data['Status']))  			? $data['Status']  					: null;
	$this->PatientInstructions  = (isset($data['PatientInstructions'])) ? $data['PatientInstructions']  	: null;
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
        }

        return $this->inputFilter;
    }	
}
