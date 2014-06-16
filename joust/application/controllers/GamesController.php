<?php

class GamesController extends Zend_Controller_Action 
{
  
  function indexAction()
  {
    // automatically score playable byes at this point
    $this->_scoreByes();

    $games = new Games();
    $games->select()->setIntegrityCheck(false);
    

    // YET MORE PAIN FROM SQLITE
    // because of some screwy thing that sqlite does with column names, 
    // I can't get ->joinLeft to work with  Zend_DB_Table without doing a bunch of manual column aliasing
    // see below for the horrible, horrible details. 
    // Killing me, but I guess these should all be methods of the Games DAO , anyway. 


    $this->view->unplayed =$games->fetchAll($games->select()->setIntegrityCheck(false)
					    ->from(array('g' =>'games'))
					    ->where("( victor is NULL OR victor ='')" )
					    ->joinLeft( array('sidea'=>'teams'),  // join table
							'g.sidea=sidea.code', // join condition
							array('sideacode'=>'sidea.code', 'sideaorg'=>'sidea.org') ) // columns to select, aliased
					    ->joinLeft( array('sideb'=>'teams'), 
							'g.sideb=sideb.code', 
							array('sidebcode'=>'sideb.code', 'sideborg'=>'sideb.org') )
					    ->order('id'));




    // played games have a valid victor 
    $this->view->played =$games->fetchAll($games->select()->setIntegrityCheck(false)
					  ->from(array('g' =>'games'))
					  ->where("victor  is not null")
					  ->where("victor !=''" )
					  ->joinLeft( array('sidea'=>'teams'),  // join table
						      'g.sidea=sidea.code', // join condition
						      array('sideacode'=>'sidea.code', 'sideaorg'=>'sidea.org') ) // columns to select, aliased
					  ->joinLeft( array('sideb'=>'teams'), 
						      'g.sideb=sideb.code', 
						      array('sidebcode'=>'sideb.code', 'sideborg'=>'sideb.org') )
					  ->order('id DESC'));

  }


  function publicAction()
  {
    $games = new Games();
    
    // unplayed games have no victor, but do have a valid  team  for both sidea and sideb
    $this->view->unplayed =$games->fetchAll($games->select()->where("( victor is NULL OR victor ='') AND  sidea  in (select code from teams UNION select 'bye' as code) AND  sideb in (select code from teams UNION select 'bye' as code)") ->order('id'));

    //pending teams don't have a valid team on one or more sides
    $this->view->pending =$games->fetchAll($games->select()->where("(sidea != 'bye' AND sidea not in (select code from teams)) or (sideb  !='bye' AND sideb not in (select code from teams) ) or sidea is null or sideb is null " ) ->order('id'));

    // played games have a valid victor 
    $this->view->played =$games->fetchAll($games->select()->where("victor  is not null") ->order('id'));

  }


  function autoAction() { 
    $this->_scoreByes();   
    $this->_scorePlayable();
  }



  function addAction()
  {
    //TODO: can this be refactored in to editAction?

    $form = new GameForm();
    $form->submit->setLabel('Edit');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	$games= new Games();
	$id = $form->getValue('id');
	  
	$row = $games->fetchRow("id='".$id."'");

	$row->org      = $form->getValue('org');
	$row->email    = $form->getValue('email');
	$row->id       = $form->getValue('id');
	$row->bracket  = $form->getValue('bracket');
	$row->round    = $form->getValue('round');
	$row->consolation = $form->getValue('consolation');
	$row->sidea    = $form->getValue('sidea');
	$row->sideb    = $form->getValue('sideb');
	$row->wgame    = $form->getValue('wgame');
	$row->wside    = $form->getValue('wside');
	$row->lgame    = $form->getValue('lgame');
	$row->lside    = $form->getValue('lside');
	$row->victor   = $form->getValue('victor');

	$row->save();
	  
	$this->_redirect('games');
      } else {
	$form->populate($formData);
      }
    }
  }



  function editAction()
  {
    $form = new GameForm();
    $form->submit->setLabel('Edit');
    $this->view->form =$form;
    
    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	$games= new Games();
	$id = $form->getValue('id');
	
	$row = $games->fetchRow("id='".$id."'");

	
	$row->id       = $form->getValue('id');
	$row->bracket  = $form->getValue('bracket');
	$row->round    = $form->getValue('round');
	$row->consolation = $form->getValue('consolation');
	$row->sidea    = $form->getValue('sidea');
	$row->sideb    = $form->getValue('sideb');
	$row->wgame    = $form->getValue('wgame');
	$row->wside     = $form->getValue('wside');
	$row->lgame      = $form->getValue('lgame');
	$row->lside       = $form->getValue('lside');
	$row->victor    = $form->getValue('victor');

	$row->save();
	  
	$this->_redirect('games');
      } else {
	$form->populate($formData);
      }
    } else {
      $id= $this->_request->getParam('id',0);
      $games = new Games();
      $game = $games->fetchRow("id='".$id."'");

      
      $form->populate($game->toArray() );

    }
  }

  function scoreAction()
  {
    //TODO: add in some error handling

    if($this->_request->isPost()) {

      // if we got a game id, check to see if it's a valid game id. 
      
      $gid= $this->_request->getParam('game',0);
      $games = new Games();
      $game = $games->fetchRow($games->select()->where("id=?", $gid) );
      
      $wgame = $games->fetchRow($games->select()->where("id=?", $game['wgame']) );
      $lgame = $games->fetchRow($games->select()->where("id=?", $game['lgame']) );

      $formData = $this->_request->getPost();
      
      // pass gid to the form so we can check that the victor is valid for this game
      $form=new ScoreGameForm( $game['id']);
      
      if ($form->isValid($formData) ) {
	// post with valid form data

	// if the game is unresolved, set victor to empty string
	if( 'unresolved' == $form->getValue('victor') ) {
	  $this->_scoreGame(  $game->id, '' );
	} else {
	  $this->_scoreGame(  $game->id, $form->getValue('victor'));
	}
	



       	$this->_redirect('games');

      } else {
	// post with invalid form data
	// probably should do something useful here
	
	echo "ERROR: Invalid game or victor submitted ";
	print_r($formData);
	$form->populate($formData);

      }
      
    } else {
      
      // not a post, so we print the scoring form for this game
      $gid= $this->_request->getParam('game',0);
      $games = new Games();
      $game = $games->fetchRow($games->select()->where("id=?", $gid) );

      $teams = new Teams();
      $team = $teams->fetchRow( $teams->select()->where("code=?", $game['sidea'])); 
      $this->view->sidea= $team['code'] . " " . $team['org'];
      $team = $teams->fetchRow($teams->select()->where("code=?", $game['sideb']));
      $this->view->sideb=$team['code']. " " . $team['org']; 
      
      $form = new ScoreGameForm($game['id']);
      $this->view->form = $form;
    }       
  } 


  // set the winner and send teams to next games as appropriate
  function _scoreGame( $gameid, $victor) {



    
    // get the game, wgame, and lgame from the database for the given game id

    $games = new Games();
    $game = $games->fetchRow($games->select()->where("id=?", $gameid) );
    $wgame = $games->fetchRow($games->select()->where("id=?", $game['wgame']) );
    $lgame = $games->fetchRow($games->select()->where("id=?", $game['lgame']) );


    // fail if the victor is set for the wgame or lgame

    if (isset($wgame->victor) AND $wgame->victor !='' OR isset($lgame->victor) AND $lgame->victor !='') {
      throw new Exception('This game has dependencies, unresolve those before changing this one. ');    
    }

    if ( "bye" == $victor AND  ("bye" != $game->sidea OR "bye" != $game->sideb)) {
      throw new Exception('A bye can never win over a non-bye. ');    
    }


    // set the winner
    $game->victor = $victor;
    $game->save();

    // if this isn't the final game
    if(0 != $game->wgame) {

      // send the victor to the next game
      if( 'A'== $game['wside']  ) {
	$wgame->sidea = $game['victor'];
      } else {
	$wgame->sideb = $game['victor'];
      }
      $wgame->save();
    }

    $vanquished="";
    if ( $game['victor'] == $game->sidea ) {
      $vanquished = $game->sideb ;
    } else{
      $vanquished = $game->sidea;
    }

    // if the lgame isn't negative ( indicationg a consolation game was lost )
    // AND if the game isn't a final game or the loser wasn't on the b side of the final
    
    if( ( 0 < $game->lgame ) and ( 0 == $game->final or $vanquished != $game->sideb ) )  {

      if( 'A'== $game->lside  ) {
	$lgame->sidea = $vanquished;
      } else {
	$lgame->sideb = $vanquished;
      }
	    
      $lgame->save();
    }  
  }




  




  function _scoreByes() {

    $games = new Games();
    // unplayed games have no victor, but do have a valid  team  for both sidea and sideb
    $playable=$games->fetchAll($games->select()->where("( victor is NULL OR victor ='') AND  sidea  in (select code from teams UNION select 'bye' as code) AND  sideb in (select code from teams UNION select 'bye' as code)") );

    foreach( $playable as $match ) {
      if ( 'bye' == $match->sidea) {
	$this->_scoreGame($match->id, $match->sideb);
      } else if ( 'bye' == $match->sideb ) {
	$this->_scoreGame($match->id, $match->sidea);
      }
    }
  }


  function _scorePlayable() {

    $games = new Games();
    // unplayed games have no victor, but do have a valid  team  for both sidea and sideb
    $playable=$games->fetchAll($games->select()->where("( victor is NULL OR victor ='') AND  sidea  in (select code from teams UNION select 'bye' as code) AND  sideb in (select code from teams UNION select 'bye' as code)") );

    foreach( $playable as $match ) {
      if ( $match->sideb < $match->sidea) {
	$this->_scoreGame($match->id, $match->sideb);
      } else {
	$this->_scoreGame($match->id, $match->sidea);
      }
    }
  }


}