<?php
namespace Notejam\Controller;

/**
 * @Prefix("notes")
 */
class Note extends \Asgard\Http\Controller {
	use \AuthTrait;
	public $fragments;
	
	public function before(\Asgard\Http\Request $request) {
		if(isset($request['note_id'])) {
			$this->note = $this->user->notes()->load($request['note_id']);
			if(!$this->note)
				$this->notFound();
		}
	}

	/**
	 * @Route("create")
	*/
	public function createAction($request) {
		$this->container['html']->setTitle('New Note');
		$this->view = 'form';

		$note = $this->user->notes()->make();
		$this->form = $this->getForm($note);
		if($this->form->isValid()) {
			$this->form->save();
			$this->getFlash()->addSuccess('Note is successfully created.');
			return $this->response->redirect($note->url());
		}
	}

	/**
	 * @Route(":note_id")
	*/
	public function showAction($request) {
		$this->container['html']->setTitle($this->note);
	}

	/**
	 * @Route(":note_id/edit")
	*/
	public function editAction($request) {
		$this->container['html']->setTitle($this->note);
		$this->view = 'form';

		$this->form = $this->getForm($this->note);
		if($this->form->isValid()) {
			$this->form->save();
			$this->getFlash()->addSuccess('Note is successfully updated.');
			return $this->response->redirect($this->note->url());
		}
	}

	/**
	 * @Route(":note_id/delete")
	*/
	public function deleteAction($request) {
		$this->container['html']->setTitle($this->note);

		if($request->method() === 'POST') {
			$this->note->destroy();
			$this->getFlash()->addSuccess('Note is successfully deleted.');
			return $this->response->redirect($this->url(['General\Controller\DefaultController', 'index']));
		}
	}

	protected function getForm($note) {
		$form = $this->container->make('entityform', [$note]);
		$form->addRelation('pad', function($orm) {
			$orm->where('user_id', $this->user->id);
		});
		return $form;
	}
}