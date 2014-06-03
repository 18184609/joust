<?php

class Brackets  extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'brackets';
    protected $_primary = 'id';  // set primary key because this is a view
    

}