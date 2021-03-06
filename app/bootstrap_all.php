<?php
if(!defined('_ASGARD_START_'))
	define('_ASGARD_START_', time()+microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . $container['kernel']->get('root'));

if(file_exists(__DIR__.'/helpers.php'))
	require_once __DIR__.'/helpers.php';

#Working dir
chdir(__DIR__.'/..');

#Logger
$container->register('logger', function($container) {
	return new Logger($container['config']['log']);
});

#Error handler
$container['errorHandler']
	->ignoreDir(__DIR__.'/../vendor/nikic/php-parser/')
	->setLogPHPErrors($container['config']['log_php_errors'])
	->setDebug($container['config']['debug']);
$container['errorHandler']->setDebug($container['config']['debug']);
if($this->container['config']['log'] && $container->has('logger'))
	$container['errorHandler']->setLogger($container['logger']);
\Asgard\Debug\Debug::setURL($container['config']['debug_url']);

#Translator
foreach(glob($container['kernel']->get('root').'/translations/'.$container['translator']->getLocale().'/*') as $file)
	$container['translator']->addResource('yaml', $file, $container['translator']->getLocale());

#Loading ORM and Timestamps behavior for all entities
$container['hooks']->hook('Asgard.Entity.LoadBehaviors', function($chain, \Asgard\Entity\Definition $definition, array &$behaviors) {
	if(!isset($behaviors['orm']))
		$behaviors[] = new \Asgard\Orm\ORMBehavior;
});

#Add all entities to orm migration
$container['hooks']->hook('Asgard.Entity.Definition', function($chain, $definition) {
	$definition->set('ormMigrate', true);
});

#Call start
$container['httpKernel']->start($container['kernel']->get('root').'/app/start.php');

#Layout
$container['httpKernel']->filterAll('Asgard\Http\Filter\PageLayout', [
	['\General\Controller\DefaultController', 'layout'],
	$container['kernel']->get('root').'/app/General/html/html.php'
]);

#Remove trailing / and www.
$container['hooks']->hook('Asgard.Http.Start', function($chain, $request) {
	$oldUrl = $request->url;
	$newUrl = clone $oldUrl;

	if(preg_match('/^www./', $oldUrl->host()))
		$newUrl->setHost(preg_replace('/^www./', '', $oldUrl->host()));
	if(($url=rtrim($oldUrl->get(), '/')) !== $oldUrl->get())
		$newUrl->setURL($url);

	if($newUrl->full() !== $oldUrl->full())
		return (new \Asgard\Http\Response())->redirect($newUrl->full());
});

\Asgard\File\FileSystem::mkdir('storage/sessions');
session_save_path(realpath('storage/sessions'));

#set the EntityManager static instance for activerecord-like Entity (e.g. new Article or Article::find())
\Asgard\Entity\EntityManager::setInstance($container['entityManager']);

#flash
$container['flash']->setCallback(function($msg, $type) {
	return '<div class="alert alert-'.$type.'">'.$msg.'</div>';
});
$container['flash']->setGlobalCallback(function($flash, $cat) {
	if($flash->has()) {
		echo '<div class="alert-area">';
		$flash->showAll($cat, false);
		echo '</div>';
	}
});

#Libxml warnings
libxml_use_internal_errors(true);

if(php_sapi_name() === 'cli') {
	$url = new \Asgard\Http\URL(new \Asgard\Http\Request, 'localhost', '', '');
	$container['resolver']->setUrl($url);
}

#set default timezone
try {
	date_default_timezone_get();
} catch(\Exception $e) {
	date_default_timezone_set('Europe/London');
}

#user cache
if($container['kernel']->getEnv() === 'prod')
	$container['cache'] = new \Asgard\Cache\Cache(new Doctrine\Common\Cache\FilesystemCache('../storage/cache'));
else
	$container['cache'] = new \Asgard\Cache\Cache;

#auth
$container->register('auth', function($container) {
	return new \Auth($container['session'], $container['cookies'], $container['config']['key']);
});