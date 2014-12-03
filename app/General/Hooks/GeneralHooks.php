<?php
namespace General\Hooks;

class GeneralHooks extends \Asgard\Hook\HookContainer {
	/**
	 * @Hook("Asgard.Http.Start")
	 */
	public static function maintenance(\Asgard\Hook\Chain $chain, \Asgard\Http\Request $request) {
		if($chain->getContainer()['kernel']['env'] == 'prod' && file_exists($chain->getContainer()['kernel']['root'].'/storage/maintenance')) {
			$controller = new \General\Controllers\DefaultController($chain->getContainer());
			return $controller->run('maintenance', $request);
		}
	}

	/**
	 * @Hook("Asgard.Http.Exception.Asgard\Http\Exceptions\NotFoundException")
	 */
	public static function hook404Exception(\Asgard\Hook\Chain $chain, \Exception $exception, \Asgard\Http\Response &$response, \Asgard\Http\Request $request) {
		$response = $chain->getContainer()['httpKernel']->runController('General\Controllers\DefaultController', '_404', $request)->setCode(404);
	}

	/**
	 * @Hook("Asgard.Http.Output")
	 * @Priority(1000)
	 */
	public static function gzip(\Asgard\Hook\Chain $chain, \Asgard\Http\Response $response, \Asgard\Http\Request $request) {
		// if(!strstr($request->server['HTTP_ACCEPT_ENCODING'], 'gzip') || !$response->getContent())
			return;

		$output = gzencode($response->getContent());
		$response
			->setContent($output)
			->setHeader('Content-Encoding', 'gzip')
			->setHeader('Content-Length', strlen($output))
			->setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
			->setHeader('Pragma', 'no-cache');
	}
}