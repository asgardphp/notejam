<?php
namespace Notejam\Entity;

class Pad extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'name' => [
				'type' => NULL,
				'required' => true,
			],
			'user' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\User',
			],
			'notes' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\Note',
				'many' => true,
			],
		];
	}

	public function __toString() {
		return (string)$this->name;
	}

	public function url() {
		return $this->getDefinition()->getContainer()['resolver']->url(array('Notejam\Controller\Pad', 'show'), ['pad_id'=>$this->id]);
	}
}