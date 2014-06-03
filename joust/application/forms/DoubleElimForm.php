<?php

class DoubleElimForm extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('de');

	
	
	$areyousure = new Zend_Form_Element_Checkbox('areyousure');
	$areyousure->setLabel('Are you sure you want to move to the DE phase of the tournament? After this, no further changes to seeding can be made.')
	  ->setRequired('true')
	  ->addValidator('NotEmpty', true);;



        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Proceed to DE');

        $this->addElements(array($areyousure, $submit));

    }
}

