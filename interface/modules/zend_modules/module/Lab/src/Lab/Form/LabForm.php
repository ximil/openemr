<?php
namespace Lab\Form;

use Zend\Form\Form;

class LabForm extends Form
{
    public function __construct($name = null)
    {
	global $pid,$encounter;
        parent::__construct('lab');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
	$this->add(array(
            'name' => 'patient_id',
            'attributes' => array(
                'type'  => 'hidden',
		'value' => $pid,
            ),
        ));
	$this->add(array(
            'name' => 'encounter_id',
            'attributes' => array(
                'type'  => 'hidden',
		'value' => $encounter,
            ),
        ));
	$this->add(array(
            'name' => 'procedurecount',
            'attributes' => array(
                'type'  => 'hidden',
		'id'	=> 'procedurecount',
		'value'	=> 2,
            ),
        ));
	$this->add(array(
            'name' => 'provider',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'editable' => 'false',
		'required' => 'required'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'lab_id',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'id' => 'lab_id',
		'required' => 'required',
		'onchange' => '/*checkLab(this.value)*/',
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
//	$this->add(array(
//            'name' => 'location',
//	    'type'  => 'Zend\Form\Element\Select',
//            'attributes' => array(
//		'class' => '/*easyui-combobox*/ combo',
//		'data-options' => 'required:true',
//		'required' => 'required'
//            ),
//	    'options' => array(
//		'value_options' => array(
//		    '' => xlt('Unassigned'),
//		),
//	    ),
//        ));
	$this->add(array(
            'name' => 'orderdate',
	    'type'  => 'Zend\Form\Element\Date',
	    'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-datebox',
		'data-options' => 'required:true',
		'value' => date("Y-m-d"),
		'required' => 'required'
            ),
        ));
	$this->add(array(
            'name' => 'timecollected',
            'attributes' => array(
                'type'  => 'text',
		'id' => 'timecollected',
		'class' => 'easyui-datetimebox',
		'data-options' => 'required:true',
		'value' => date("Y-m-d H:i:s"),
		'required' => 'required'
            ),
        ));
	$this->add(array(
            'name' => 'priority',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'required' => 'required'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'status',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'required' => 'required',
            ),
        ));
	$this->add(array(
            'name' => 'billto',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'required' => 'required',
            ),
	    'options' => array(
		'value_options' => array(
		    array(
			  'value' => "P",
			  'label' => xlt('Patient'),
			  'selected' => TRUE
			 ),
		    array(
			  'value' => "T",
			  'label' => xlt('Third Party')
			 ),
		    array(
			  'value' => "C",
			  'label' => xlt('Facility')
			 ),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'diagnoses[]',
            'attributes' => array(
                'type'  => 'text',
		'id'	=> 'diagnoses_1',
		'class' => 'easyui-validatebox combo',
		'required' => 'required',
		'placeholder' => 'Seperated by semicolon(;)'
            ),
        ));
	$this->add(array(
	    'type' => 'Zend\Form\Element\Textarea',
            'name' => 'patient_instructions',
	    'attributes' => array(
                'class' => 'easyui-validatebox combo',
		'style' => 'height:60px',
		'id' => 'patient_instructions',
            ),
        ));
	$this->add(array(
	    'type' => 'Zend\Form\Element\Textarea',
            'name' => 'internal_comments',
	    'attributes' => array(
                'class' => 'easyui-validatebox combo',
		'style' => 'height:60px',
		'id' => 'internal_comments',
            ),
        ));
	$this->add(array(
	    'name' => 'specimencollected',
	    'type' => 'Zend\Form\Element\Radio',
	    'attributes' => array(
		'required' => 'required'
            ),
	    'options' => array(
		'value_options' => array(
		    'onsite' => xlt('On Site'),
		    'labsite' => xlt('Lab Site'),
		),
	    ),
	));
        $this->add(array(
            'name' => 'procedures[]',
            'attributes' => array(
		'id' => 'procedures_1',
                'type'  => 'text',
		'class' => 'easyui-validatebox combo',
		'onKeyup' => 'getProcedures(this.value, this.id ,"lab_id")',
		'required' => 'required'
            ),
        ));
	$this->add(array(
            'name' => 'procedure_code[]',
            'attributes' => array(
                'type' => 'hidden',
		'id' => 'procedure_code_1'
            ),
        ));
	$this->add(array(
            'name' => 'procedure_suffix[]',
            'attributes' => array(
                'type' => 'hidden',
		'id' => 'procedure_suffix_1'
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
	$this->add(array(
            'name' => 'addprocedure',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Add Procedure',
                'id' => 'addprocedure',
		'onclick' => 'cloneRow()',
            ),
        ));
    }
}
