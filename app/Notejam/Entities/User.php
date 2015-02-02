<?php
namespace Notejam\Entities;

class User extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'email' => [
				'type' => NULL,
				'required' => true,
			],
			'password' => [
				'type' => 'password',
			],
			'pads' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\Pad',
				'many' => true,
			],
			'notes' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\Note',
				'many' => true,
			],
		];

	}

	public function __toString() {
		return (string)$this->email;
	}
}