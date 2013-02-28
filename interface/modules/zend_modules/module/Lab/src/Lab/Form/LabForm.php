<?php
namespace Lab\Form;

use Zend\Form\Form;

class LabForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('lab');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
	$this->add(array(
            'name' => 'provider',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true',
		'editable' => 'false',
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
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true',
		//'onChange' => 'getLocation(this.value)',
		'onChange' => 'getTestList(this.value)',
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
		'id' => 'lab_id',
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true',
		//'onChange' => 'getLocation(this.value)',
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'location',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true',
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'orderdate',
            'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-datebox',
		'data-options' => 'required:true',
		'value' => date("Y-m-d"),
            ),
        ));
	$this->add(array(
            'name' => 'timecollected',
            'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-datetimebox',
		'data-options' => 'required:true',
		'value' => date("Y-m-d H:i:s"),
            ),
        ));
	$this->add(array(
            'name' => 'priority',
	    'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true'
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
		'class' => 'easyui-combobox combo',
		'data-options' => 'required:true'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'diagnoses',
            'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-validatebox combo',
		'data-options' => 'required:true'
            ),
        ));
	$this->add(array(
	    'type' => 'Zend\Form\Element\Textarea',
            'name' => 'patient_instructions',
	    'attributes' => array(
                'class' => 'easyui-validatebox combo',
		'style' => 'height:60px',
            ),
        ));
        $this->add(array(
            'name' => 'procedures',
            'attributes' => array(
		'id' => 'procedures',
                'type'  => 'text',
		'class' => 'easyui-validatebox combo',
		'data-options' => 'required:true',
		//'onKeyup' => 'getProcedures(this.value, this.id ,"labid")'
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
    }
}
