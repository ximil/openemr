<?php
namespace Lab\Form;

use Zend\Form\Form;

class SpecimenForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('specimen');
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
            'name' => 'procedure_order_id[]',
            'attributes' => array(
				        'type'  => 'hidden',
								'id' => 'procedure_order_id',
            ),
        ));
				$this->add(array(
            'name' => 'procedure_order_seq[]',
            'attributes' => array(
				        'type'  => 'hidden',
								'id' => 'procedure_order_seq',
            ),
        ));
				$this->add(array(
            'name' => 'specimen[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'specimen',
								'class' => 'easyui-validatebox combo',
            ),
        ));
				$this->add(array(
            'name' => 'specimen_collected_time[]',
            'attributes' => array(
                'type'  => 'text',
								'id'	=> 'specimen_collected_time',
								'class' => 'easyui-datetimebox',
            ),
        ));
				$this->add(array(
            'name' => 'specimen_search_status',
						'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
								'class' => 'combo',
								'editable' => 'true',
            ),
        ));
				$this->add(array(
            'name' => 'specimen_status[]',
						'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
								'class' => 'combo',
								'editable' => 'true',
            ),
						'options' => array(
								'value_options' => array(
								    '' => xlt('Unassigned'),
								),
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
								'onKeyup' => 'getPatient(this.value, this.id,"./searchPatient")',
								'autocomplete' => 'off'
            ),
        ));
    }
}
