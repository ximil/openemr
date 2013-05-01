<?php
namespace Appointments\Form;

use Zend\Form\Form;

class AppointmentsForm extends Form
{
    public function __construct($name = null)
    {
	global $pid,$encounter;
        parent::__construct('encounter');
        $this->setAttribute('method', 'post');
    }
}

