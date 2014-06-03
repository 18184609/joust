<?php


class AwardForm extends Zend_Form
{

  public function __construct($options=null)
  {
    parent::__construct($options); 
     

    //Add Form Elements


    $this->addElement('text', 
		      'id', 
		      array('label'      => 'Award ID',
			    'readonly' =>true
			    ));


    $this->addElement('select', 
		      'name', 
		      array(
			    'label'      => 'Award Name',
			    'required'   => true,
			    'multiOptions' => array(
						    'Overall Judges Choice' => 'Overall Judges Choice',
			                'Spirit of Botball' => 'Spirit of Botball',
			                'KISS Award' => 'KISS Award',
		                    'Outstanding Onsite Presentation' => 'Outstanding Onsite Presentation',
		                    'Outstanding Engineering' => 'Outstanding Engineering',
		                    'Outstanding Programming' => 'Outstanding Programming',
						    'Outstanding Overall Design' => 'Outstanding Overall Design',
						    'Outstanding Sub-System' => 'Outstanding Sub-System',
						    'Outstanding Use of Sensors' => 'Outstanding Use of Sensors',
						    'Robot Collaboration / Synchronization' => 'Robot Collaboration / Synchronization',
						    'Outstanding Team Spirit' => 'Outstanding Team Spirit',
						    'Outstanding Outreach' => 'Outstanding Outreach',
						    'Outstanding Rookie Team' => 'Outstanding Rookie Team',
						    'ACE Award' => 'ACE Award',
						    'Most Effective Strategy' => 'Most Effective Strategy',
						    'Best Defense' => 'Best Defense',
						    'Most Photogenic Robot' => 'Most Photogenic Robot',
						    'Alliance First Place' => 'Alliance First Place',
						    'Alliance Second Place' => 'Alliance Second Place',
						    'Outstanding Documentation' => 'Outstanding Documentation')
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
		      array('label'      => 'Team Code / Name',
			    'required'   => true,
			    'filters'    => array('StringTrim'), 
			    ));


    $this->addElement('textarea', 
		      'notes', 
		      array('label'      => 'Notes',
			    'filters'    => array('StringTrim'), 
			    ));
    
    

	

        // add the submit button
	$this->addElement('submit', 
			  'submit', 
			  array('label'    => 'Add Award',
				));
  }
}
