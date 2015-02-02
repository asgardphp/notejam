<?php
class Init extends \Asgard\Migration\DBMigration {
	public function up() {
		$this->container['schema']->create('note', function($table) {
			$table->addColumn('id', 'integer', [
				'notnull' => true,
				'autoincrement' => true,
			]);
			$table->addColumn('name', 'string', [
			]);
			$table->addColumn('text', 'text', [
			]);
			$table->addColumn('pad_id', 'integer', [
			]);
			$table->addColumn('user_id', 'integer', [
			]);
			$table->addColumn('created_at', 'datetime', [
			]);
			$table->addColumn('updated_at', 'datetime', [
			]);
			$table->setPrimaryKey(
				[
					'id',
				]
			);
		});
		
		$this->container['schema']->create('pad', function($table) {
			$table->addColumn('id', 'integer', [
				'notnull' => true,
				'autoincrement' => true,
			]);
			$table->addColumn('name', 'string', [
			]);
			$table->addColumn('user_id', 'integer', [
			]);
			$table->setPrimaryKey(
				[
					'id',
				]
			);
		});
		
		$this->container['schema']->create('user', function($table) {
			$table->addColumn('id', 'integer', [
				'notnull' => true,
				'autoincrement' => true,
			]);
			$table->addColumn('email', 'string', [
			]);
			$table->addColumn('password', 'string', [
			]);
			$table->setPrimaryKey(
				[
					'id',
				]
			);
		});
	}

	public function down() {
		$this->container['schema']->drop('note');
		
		$this->container['schema']->drop('pad');
		
		$this->container['schema']->drop('user');
	}
}