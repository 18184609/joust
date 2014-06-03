<?php

class AwardsController extends Zend_Controller_Action 
{
  
  function indexAction()
  {
    $awards = new Awards();

    $this->view->trophies =$awards->fetchAll($awards->select()->where("type='T'") ->order('id'));
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
	  $row->name = $form->getValue('type');
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


}