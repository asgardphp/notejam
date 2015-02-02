<?php
namespace Notejam\Entities;

class Note extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'name' => [
				'type' => NULL,
				'required' => true,
			],
			'text' => [
				'type' => 'longtext',
				'required' => true,
			],
			'pad' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\Pad',
			],
			'user' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entities\\User',
			],
		];


		$definition->behaviors = [
				new \Asgard\Behaviors\TimestampsBehavior,
			];	}

	public function __toString() {
		return (string)$this->name;
	}

	public function url() {
		return \Asgard\Container\Container::singleton()['resolver']->url(array('Notejam\Controllers\NoteController', 'show'), ['note_id'=>$this->id]);
	}
}