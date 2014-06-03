<?php


class GameForm extends Zend_Form
{

  public function __construct($options=null)
  {
    parent::__construct($options); 
     

    //Add Form Elements
    $this->addElement('text', 'id', array('label'      => 'Game id:', 'required'   => true, 'filters'    => array('StringTrim'),));

    $this->addElement('text','bracket', array('label' =>'round   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','round', array('label' =>'round   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','consolation', array('label' =>'consolation', 'filters' =>array('StringTrim'),));
    $this->addElement('text','sidea', array('label' =>'sidea   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','sideb', array('label' =>'sideb   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','wgame', array('label' =>'wgame   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','wside', array('label' =>'wside   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','lgame', array('label' =>'lgame   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','lside', array('label' =>'lside   ', 'filters' =>array('StringTrim'),));
    $this->addElement('text','victor', array('label' =>'victor  ', 'filters' =>array('StringTrim'),));
    // add the submit button
    $this->addElement('submit', 'submit', array('label'    => 'Add Team',));
  }
}
