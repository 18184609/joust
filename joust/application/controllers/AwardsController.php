<?php

class AwardsController extends Zend_Controller_Action 
{
  
  function indexAction()
  {
    $awards = new Awards();

    $this->view->trophies=$awards->fetchAll($awards->select()->where("type='T'") ->order('id'));
    $this->view->certificates =$awards->fetchAll($awards->select()->where("type='C'") ->order('id'));


  }


  function addAction()
  {
    $form = new AwardForm();
    $form->submit->setLabel('Add');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();
      if ($form->isValid($formData) ) {
	  $awards= new Awards();
	  	  
	  $row = $awards->createRow();
	  $row->name = $form->getValue('name');
	  $row->type = $form->getValue('type');
	  $row->team = $form->getValue('team');
	  $row->notes = $form->getValue('notes');
	  $row->save();
	  
	  $this->_redirect('awards');
	} else {
	  $form->populate($formData);
      }
    }
  }




  function editAction()
  {
    $form = new AwardForm();
    $form->submit->setLabel('Edit');
    $this->view->form =$form;

    if($this->_request->isPost()) {
      $formData = $this->_request->getPost();

      if ($form->isValid($formData) ) {
	  $id = $form->getValue('id');

	  $awards= new Awards();
	  $row = $awards->fetchRow("id='".$id."'");

	  $row->name = $form->getValue('name');
	  $row->type = $form->getValue('type');
	  $row->team = $form->getValue('team');
	  $row->notes = $form->getValue('notes');
	  $row->save();
	  
	  $this->_redirect('awards');
	} else {
	  $form->populate($formData);
      }
    } else {
      $id= $this->_request->getParam('id',0);
      $awards = new Awards();
      $award = $awards->fetchRow("id='".$id."'");
      
     // should implement this with a fliter
      $formdata = $award->toArray();
      $formdata['name'] = stripslashes($formdata['name']);
      $formdata['notes'] = stripslashes($formdata['notes']);

      $form->populate($formdata );
      $this->view->award = $award;
    }
  }

  function deleteAction() 
  { 
    if ($this->_request->isPost()) { 
      $id = $this->_request->getPost('id'); 
      $del = $this->_request->getPost('del'); 

      if ($del == 'Yes' && "" != $id ) { 
	$awards= new Awards(); 
	$where = $awards->getAdapter()->quoteInto('id = ?', $id);
	$awards->delete($where); 
      } 
      $this->_redirect('awards'); 
    } else { 
      $id = $this->_request->getParam('id'); 
      if ("" != $id ) { 
	$awards = new Awards(); 
	$this->view->award= $awards->fetchRow($awards->select()->where('id = ?', $id) ); 
      } 
    }
  }


}