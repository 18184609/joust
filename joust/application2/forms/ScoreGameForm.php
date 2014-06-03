<?php


class ScoreGameForm extends Zend_Form
{

  public $sidea = null;
  public $sideb = null;

  public function __construct($gid = null, $options=null)
  {
    parent::__construct($options); 

    // Get the teams in this game
    $this->__getTeams($gid);

    //Add Form Elements
    $this->addElement('hidden', 
		      'game', array(  'required'   => true, 'filters'    => array('StringTrim'),));


    $scoredgame = new Zend_Form_Element_Hidden('game');
    $scoredgame->setValue($gid)
      ->setRequired(true) ->addValidator('NotEmpty', true);

    $victor = new Zend_Form_Element_Select('victor');
    $victor->setLabel('Victor')
      ->setMultiOptions(array("unresolved"=>"unresolved",
			      $this->sidea=>$this->sidea,
			      $this->sideb=>$this->sideb))
          ->setRequired(true)->addValidator('NotEmpty', true);
    
    $this->addElements(array($scoredgame, $victor));    


    // add the submit button
    $this->addElement('submit', 'submit', array('label'    => 'Score Game',));

  }

  public function __getTeams($gid)
  {
    $games=new Games();
    $game = $games->fetchRow( $games->select()->where( 'id=? ', $gid) );
    
    $this->sidea = $game['sidea'];
    $this->sideb = $game['sideb'];

  }




}
