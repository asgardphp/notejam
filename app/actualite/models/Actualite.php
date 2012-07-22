<?php
class Actualite extends \Coxis\Core\Model {
	public static $properties = array(
		'titre',
		'date'    =>    array(
			'required'    =>    false,
		),
		'lieu'    =>    array(
			'required'    =>    false,
		),
		'introduction',
		'contenu',
	);
	
	public static $files = array(	
		'image' => array(
			'dir'	=>	'actualite/',
			'type'	=>	'image',
			'required'	=>	false,
			//~ 'multiple'	=>	true,
		),
	);
	
	public static $relationships = array(
		'commentaires'	=>	array(
			'model'	=>	'commentaire',
			'type'		=>	'HMABT',
			//~ HMABT
		),
	);
	
	public static $behaviors = array(
		'slugify' => true,
		'sortable' => true,
	);
		
	public static $meta = array();
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}