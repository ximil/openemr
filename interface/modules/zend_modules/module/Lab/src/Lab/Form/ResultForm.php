<?php
namespace Lab\Form;

use Zend\Form\Form;

class ResultForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('resultentry');
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
								'id' => 'patient_id',
            ),
        ));
				$this->add(array(
            'name' => 'procedure_report_id[]',
            'attributes' => array(
				        'type'  => 'hidden',
								'id' => 'procedure_report_id',
            ),
        ));
				$this->add(array(
            'name' => 'abnormal[]',
						'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
								'class' => 'combo smalltb',
								'editable' => 'true',
            ),
						'options' => array(
								'value_options' => array(
								    '' => xlt('Unassigned'),
								),
						),
        ));
				$this->add(array(
            'name' => 'result[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'result',
								'class' => 'combo smalltb',
            ),
        ));
				$this->add(array(
            'name' => 'units[]',
						'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
								'class' => 'combo smalltb',
								'editable' => 'true',
            ),
						'options' => array(
								'value_options' => array(
								    '' => xlt('Unassigned'),
								),
						),
        ));
				$this->add(array(
            'name' => 'result_status[]',
						'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
								'class' => 'combo mediumtb',
								'editable' => 'true',
            ),
						'options' => array(
								'value_options' => array(
								    '' => xlt('Unassigned'),
								),
						),
        ));
				$this->add(array(
            'name' => 'facility[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'facility',
								'class' => 'combo mediumtb',
            ),
        ));
				$this->add(array(
            'name' => 'comments[]',
            'attributes' => array(
                'type'  => 'textarea',
								'id'	=> 'comments',
								'class' => 'combo mediumtb',
								'style' => 'height:100px;width:260px !important;' ,
								
            ),
        ));
				$this->add(array(
            'name' => 'notes[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'notes',
								'class' => 'combo mediumtb',
            ),
        ));
				$this->add(array(
            'name' => 'range[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'range',
								'class' => 'combo smalltb',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));
				$this->add(array(
            'name' => 'search_from_date',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'search_from_date',
								'class' => 'easyui-datebox',
            ),
        ));
				$this->add(array(
            'name' => 'search_to_date',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'search_to_date',
								'class' => 'easyui-datebox',
            ),
        ));
				$this->add(array(
            'name' => 'search_patient',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'search_patient',
								'class' => 'combo',
								'onKeyup' => 'getPatient(this.value, this.id,"../../specimen/searchPatient")',
								'autocomplete' => 'off'
            ),
        ));
    }
}
