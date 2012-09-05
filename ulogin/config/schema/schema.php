<?php

/* SVN FILE: $Id$ */
/* App schema generated on: 2010-12-14 14:12:19 : 1292327779 */

class AppSchema extends CakeSchema {

    var $name = 'App';

    function before($event = array()) {
        return true;
    }

    function after($event = array()) {

    }

	var $soc_connections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => 0),
        'uid' => array('type' => 'string', 'null' => false),
        'provider' => array('type' => 'string', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'uid' => array('column' => 'uid', 'unique' => 0), 'provider' => array('column' => 'provider', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

}
