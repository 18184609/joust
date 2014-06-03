<?php
/**
 * Script for creating and loading database
 */

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
set_include_path(APPLICATION_PATH . '/../library' . PATH_SEPARATOR . '../application/models/' . PATH_SEPARATOR . '../application/forms/' . PATH_SEPARATOR.  get_include_path());


require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

define('BOOTSTRAP', true);
include_once dirname(__FILE__) . '/../application/bootstrap.php';

$dbAdapter = Zend_Registry::getInstance()->dbAdapter;

try {
    
  for($i=1; $i <48; $i++) {
    
    $teamsFactory = new Teams();
    $data = array(
		  'code' => '00-'.$i,
		  'org' => 'Test Team '.$i,
		  'email'=>'lcox@kipr.org',
		  'p1doc' =>$i,
		  'p2doc' =>$i,
		  'p3doc' =>$i,
		  'p4doc' =>$i,
		  'seed1' =>100 - $i,
		  'seed2'=> 100 - $i,
		  'seed3'=> 100 - $i,
		  );
    $teamsFactory->insert($data);
  }
    echo 'Data Loaded.';
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}

// generally speaking, this script will be run from the command line
return true;
