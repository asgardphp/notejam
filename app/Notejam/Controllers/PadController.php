<?php
namespace Notejam\Controllers;

/**
 * @Prefix("pads")
 */
class PadController extends \Asgard\Http\Controller {
	public function before(\Asgard\Http\Request $request) {
		if(!$this->user = $request->session->get('user'))
			return $this->response->redirect($this->container['resolver']->url(['Notejam\Controllers\UserController', 'signin']));

		if(isset($request['pad_id'])) {
			$this->pad = $this->user->pads()->where('id', $request['pad_id'])->first();
			if(!$this->pad)
				$this->notFound();
		}
	}

	/**
	 * @Route("create")
	*/
	public function createAction($request) {
		$this->container['html']->setTitle('New pad');
		$this->view = 'form';

		$pad = $this->user->pads()->make();
		$this->form = $this->container->make('entityform', [$pad]);
		if($this->form->isValid()) {
			$this->form->save();
			$this->getFlash()->addSuccess('Pad is successfully created.');
			return $this->response->redirect($pad->url());
		}
	}

	/**
	 * @Route(":pad_id")
	*/
	public function showAction($request) {
		$this->container['html']->setTitle($this->pad.' ('.$this->pad->notes()->count().' notes)');
	}

	/**
	 * @Route(":pad_id/edit")
	*/
	public function editAction($request) {
		$this->container['html']->setTitle($this->pad.' ('.$this->pad->notes()->count().' notes)');
		$this->view = 'form';

		$this->form = $this->container->make('entityform', [$this->pad]);
		if($this->form->isValid()) {
			$this->form->save();
			$this->getFlash()->addSuccess('Pad is successfully edited.');
			return $this->response->redirect($this->pad->url());
		}
	}

	/**
	 * @Route(":pad_id/delete")
	*/
	public function deleteAction($request) {
		$this->container['html']->setTitle($this->pad.' ('.$this->pad->notes()->count().' notes)');

		if($request->method() == 'POST') {
			$this->pad->destroy();
			$this->getFlash()->addSuccess('Pad is successfully deleted.');
			return $this->response->redirect($this->container['resolver']->url(['General\Controllers\DefaultController', 'index']));
		}
	}
}