<?php

class RosterUploadForm extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('upload');
        $this->setAttrib('enctype', 'multipart/form-data');

	
	
	$areyousure = new Zend_Form_Element_Checkbox('areyousure');
	$areyousure->setLabel('Are you sure you want to delete the exting tournament data and start over')
	  ->setRequired('true')
	  ->addValidator('NotEmpty', true);;

        $file = new Zend_Form_Element_File('file');
      	$file->setLabel('BOPD Scores File (csv)')
            ->setDestination(APPLICATION_PATH.'/../data/uploads')
            ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Upload');

        $this->addElements(array($areyousure, $file, $submit));

    }
}

