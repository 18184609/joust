<?php

class BracketController extends Zend_Controller_Action 
{
  // number of teams in bracket ( must be n*n)

  public $bracket_size;


  function indexAction() {

    $gamesDAO = new Games;
    
    // main DE Bracket
   // $this->view->wgames = $gamesDAO->fetchAll($gamesDAO->select()->where("final=0 AND consolation=0") ->order('id') );

    $this->view->wgames =$gamesDAO->fetchAll($gamesDAO->select()->setIntegrityCheck(false)
					    ->from(array('g' =>'games'))
					    ->where("consolation = 0 AND final =0" )
					    ->joinLeft( array('sidea'=>'teams'),  // join table
							'g.sidea=sidea.code', // join condition
							array('sideacode'=>'sidea.code', 'sideaorg'=>'sidea.org') ) // columns to select, aliased
					    ->joinLeft( array('sideb'=>'teams'), 
							'g.sideb=sideb.code', 
							array('sidebcode'=>'sideb.code', 'sideborg'=>'sideb.org') )
					    ->order('id'));




  }

  function consolationAction() {

    $gamesDAO = new Games;
    
    // Consolation Bracket
//    $this->view->lgames = $gamesDAO->fetchAll($gamesDAO->select() ->where("final=0 AND consolation > 0") ->order('id') );

    $this->view->lgames =$gamesDAO->fetchAll($gamesDAO->select()->setIntegrityCheck(false)
					     ->from(array('g' =>'games'))
					     ->where("final =0 and consolation > 0" )
					     ->joinLeft( array('sidea'=>'teams'),  // join table
							 'g.sidea=sidea.code', // join condition
							 array('sideacode'=>'sidea.code', 'sideaorg'=>'sidea.org') ) // columns to select, aliased
					     ->joinLeft( array('sideb'=>'teams'), 
							 'g.sideb=sideb.code', 
							 array('sidebcode'=>'sideb.code', 'sideborg'=>'sideb.org') )
					     ->order('id'));
  }


  function finalsAction() {

    $gamesDAO = new Games;
    
    $this->view->fgames =$gamesDAO->fetchAll($gamesDAO->select()->setIntegrityCheck(false)
					    ->from(array('g' =>'games'))
					    ->where("final !=0" )
					    ->joinLeft( array('sidea'=>'teams'),  // join table
							'g.sidea=sidea.code', // join condition
							array('sideacode'=>'sidea.code', 'sideaorg'=>'sidea.org') ) // columns to select, aliased
					    ->joinLeft( array('sideb'=>'teams'), 
							'g.sideb=sideb.code', 
							array('sidebcode'=>'sideb.code', 'sideborg'=>'sideb.org') )
					    ->order('id'));




  }
  

  public function buildAction() 
  {
    $bodyCopy = "( If you're starting the DE phase of the tournament, then this is exactly what you want. )" ; // messages back to the user
    
    $form = new DoubleElimForm();
    
    if ($this->_request->isPost()) {

      // we got data, so we try to process it 

      $formData = $this->_request->getPost();

      if ($form->isValid($formData)) {  

	// form is valid, process it 
	$areyousure = $form->areyousure;
	
	if (true == $areyousure->checked) { 
	
	  $this->_round234();
	  $this->_redirect('games');
	}  else  { 

	  // user wasn't sure, ask again

	  $bodyCopy = "<p><b>You didn't check the \"are you sure\" box, so I'm giving you another oppurtunity to back out.</b> "; 
	  $form->populate($formData);
	}
	
      } else { 	

	// form wasn't valid, ask again

	$form->populate($formData);
      }
    }
       
    // display the form and the body copy message
    $this->view->bodyCopy = $bodyCopy ;
    $this->view->form = $form;
    
  }







  function _round234()
  {

    $this->_clearGames();

    // should set bracket size in database
    // should set bracket status to disallow further seeding changes

    $this->_sizeBracket();


    // set up round 1
    $this->_firstGames();
    $this->_seedAdvantage();
    
    // set up middle rounds with no wierd features
    for( $round=2; $round <= log($this->bracket_size, 2); $round++) {
      $this->_nextWGames( $round);
      $this->_nextC1Games( $round);
      $this->_nextC2Games( $round);
    }

    // set up final rounds
    $this->_finalGames();

    $this->_redirect('games');

  }


  function _clearGames() {

      $gameDAO= new Games();
      $gameDAO->delete("id is not null");

  }


  function _sizeBracket() {  
    $teamsDAO = new Teams(); 
    $this->bracket_size =  pow(2, ceil( log(count($teamsDAO->fetchall() ), 2)) );
  }

  function _firstGames() 
  {

    $gamesFactory = new Games();

    $first_game=1;
    $last_game= $first_game +  ($this->bracket_size/ pow(2,1)) - 1;
    $wgame= $last_game + 1;
    $lgame= $wgame + $this->bracket_size/pow(2,2);

    for( $i = 1; $i <= $last_game; $i++) {


      if(  1 == ($i) % 2  ) {
	$wside =$lside = 'A';
      }else {
	$wside =$lside = 'B';
      }
      
      $match= array(
		    'id' => $i,
		    'bracket' => $this->bracket_size,
		    'round'=>1,
		    'wgame'=>$wgame,
		    'wside'=>$wside,
		    'lgame'=>$lgame,
		    'lside'=>$lside,
		    );

      if(  0 == ($i) % 2  )
	{
	  $wgame++;
	  $lgame++;
	} // after every even game, the next game goes to a new wgame and lgame
      $gamesFactory->insert($match);
    }    
  }



  // creates next batch of winner games
  function _makeWGames( $round, $first_game,  $last_game,   $wgame,  $lgame ) 
  {

    $gamesFactory = new Games();
    for( $i = $first_game; $i <= $last_game; $i++) {
      
      if(  1 == ($i) % 2  ) { $wside  = 'A'; }else { $wside = 'B'; }

	if ( 0 == $round %2) {
	  // Because of the way we're arranging the brackets, we send loosers
	  // to the  A side for  odd games  and the the B side for even games
	  $lside = 'B';  
	} else {
	  $lside = 'A';
	}


      // if there's more than one option for games in the next C phase, then
      // send people to the next pr previous grouping on the C bracket when the lose
      // so they won't face the same opponent as often in the C bracket

      if ($first_game != $last_game) {
	$xlgame=(1 == $lgame % 2 ) ? ($lgame + 1) : ($lgame - 1);
      } else{
	$xlgame = $lgame;
      }

      $match= array(
		    'id' => $i,
		    'bracket' => $this->bracket_size,
		    'round'=>$round,
		    'wgame'=>$wgame,
		    'wside'=>$wside,
		    'lgame'=>$xlgame,
		    'lside'=>$lside,
		    );
      
      // every other game, send people to the next wgame
      if(  0 == ($i) % 2  ) { 
	$wgame++; 
      } 

      // every game, send people to the next lgame
      $lgame++;
      $gamesFactory->insert($match);
    }

  }



  // should work for round 2 and higher
  function _nextWGames( $round) {

    // first game of this win phase
    $first_game= ($this->bracket_size/ pow(2,1)) +1;
    for( $i=2;  $i < $round; $i++){
      $first_game += 3 * ( $this->bracket_size/ pow(2, $i) );
    }

    // last game of this win phase
    $last_game= $first_game +  ($this->bracket_size/ pow(2,$round)) -1;

    // first game of win  phase of next round
    $wgame=  $first_game + 3 * ( $this->bracket_size / pow(2, $round) );
    
    // first game of C2 phase of this round
    $lgame=  $first_game + 2* ( $this->bracket_size / pow(2, $round) );

    echo "<br>_makeWGames($round,$first_game, $last_game, $wgame, $lgame)";
    $this->_makeWGames($round,$first_game, $last_game, $wgame, $lgame);


   
  }





  // creates next batch of stage 1 consolation games
  function _makeC1Games( $round, $first_game, $last_game, $wgame) { 

      $gamesFactory = new Games();

      for( $i = $first_game; $i <= $last_game; $i++) {

	if ( 0 == $round %2) {
	  // Because of the way we're arranging the brackets, we send winners 
	  // to the  B side for  odd games  and the the A side for even games
	  $wside = 'A';  
	} else {
	  $wside = 'B';
	}


	$match= array(
		      'id' => $i,
		      'bracket' => $this->bracket_size,
		      'consolation'=> 1,
		      'round'=>$round,
		      'wgame'=>$wgame,
		      'wside'=>$wside,
		      'lgame'=>0-$i,
		      'lside'=>0,
		      );
	
	// every game, send people to the next wgame
	$wgame++;
	// no lgames for the C phases of each round
	$gamesFactory->insert($match);
      }
    }



  // should work for round 2 or higher
  function _nextC1Games( $round) {

    // first game of  win phase
    $first_game=($this->bracket_size/2) +1;
    for( $i=2;  $i < $round; $i++){
      $first_game += 3 * ( $this->bracket_size/ pow(2, $i) );
    }

    //first game of C1 phase
    $first_game += $this->bracket_size / pow(2, $round);
    $last_game= $first_game +  ($this->bracket_size/ pow(2,$round)) -1;
    $wgame= $last_game+1;

    echo "<br>_makeC1Games($round,$first_game, $last_game, $wgame) ";
    $this->_makeC1Games($round,$first_game, $last_game, $wgame);
  }



  function _makeC2Games( $round, $first_game, $last_game, $wgame) { 

      $gamesFactory = new Games();

      for( $i = $first_game; $i <= $last_game; $i++) {

	
	// if we're building the final consolation game 
	// ( which is the 3rd game before the end )
	// then the wside is always B
	// otherwise, it alternates for normal C2 games

	if ( ($this->bracket_size*2) - 3 == $i ) { $wside='B'; } 
	else if(  0 == ($i) % 2 ) { $wside = 'B'; }
	else { $wside =  'A'; }
	
	
	$match= array(
		      'id' => $i,
		      'bracket' => $this->bracket_size,
		      'consolation'=> 2,
		      'round'=>$round,
		      'wgame'=>$wgame,
		      'wside'=>$wside,
		      'lgame'=>0-$i,
		      'lside'=>0,
		      );
	
    // every other game, send people to the next wgame
	if(  0 == ($i) % 2  ) { 
	  $wgame++;  
	} 
	
	$gamesFactory->insert($match);
      }
    }


  // should work for round 2 or higher
  function _nextC2Games( $round) {

    // first game of  win phase
    $first_game=$this->bracket_size/2 + 1;

    for( $i=2;  $i < $round; $i++){
      $first_game += 3 * ( $this->bracket_size / pow(2, $i) );
    }

    // first game of C2 phase 
    $first_game += 2* ($this->bracket_size / pow(2, $round) );

    $last_game= $first_game +  ($this->bracket_size/ pow(2,$round)) -1;


    // floor rounds down to 0 for non integer fractions
    $wgame= $last_game+ 1 + floor(($this->bracket_size / pow(2, $round+1) ));

    echo "<br> _makeC2Games($round,$first_game, $last_game, $wgame) " ;

    $this->_makeC2Games($round,$first_game, $last_game, $wgame) ;
  }


  function _finalGames() {

    $gamesFactory = new Games();
    
    $round=log($this->bracket_size, 2)+1;
    $game1=$this->bracket_size*2 - 2;

    $final1= array(
		   'id' => $game1,
		   'bracket' =>$this->bracket_size,
		   'final'=>1,
		   'round'=> $round,
		   'wgame'=> $game1 + 1,
		   'wside'=>'A',
		   'lgame'=> $game1 + 1,
		   'lside'=>'B',
		   );

    $final2= array(
		   'id' => $game1 + 1,
		   'bracket' =>$this->bracket_size,
		   'final'=> 2,
		   'round'=>$round,
		   'wgame'=>$game1 + 2,
		   'wside'=>'A',
		   'lgame'=>0 - ($game1+1),
		   'lside'=>0,
		   );

    $final3= array(
		   'id' => $game1 + 2,
		   'bracket' =>$this->bracket_size,
		   'final'=> 3,
		   'round'=>$round,
		   'wgame'=>0,
		   'wside'=>0,
		   'lgame'=>0,
		   'lside'=>0,
		   );



    $gamesFactory->insert($final1);
    $gamesFactory->insert($final2);
    $gamesFactory->insert($final3);

  }


  function _seedSimpeAdvantage() 
  {

    $gamesFactory = new Games();
    $rosterFactory = new Roster();
    $roster = $rosterFactory->fetchAll( $rosterFactory->select()->order('seedrank ASC'));

    // store the team codes by seed rank
    // we don't have to count because they're already sorted
    $rankedTeams = array();
    $rankedTeams[]=0;
    foreach ($roster as $team) {
      $rankedTeams[] = $team['code'];
    }


    // how many teams do we have?
    $teamcount =  count($roster);

    for( $i = 0; $i < $this->bracket_size / 2 ; $i++) {
      
      $victor='';
      $aRank =(2 * $i) +1;
      $bRank =$this->bracket_size -(2 *$i);
      
      if ( $aRank <= $teamcount){
	  $sidea=$rankedTeams[$aRank];
      } else {
	$sidea="bye";
      }

      if ( $bRank <= $teamcount ){
	$sideb= $rankedTeams[$bRank];
      } else {
	$sideb= "bye";       
      }
      
      $row = $gamesFactory->fetchRow($gamesFactory->select()->where('id=?', $i+1) );
      $row->sidea = $sidea ;
      $row->sideb = $sideb ;
      $row->save();
    }
  }




  function _seedAdvantage() 
  {

    $gamesFactory = new Games();
    $rosterFactory = new Roster();
    $roster = $rosterFactory->fetchAll( $rosterFactory->select()->order('seedrank ASC'));

    // store the teams by seed rank
    // we don't have to count because they're already sorted
    $rankedTeams = array();
    $rankedTeams[]=0;
    foreach ($roster as $team) {
      $rankedTeams[] = $team['code'];
    }




    $mySeeding = $this->_seedingAssigner( $this->bracket_size);
    $game=1;

    foreach ($mySeeding as $match ) {

      $sidea = $rankedTeams [ $mySeeding[$game-1]['A'] ] ;
      if(is_null($sidea)) {
	$sidea = 'bye';
      }
      $sideb = $rankedTeams [ $mySeeding[$game-1]['B'] ] ;
      if(is_null($sideb)) {
	$sideb = 'bye';
      }


      $row = $gamesFactory->fetchRow($gamesFactory->select()->where('id=?', $game) );
      $row->sidea = $sidea ;
      $row->sideb = $sideb ;
      $row->save();
      $game++;
    }

      

  }




// given a power of 2 
// return seeding assignments appropriate to a bracket for that many teams

  function _seedingAssigner( $bracketsize ) 
  {

    // check that bracketsize is a power of 2
    assert( '0==( $bracketsize & ( $bracketsize -1));');
  
    // set up the base case for a bracket of 2 teams
    $seeding= array(  array( 'A' => 1, 'B' => 2 ) );
  
    // add more games until we get to the right bracket size
    while( count($seeding) < ($bracketsize/2 ) ) {
      $seeding = $this->_nextMatches($seeding);
    }
    return $seeding;
  }

  function _nextMatches ($seeding) {
  
    $matchCount= count($seeding);
    $nextMatchSum =  ($matchCount * 2 * 2) +1;

    // each match in the current seeding array sets up two matches in the next seeding array
    // so (1,4) (3,2) becomes (1, )( ,4)(3, )( ,2) 
    $newMatches=array();
    foreach( $seeding as $match ) {
      $newMatches[] = array( 'A' => $match['A'], 
			     'B' => '' ); 
      $newMatches[] = array( 'A' => '',
			     'B' => $match['B']  );
    }  
    // each of the new matches needs to get paired up with the appropriate seed. 
    // so (1, )( ,4)(3, )( ,2)  becomes (1,8)(5,4)(3,6)(7,2)
    foreach ($newMatches as &$match) {
      if ($match['A']==='') 
      { 
	$match['A'] =  $nextMatchSum - $match['B']; 
      }
      else if ($match['B']==='') 
      {   
	$match['B'] = $nextMatchSum - $match['A']; 
      }
    


    }
    unset($match );

    return $newMatches;

  } 


}
