<?php
#template fragments
$controller->fragments = new \Notejam\Viewable\Fragments;
$controller->fragments->addTemplatePathSolver(function($viewable, $template) {
	return 'app/Notejam/html/fragments/'.$template.'.php';
});

#controller requiring user authentication
if($controller !== null && in_array('AuthTrait', class_uses($controller))) {
	$user = $container['auth']->user();
	if(!$user) {
		$container['session']->set('referer', $request->url->full());
		$response = new \Asgard\Http\Response;
		$response->setRequest($request);
		$redirect = isset($controller->authRedirect) && $controller->authRedirect ? $controller->authRedirect : ['Notejam\Controller\PublicUser', 'signin'];
		return $response->setCode(401)->redirect($this->container['resolver']->url($redirect));
	}
	$controller->user = $user;
}