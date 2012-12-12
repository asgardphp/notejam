<?php
Config::set('slideshow', 'width', 1000);
Config::set('slideshow', 'height', 768);

\Coxis\Bundles\Imagecache\Libs\ImageCache::addPreset('imagecache', array(
	'resize'	=>	array(
		'width'	 =>	Config::get('slideshow', 'width'),
	),
	'crop'	=>	array(
		'height' =>	Config::get('slideshow', 'height'),
	)
));

\Coxis\Bundles\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => 'Slideshow', 'link' => 'slideshow');