<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');

require_once __DIR__.'/../autoload.php';

$kernel = new \Kernel(dirname(__DIR__));
$kernel->load();
$container = $kernel->getContainer();

$container['schema']->dropAll();
$mm = new \Asgard\Migration\MigrationManager($container['kernel']['root'].'/migrations/', $container['db'], $container['schema'], $container);
$mm->migrateAll(false);

if(!defined('_TESTING_')) {
	define('_TESTING_', $container['kernel']['root'].'/tests/tested.txt');
	\Asgard\File\FileSystem::delete(_TESTING_);
}
