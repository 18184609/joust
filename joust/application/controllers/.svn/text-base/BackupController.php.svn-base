<?php 


class BackupController extends Zend_Controller_Action 
{ 


  function indexAction() {


    $db = Zend_Registry::get('dbAdapter');
    $config = Zend_Registry::get('configuration');



    // lock the database by starting a transaction
    $db->beginTransaction();
    
    // while the database is locked, copy the file
    $datafile = $config->database->params->dbname;
    copy( $datafile, $datafile.".".time());
    
    // unlock the database by ending the transaction
    $db->commit();
    
    
    // get the list of backup files to send to the view for display

    $backups=array();
    $backupdir=dirname($datafile);
    foreach( scandir( $backupdir) as $bufile ) {
      if( false !== strpos($bufile, "joust") )
	$backups[] = basename($bufile);
    }


    $this->view->backups = $backups;



  }



}
