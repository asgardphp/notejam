<?php
$config = array(
	'all' => array(
		'key'	=>	'coxis',
		'admin'	=>	array(
			'footer'	=>	''
		),
		'salt'	=>	'FCT7f6ew%^',
		'bundle_directories' => array('bundles', 'app'),
		'imagecache'	=>	false,
		'locale'	=>	'fr',
		'locales'	=>	array(
			'fr', 'en'
		),
		'cache'	=>	array(
			// 'method'	=>	'apc',
			'method'	=>	'file',
		)
	),
	'dev'	=>	array(
		'phpcache'	=>	false,
		// 'phpcache'	=>	true,
	),
);