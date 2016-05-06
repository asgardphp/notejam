<?php
namespace Notejam\Entity;

class User extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'email' => [
				'type' => 'email',
				'required' => true,
				'validation' => [
					'unique' => true,
				]
			],
			'password' => [
				'type' => 'password',
			],
			'pads' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\Pad',
				'many' => true,
			],
			'notes' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\Note',
				'many' => true,
			],
		];

	}

	public function __toString() {
		return (string)$this->email;
	}
}