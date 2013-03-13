<?php
namespace Lab\Form;

use Zend\Form\Form;

class PullForm extends Form
{
    public function __construct($name = null)
    {
	global $pid,$encounter;
        parent::__construct('pull');
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

	$this->add(array(
            'name' => 'pulltest',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Pull Test',
                'id' => 'pulltest',
		'onclick' => 'pulldata("lab_id",1)',
            ),
        ));
	
	$this->add(array(
            'name' => 'pullaoe',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Pull AOE',
                'id' => 'pullAOE',
		'onclick' => 'pulldata(lab_id,2)',
            ),
        ));
	
	$this->add(array(
            'name' => 'pulltestaoe',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Pull Test and AOE',
                'id' => 'pulltestaoe',
		'onclick' => 'pulldata(lab_id,3)',
            ),
        ));
	
	
	
    }
}
