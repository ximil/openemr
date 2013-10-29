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
				
				/*
				 //SAMPLE CONFIGURATION
				 $hooks	=  array(
												'0' => array(
														'name' 	=> "HookName1",
														'title' 	=> "HookTitle1",
														'path' 	=> "path/to/Hook1",
												),
												'1' => array(
														'name' 	=> "HookName2",
														'title' 	=> "HookTitle2",
														'path' 	=> "path/to/Hook2",
												),									
										);*/
				
				$hooks	=  array();	
	
				return $hooks;
    }
    public function getAclConfig()
    {
				/*
				 //SAMPLE CONFIGURATION
				 $acl = array(
						array(
							'section_id' 				=> 'SectionID1',
							'section_name' 			=> 'SectionDisplayName1',
							'parent_section' 		=> 'ParentSectionID1',
							),
						array(
							'section_id' 				=> 'SectionID2',
							'section_name' 			=> 'SectionDisplayName2',
							'parent_section' 		=> 'ParentSectionID2',
							),
						);
				*/
				$acl = array();
				return $acl;
		}
    
    public function configSettings()
    {
        /*
				 //SAMPLE CONFIGURATION
				 $settings = array(
            array(
                'display'   => 'Display1',
                'field'     => 'Filed1',
                'type'      => 'FieldType1',
            ),
            array(
                'display'   => 'Display2',
                'field'     => 'Filed2',
                'type'      => 'FieldType2',
            ),
        );*/
				$settings = array();
        return $settings;
    }
		
		public function getDependedModulesConfig()
    {
				//SPECIFY LIST OF DEPENDED MODULES OF A MODULE
				//$dependedModules	=  array('Encounter','Lab',);						
				return $dependedModules;
    }		
}