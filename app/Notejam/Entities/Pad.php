<?php
namespace Notejam\Entities;

class Pad extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'name' => [
				'type' => NULL,
				'required' => true,
			],
			'user' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\User',
			],
			'notes' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\Note',
				'many' => true,
			],
		];
	}

	public function __toString() {
		return (string)$this->name;
	}

	public function url() {
		return \Asgard\Container\Container::singleton()['resolver']->url(array('Notejam\Controllers\PadController', 'show'), ['pad_id'=>$this->id]);
	}
}