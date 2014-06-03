<?php


class AllianceForm extends Zend_Form
{

  public function __construct($options=null)
  {
    parent::__construct($options); 
     

    //Add Form Elements
    $this->addElement('text', 
		      'team1', 
		      array('label'      => 'Team 1',
			    'required'   => true,
			    'filters'    => array('StringTrim'),
			    ));
    
    $this->addElement('text', 
		      'team2', 
		      array('label'      => 'Team 2',
			    'required'   => true,
			    'filters'    => array('StringTrim'), 
			    ));
    

        $this->addElement('text', 
			  'game1', 
			  array('label'      => 'Game 1',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'game2', 
			  array('label'      => 'Game 2',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'game3', 
			  array('label'      => 'Game 3',
				'filters'    => array('StringTrim'),
				));


	

        // add the submit button
	$this->addElement('submit', 
			  'submit', 
			  array('label'    => 'Add Alliance',
				));



  }
}
