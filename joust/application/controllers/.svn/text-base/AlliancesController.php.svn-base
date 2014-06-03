<?php

class AlliancesController extends Zend_Controller_Action 
{
  
  function indexAction()
  {
    $alliances = new Alliances();
    $this->view->alliances = $alliances->fetchAll();;
  }

  function addAction()
  {
    $form = new AllianceForm();
    $form->submit->setLabel('Add');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	  $alliances= new Alliances();
	  
	  $row = $alliances->createRow();
	  $row->team1 = $form->getValue('team1');
	  $row->team2 = $form->getValue('team2');
	  $row->game1 = $form->getValue('game1');
	  $row->game2 =$form->getValue('game2');
	  $row->game3 =$form->getValue('game3');
	  $row->save();
	  
	  $this->_redirect('alliances');
	} else {
	  $form->populate($formData);
	
      }
    }
  }

  function editAction()
  {
    $form = new AllianceForm();
    $form->submit->setLabel('Edit');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	  $alliances= new Alliances();
	  $id = $form->getValue('id');
	  
	  $row = $alliances->fetchRow("id='".$id."'");
	  $row->team1 = $form->getValue('team1');
	  $row->team2 = $form->getValue('team2');
	  $row->game1 = $form->getValue('game1');
	  $row->game2 =$form->getValue('game2');
	  $row->game3 =$form->getValue('game3');
	  $row->save();
	  
	  $this->_redirect('alliances');
	} else {
	  $form->populate($formData);
      }
    } else {
      $id= $this->_request->getParam('id',0);
      $alliances = new Alliances();
      $alliance = $alliances->fetchRow("id='".$id."'");
      
     // should implement this with a fliter
      $formdata = $alliance->toArray();
      $form->populate($formdata );
      $this->view->alliance = $alliance;
    }
  }

  function deleteAction() 
  { 
    if ($this->_request->isPost()) { 
      $id = $this->_request->getPost('id'); 
      $del = $this->_request->getPost('del'); 

      if ($del == 'Yes' && "" != $id ) { 
	$alliances = new Alliances(); 
	$where = $alliances->getAdapter()->quoteInto('id = ?', $id);
	$alliances->delete($where); 
      } 
      $this->_redirect('alliances'); 
    } else { 
      $id = $this->_request->getParam('id'); 
      if ("" != $id ) { 
	$alliances = new Alliances(); 
	$this->view->alliance = $alliances->fetchRow($alliances->select()->where('id = ?', $id) ); 
      } 
    }


  }

}