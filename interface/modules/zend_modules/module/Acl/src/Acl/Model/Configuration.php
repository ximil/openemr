<?php
namespace Acl\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Form;

class Configuration extends Form implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    public function __construct()
    {
	parent::__construct('configuration');
        $this->setAttribute('method', 'post');
        
	
	$this->add(array(
            'name' => 'hie_path',
            'attributes' => array(
                    'type'  => 'text',
                    'id'    => 'hie_path'
                ),
            'options' => array(
                    'label' => '
                        HIE Path',
                ),
        ));
        
        $this->add(array(
            'name' => 'test',
	    'type'  => 'Zend\Form\Element\Select',
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

    public function exchangeArray($data)
    {
	
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


            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    
    public function getHookConfig()
    {
				//SHOULD SPECIFY THE CONTROLLER AND ITS ACTION IN THE PATH, INCLUDING INDEX ACTION
				$hooks	=  array(
												'0' => array(
														'name' 	=> "procedure_order",
														'title' 	=> "Procedure Order",
														'path' 	=> "public/lab/index",
												),
												'1' => array(
														'name' 	=> "procedure_result",
														'title' 	=> "Lab Result",
														'path' 	=> "public/lab/result/index",
												),									
										);	
	
				return $hooks;
    }
    public function getAclConfig()
    {
				$acl = array(
						);
				return $acl;
		}
    
    public function configSettings()
    {
        $settings = array(
            array(
                'display'   => 'HIE Path',
                'field'     => 'hie_path',
                'type'      => 'text',
            ),
            array(
                'display'   => 'Test',
                'field' => 'user',
                'type'  => 'select',
            ),
        );
        return $settings;
    }
		
		public function getDependedModulesConfig()
    {
				//SPECIFY LIST OF DEPENDED MODULES OF A MODULE
				//$dependedModules	=  array('Encounter','Lab',);						
				return $dependedModules;
    }		
}