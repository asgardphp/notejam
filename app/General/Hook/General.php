<?php
namespace General\Hook;

class General extends \Asgard\Hook\HookContainer {
	/**
	 * @Hook("Asgard.Http.Start")
	 */
	public static function maintenance(\Asgard\Hook\Chain $chain, \Asgard\Http\Request $request) {
		if(file_exists($chain->getContainer()['kernel']['root'].'/storage/maintenance')) {
			$controller = new \General\Controller\DefaultController;
			$chain->getContainer()['httpKernel']->prepareController($controller, 'maintenance', $request);
			return $controller->run('maintenance', $request);
		}
	}

	/**
	 * @Hook("Asgard.Http.Exception.Asgard\Http\Exceptions\NotFoundException")
	 */
	public static function hook404Exception(\Asgard\Hook\Chain $chain, \Exception $exception, \Asgard\Http\Response &$response, \Asgard\Http\Request $request) {
		$response = $chain->getContainer()['httpKernel']->runController('General\Controller\DefaultController', '_404', $request)->setCode(404);
	}
}