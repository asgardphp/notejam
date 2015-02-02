<?php
class Automigrate extends \Asgard\Migration\DBMigration {
	public function up() {
		$this->container['schema']->table('note', function($table) {
			$table->add('user_id', 'int(11)')
				->nullable();
			$table->col('pad_id')
				->after('user_id');
		});
	}

	public function down() {
		$this->container['schema']->table('note', function($table) {
			$table->col('pad_id')
				->after('updated_at');
			$table->drop('user_id');
		});
	}
}