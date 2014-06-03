<?php

class Roster extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'standings_view';
    protected $_primary = 'code';  // set primary key because this is a view
    protected $_sequence = false;  // primary key is a natural key, not a sequence
}
