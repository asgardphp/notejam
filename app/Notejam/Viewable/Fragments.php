<?php
namespace Notejam\Viewable;

class Fragments {
	use \Asgard\Templating\ViewableTrait;

	public function pads($controller, $user) {
		if(!($this->user = $user))
			return '';
		$this->controller = $controller;
	}

	public function notes($request, $orm) {
		$this->url     = $request->url;
		$sort          = $request->get->get('sort') === 'note' ? 'name':'updated_at';
		$dir           = $request->get->get('dir') === 'ASC' ? 'ASC':'DESC';
		$this->sortDir = $sort.' '.$dir;
		$this->notes   = $orm->orderBy($this->sortDir);
	}
}