<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host=db;dbname=ext_buo',
			'username'   => 'root',
			'password'   => 'ext_buo',
		),
        'profiling' => true,
	),
);
