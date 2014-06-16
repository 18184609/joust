<?php

class TeamsController extends Zend_Controller_Action 
{
  
  function indexAction()
  {
    $teams = new Teams();
    $this->view->teams = $teams->fetchAll();

    $roster = new Roster();
    $this->view->roster = $roster->fetchAll("org is not null ", "org");
  }

  function addAction()
  {
    $form = new TeamForm();
    $form->submit->setLabel('Add');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	  $teams= new Teams();
	  
	  $row = $teams->createRow();
	  $row->code = $form->getValue('code');
	  $row->org = $form->getValue('org');
	  $row->email = $form->getValue('email');
	  $row->p1doc=$form->getValue('p1doc');
	  $row->p2doc=$form->getValue('p2doc');
	  $row->p3doc=$form->getValue('p3doc');
	  $row->seed1=$form->getValue('seed1');
	  $row->seed2=$form->getValue('seed2');
	  $row->seed3=$form->getValue('seed3');
	  $row->save();
	  
	  $this->_redirect('teams');
	} else {
	  $form->populate($formData);
	
      }
    }
  }

  function editAction()
  {
    $form = new TeamForm();
    $form->submit->setLabel('Edit');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	  $teams= new Teams();
	  $code = $form->getValue('code');
	  
	  $row = $teams->fetchRow("code='".$code."'");
	  $row->org = $form->getValue('org');
	  $row->email = $form->getValue('email');
	  $row->p1doc=$form->getValue('p1doc');
	  $row->p2doc=$form->getValue('p2doc');
	  $row->p3doc=$form->getValue('p3doc');
	  $row->p4doc=$form->getValue('p4doc');
	  $row->seed1=$form->getValue('seed1');
	  $row->seed2=$form->getValue('seed2');
	  $row->seed3=$form->getValue('seed3');
	  $row->save();
	  
	  $this->_redirect('teams');
	} else {
	  $form->populate($formData);
      }
    } else {
      $code= $this->_request->getParam('code',0);
      $teams = new Teams();
      $team = $teams->fetchRow("code='".$code."'");
      
     // should implement this with a fliter
      $formdata = $team->toArray();
      $formdata['org'] = stripslashes($formdata['org']);

      $form->populate($formdata );
      $this->view->team = $team;
    }
  }

  function deleteAction() 
  { 
    if ($this->_request->isPost()) { 
      $code = $this->_request->getPost('code'); 
      $del = $this->_request->getPost('del'); 

      if ($del == 'Yes' && "" != $code ) { 
	$teams = new Teams(); 
	$where = $teams->getAdapter()->quoteInto('code = ?', $code);
	$teams->delete($where); 
      } 
      $this->_redirect('teams'); 
    } else { 
      $code = $this->_request->getParam('code'); 
      if ("" != $code ) { 
	$teams = new Teams(); 
	$this->view->team = $teams->fetchRow($teams->select()->where('code = ?', $code) ); 
      } 
    }


  }
}
