<?php
Config::set('slideshow', 'width', 1000);
Config::set('slideshow', 'height', 768);

\Coxis\Imagecache\Libs\ImageCache::addPreset('imagecache', array(
	'resize'	=>	array(
		'width'	 =>	Config::get('slideshow', 'width'),
	),
	'crop'	=>	array(
		'height' =>	Config::get('slideshow', 'height'),
	)
));

\Coxis\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => 'Slideshow', 'link' => 'slideshow');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('bundles/slideshow/icon.svg'), 'link'=>'slideshow', 'title' => __('Slideshow'), 'description' => 'Images du slideshow');