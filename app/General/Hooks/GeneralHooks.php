<?php
namespace General\Hooks;

class GeneralHooks extends \Asgard\Hook\HookContainer {
	/**
	 * @Hook("Asgard.Http.Start")
	 */
	public static function maintenance(\Asgard\Hook\Chain $chain, \Asgard\Http\Request $request) {
		if($chain->getContainer()['kernel']['env'] == 'prod' && file_exists($chain->getContainer()['kernel']['root'].'/storage/maintenance')) {
			$controller = new \General\Controllers\DefaultController($chain->getContainer());
			$chain->getContainer()['httpKernel']->prepareController($controller, 'maintenance', $request);
			return $controller->run('maintenance', $request);
		}
	}

	/**
	 * @Hook("Asgard.Http.Exception.Asgard\Http\Exceptions\NotFoundException")
	 */
	public static function hook404Exception(\Asgard\Hook\Chain $chain, \Exception $exception, \Asgard\Http\Response &$response, \Asgard\Http\Request $request) {
		$response = $chain->getContainer()['httpKernel']->runController('General\Controllers\DefaultController', '_404', $request)->setCode(404);
	}
}