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
            'name' => 'id[0][]',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
	$this->add(array(
            'name' => 'hiddensubmit',
            'attributes' => array(
                'type'  => 'submit',
		'id'	=> 'hiddensubmit',
		'style' => 'display:none'
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
            'name' => 'procedurecount[0][]',
            'attributes' => array(
                'type'  => 'hidden',
		'id'	=> 'procedurecount_1_1',
		'value'	=> 2,
            ),
        ));
	$this->add(array(
            'name' => 'provider[0][]',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'editable' => 'false',
		'required' => 'required',
		'id' => 'provider_1_1'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'lab_id[0][]',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'data-options' => 'required:true',
		'id' => 'lab_id_1_1',
		'required' => 'required',
		'onchange' => 'checkLab(this.value, this.id)',
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
            'name' => 'orderdate[0][]',
	    'type'  => 'Zend\Form\Element\Date',
	    'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-datebox',
		'style' => 'width:100px',
		'data-options' => 'required:true',
		'value' => date("Y-m-d"),
		'required' => 'required',
		'id' => 'orderdate_1_1'
            ),
        ));
	$this->add(array(
            'name' => 'timecollected[0][]',
            'attributes' => array(
                'type'  => 'text',
		'id' => 'timecollected_1_1',
		'class' => 'easyui-datetimebox',
		'data-options' => 'required:true',
		'value' => date("Y-m-d H:i:s"),
		'required' => 'required'
            ),
        ));
	$this->add(array(
            'name' => 'priority[0][]',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'style' => 'width:90px',
		'data-options' => 'required:true',
		'required' => 'required',
		'id' => 'priority_1_1'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'status[0][]',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'style' => 'width:90px',
		'data-options' => 'required:true',
		'required' => 'required',
		'id' => 'status_1_1'
            ),
        ));
	$this->add(array(
            'name' => 'billto[0][]',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => '/*easyui-combobox*/ combo',
		'style' => 'width:90px',
		'data-options' => 'required:true',
		'required' => 'required',
		'id' => 'billto_1_1'
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
            'name' => 'diagnoses[0][]',
            'attributes' => array(
                'type'  => 'text',
		'id'	=> 'diagnoses_1_1',
		'autocomplete' 	=> 'off',
		'onKeyup' => 'getDiagnoses(this.value, this.id)',
		'onfocus' => 'readDiagnoses(this.value, this.id)',
		'class' => 'easyui-validatebox combo',
		'required' => 'required',
		'placeholder' => 'Seperated by semicolon(;)'
            ),
        ));
	$this->add(array(
	    'type' => 'Zend\Form\Element\Textarea',
            'name' => 'patient_instructions[0][]',
	    'attributes' => array(
                'class' => 'easyui-validatebox combo',
		'style' => 'height:20px; width:420px',
		'id' => 'patient_instructions_1_1',
            ),
        ));
	$this->add(array(
	    'type' => 'Zend\Form\Element\Textarea',
            'name' => 'internal_comments[0][]',
	    'attributes' => array(
                'class' => 'easyui-validatebox combo',
		'style' => 'height:20px',
		'id' => 'internal_comments_1_1',
            ),
        ));
	$this->add(array(
	    'name' => 'specimencollected[0][]',
	    'type' => 'Zend\Form\Element\Radio',
	    'attributes' => array(
		'required' => 'required',
		'id' => 'specimencollected_1_1',
            ),
	    'options' => array(
		'value_options' => array(
		    'onsite' => xlt('On Site'),
		    'labsite' => xlt('Lab Site'),
		),
	    ),
	));
        $this->add(array(
            'name' => 'procedures[0][]',
            'attributes' => array(
		'id' => 'procedures_1_1',
		'autocomplete' 	=> 'off',
                'type'  => 'text',
		'class' => 'easyui-validatebox combo',
		'onKeyup' => 'getProcedures(this.value, this.id)',
		'required' => 'required'
            ),
        ));
	$this->add(array(
            'name' => 'procedure_code[0][]',
            'attributes' => array(
                'type' => 'hidden',
		'id' => 'procedure_code_1_1'
            ),
        ));
	$this->add(array(
            'name' => 'procedure_suffix[0][]',
            'attributes' => array(
                'type' => 'hidden',
		'id' => 'procedure_suffix_1_1'
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
