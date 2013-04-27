<?php
namespace Encounter\Form;

use Zend\Form\Form;

class EncounterForm extends Form
{
    public function __construct($name = null)
    {
		global $pid,$encounter;
        parent::__construct('encounter');
        $this->setAttribute('method', 'post');
        
        // Visit Category
        $this->add(array(
	        'name' => 'visitCategory',
		    'type'  => 'Zend\Form\Element\Select',
	        'attributes' => array(
				'class' => '/*easyui-combobox*/ combo',
	        	'style' => 'width:auto',
				'data-options' => 'required:true',
				'editable' => 'false',
				//'required' => 'required',
				'id' => 'visitCategory'
	            ),
		    'options' => array(
				'value_options' => array(
			    	'' => xlt('Select One'),
					),
		    ),
        ));
        
        // Provider
        $this->add(array(
        		'name' => 'provider',
        		'type'  => 'Zend\Form\Element\Select',
        		'attributes' => array(
        				'class' => '/*easyui-combobox*/ combo',
        				'style' => 'width:auto',
        				'data-options' => 'required:true',
        				'editable' => 'false',
        				//'required' => 'required',
        				'id' => 'provider'
        		),
        		'options' => array(
        				'value_options' => array(
        						'' => xlt('Unassigned'),
        				),
        		),
        ));
        
        // Facility
        $this->add(array(
        	'name' => 'facility',
        	'type'  => 'Zend\Form\Element\Select',
        	'attributes' => array(
        		'class' => '/*easyui-combobox*/ combo',
        		'style' => 'width:auto',
        		//'data-options' => 'required:true',
        		'editable' => 'false',
        		'required' => 'required',
        		'id' => 'facility'
        		),
        	'options' => array(
        		'value_options' => array(
        			'' => xlt('Select One'),
        		),
        	),
        ));
        
        // Billing Facility
        $this->add(array(
        	'name' => 'billingFacility',
        	'type'  => 'Zend\Form\Element\Select',
        	'attributes' => array(
        		'class' => '/*easyui-combobox*/ combo',
        		'style' => 'width:auto',
        		//'data-options' => 'required:true',
        		'editable' => 'false',
        		'required' => 'required',
        		'id' => 'billingFacility'
        		),
        	'options' => array(
        		'value_options' => array(
        			'' => xlt('Select One'),
        		),
        	),
        ));
        
        // Sensitivity
        $this->add(array(
        	'name' => 'sensitivity',
        	'type'  => 'Zend\Form\Element\Select',
        	'attributes' => array(
        		'class' => '/*easyui-combobox*/ combo',
        		'style' => 'width:auto',
        		//'data-options' => 'required:true',
        		'editable' => 'false',
        		'required' => 'required',
        		'id' => 'sensitivity'
        		),
        	'options' => array(
        		'value_options' => array(
        			'' => xlt('Select One'),
        		),
        	),
        ));
        
        // Date of Service
        $this->add(array(
        	'name' => 'dtService',
        	'type'  => 'Zend\Form\Element\Date',
        	'attributes' => array(
        		'type'  => 'text',
        		'class' => 'easyui-datebox',
        		'style' => 'width:100px',
        		'value' => date("Y-m-d"),
        		'id' => 'dtService'
        		),
        ));
        
        // Onset / hosp. Date
        $this->add(array(
        	'name' => 'dtOnset',
        	'type'  => 'Zend\Form\Element\Date',
        	'attributes' => array(
        			'type'  => 'text',
        			'class' => 'easyui-datebox',
        			'style' => 'width:100px',
        			'value' => date("Y-m-d"),
        			'id' => 'dtOnset'
        		),
        ));
        
        // Consultation Brief Description
        $this->add(array(
        		'type' => 'Zend\Form\Element\Textarea',
        		'name' => 'description',
        		'attributes' => array(
        				'class' => 'easyui-validatebox',
        				'style' => 'height:80px;width:190px',
        				'id' => 'description',
        		),
        ));
        
        // Issues
        $this->add(array(
        	'name' => 'issues[]',
        	'type'  => 'Zend\Form\Element\Select',
        	'attributes' => array(
        		'multiple' => true,
        		'title' => xl('Hold down [Ctrl] for multiple selections or to unselect'),
        		'size' => '8',
        		'class' => '/*easyui-combobox*/ combo',
        		'style' => 'height:85px; width:270px',
        		'editable' => 'false',
        		'id' => 'issues'
        		),
        ));
        
        // Provided Education Resource(s)
        $this->add(array(
        		'type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'provEduRes',
        		'attributes' => array(
        			'id' => 'provEduRes',
        		),
        		'options' => array(
        				'label' => '',
        				//'use_hidden_element' => true,
        				'checked_value' => '',
        				'unchecked_value' => ''
        		)
        ));
        
        // Provided Clinical Summary
        $this->add(array(
        		'type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'provCliSum',
        		'attributes' => array(
        			'id' => 'provCliSum',
        		),
        		'options' => array(
        			'label' => '',
        			'use_hidden_element' => true,
        			'checked_value' => '',
        			'unchecked_value' => ''
        		)
        ));
        
        // Transition/Transfer of Care
        $this->add(array(
        		'type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'transTrandCare',
        		'attributes' => array(
        				'id' => 'transTrandCare',
        		),
        		'options' => array(
        				'label' => '',
        				'use_hidden_element' => true,
        				'checked_value' => '',
        				'unchecked_value' => ''
        		)
        ));
        
        // Medication Reconciliation Performed
        $this->add(array(
        		'type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'medReconcPerf',
        		'attributes' => array(
        				'id' => 'medReconcPerf',
        		),
        		'options' => array(
        				'label' => '',
        				'use_hidden_element' => true,
        				//'checked_value' => '',
        				//'unchecked_value' => ''
        		)
        ));

    }
}

