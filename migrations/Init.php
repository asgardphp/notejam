<?php
class Init extends \Asgard\Migration\DBMigration {
	public function up() {
		$this->container['schema']->create('note', function($table) {
			$table->add('id', 'int(11)')
				->primary()
				->autoincrement();
			$table->add('name', 'varchar(255)')
				->nullable();
			$table->add('text', 'text')
				->nullable();
			$table->add('created_at', 'datetime')
				->nullable();
			$table->add('updated_at', 'datetime')
				->nullable();
			$table->add('pad_id', 'int(11)')
				->nullable();
		});
		
		$this->container['schema']->create('pad', function($table) {
			$table->add('id', 'int(11)')
				->primary()
				->autoincrement();
			$table->add('name', 'varchar(255)')
				->nullable();
			$table->add('user_id', 'int(11)')
				->nullable();
		});
		
		$this->container['schema']->create('user', function($table) {
			$table->add('id', 'int(11)')
				->primary()
				->autoincrement();
			$table->add('email', 'varchar(255)')
				->nullable();
			$table->add('password', 'varchar(255)')
				->nullable();
		});
	}

	public function down() {
		$this->container['schema']->drop('note');
		
		$this->container['schema']->drop('pad');
		
		$this->container['schema']->drop('user');
	}
}