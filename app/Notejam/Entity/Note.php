<?php
namespace Notejam\Entity;

class Note extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'name' => [
				'type' => NULL,
				'required' => true,
			],
			'text' => [
				'type' => 'text',
				'required' => true,
			],
			'pad' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\Pad',
			],
			'user' => [
				'type' => 'entity',
				'entity' => 'Notejam\\Entity\\User',
			],
		];


		$definition->behaviors = [
			new \Asgard\Behaviors\TimestampsBehavior,
		];
	}

	public function __toString() {
		return (string)$this->name;
	}

	public function url() {
		return $this->getDefinition()->getContainer()['resolver']->url(array('Notejam\Controller\Note', 'show'), ['note_id'=>$this->id]);
	}
}