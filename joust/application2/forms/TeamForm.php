<?php


class TeamForm extends Zend_Form
{

  public function __construct($options=null)
  {
    parent::__construct($options); 
     

    //Add Form Elements
    $this->addElement('text', 
		      'code', 
		      array('label'      => 'Team Code (00-0000):',
			    'required'   => true,
			    'filters'    => array('StringTrim'),
			    ));
    
    $this->addElement('text', 
		      'org', 
		      array('label'      => 'Organization:',
			    'required'   => true,
			    'filters'    => array('StringTrim'), 
			    ));
    
    
    // add an email element
    $this->addElement('text', 
		      'email', 
		      array('label'      => 'Your email address:',
			    'filters'    => array('StringTrim'),
			    'validators' => array('EmailAddress'),
			    ));

        $this->addElement('text', 
			  'p1doc', 
			  array('label'      => 'P1 Doc Score',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'p2doc', 
			  array('label'      => 'P2 Doc Score',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'p3doc', 
			  array('label'      => 'P3 Doc Score',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'p4doc', 
			  array('label'      => 'Onsite Presentation Score',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'seed1', 
			  array('label'      => 'Seed 1',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'seed2', 
			  array('label'      => 'Seed 2',
				'filters'    => array('StringTrim'),
				));

        $this->addElement('text', 
			  'seed3', 
			  array('label'      => 'Seed 3',
				'filters'    => array('StringTrim'),
				));
	

        // add the submit button
	$this->addElement('submit', 
			  'submit', 
			  array('label'    => 'Add Team',
				));
  }
}
