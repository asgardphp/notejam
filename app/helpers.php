<?php
#Debug
if(!function_exists('d')) {
	function d() {
		if(\Asgard\Container\Container::singleton()['config']['debug'] === false)
			return;
		call_user_func_array(['Asgard\Debug\Debug', 'dWithTrace'], array_merge([debug_backtrace()], func_get_args()));
	}
}

#Translation
function __($key, $params=[]) {
	return \Asgard\Container\Container::singleton()->get('translator')->trans($key, $params);
}