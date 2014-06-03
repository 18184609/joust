<?php


class AwardForm extends Zend_Form
{

  public function __construct($options=null)
  {
    parent::__construct($options); 
     

    //Add Form Elements
    $this->addElement('select', 
		      'name', 
		      array('label'      => 'Award Name:',
			    'required'   => true,
			    'multiOptions' => array('T' => 'KISS Award',
						    'C' => 'Spirit of Botball',),
			    )
		      );

    $this->addElement('select',
		      'type',
		      array(
			    'label' => 'Award Type',
			    'required' => true,
			    'multiOptions' => array('T' => 'Trophy',
						    'C' => 'Certificate',), 
			    )
		      );
    


    $this->addElement('text', 
		      'team', 
		      array('label'      => 'Team:',
			    'required'   => true,
			    'filters'    => array('StringTrim'), 
			    ));


    $this->addElement('textarea', 
		      'notes', 
		      array('label'      => 'Notes:',
			    'filters'    => array('StringTrim'), 
			    ));
    
    

	

        // add the submit button
	$this->addElement('submit', 
			  'submit', 
			  array('label'    => 'Add Award',
				));
  }
}
