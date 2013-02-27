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
            'attributes' => array(
                'type'  => 'Zend\Form\Element\Select',
		'class' => 'easyui-combobox',
		'data-options' => 'required:true'
            ),
	    'options' => array(
		'value_options' => array(
		    '' => xlt('Unassigned'),
		),
	    ),
        ));
	$this->add(array(
            'name' => 'lab_id',
            'attributes' => array(
                'type'  => 'Zend\Form\Element\Select',
		'class' => 'easyui-combobox',
		'data-options' => 'required:true'
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
		'data-options' => 'required:true'
            ),
        ));
	$this->add(array(
            'name' => 'timecollected',
            'attributes' => array(
                'type'  => 'text',
		'class' => 'easyui-datetimebox',
		'data-options' => 'required:true'
            ),
        ));
	$this->add(array(
            'name' => 'priority',
            'attributes' => array(
                'type'  => 'Zend\Form\Element\Select',
		'class' => 'easyui-combobox',
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
            'attributes' => array(
                'type'  => 'Zend\Form\Element\Select',
		'class' => 'easyui-combobox',
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
		'class' => 'easyui-validatebox',
		'data-options' => 'required:true'
            ),
        ));
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Title',
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
