<?php
namespace Lab\Form;

use Zend\Form\Form;

class LabForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('lab');
        $this->setAttribute('method', 'post');
    }
}
