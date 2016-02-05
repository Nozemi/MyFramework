<?php
	require('lib/globals.php');
	
	print_r(SQL::Query(
		1,
		'QUERY',
		'PARAMS',
		array(
			'DBHost' => 'localhost',
			'DBName' => 'noz_db',
			'DBUser' => 'root',
			'DBPass' => 'abc123'
		)
	));
?>