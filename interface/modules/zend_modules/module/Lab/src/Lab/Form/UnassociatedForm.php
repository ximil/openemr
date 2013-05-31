<?php
namespace Lab\Form;

use Zend\Form\Form;

class UnassociatedForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('unassociated');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id[]',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        
        $this->add(array(
            'name' => 'comments[]',
            'attributes' => array(
                'type'  => 'textarea',
                'cols' => '42',
                'rows' => '1',
								'id'	=> 'comments',
								'class' => 'easyui-validatebox',
            ),
        ));
        
        $this->add(array( 
            'name' => 'check', 
            'type' => 'Zend\Form\Element\MultiCheckbox', 
            'options' => array( 
                'value_options' => array(
                    '1' => '', 
                ),
            ), 
        ));
        
        $this->add(array(
            'name' => 'comments_readonly',
            'attributes' => array(
                'type'  => 'textarea',
                'cols' => '52',
                'rows' => '1',
								'id'	=> 'comments_readonly',
								'class' => 'easyui-validatebox',
                'readonly' => true
            ),
        ));
        
    }
}
