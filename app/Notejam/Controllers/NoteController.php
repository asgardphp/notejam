<?php
namespace Notejam\Controllers;

/**
 * @Prefix("notes")
 */
class NoteController extends \Asgard\Http\Controller {
	public $user;
	
	public function before(\Asgard\Http\Request $request) {
		if(!$this->user)
			return $this->response->redirect($this->url(['Notejam\Controllers\UserController', 'signin']));

		if(isset($request['note_id'])) {
			$this->note = $this->user->notes()->where('id', $request['note_id'])->first();
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

		$note = \Notejam\Entities\Note::load($request['note_id']);
		$this->form = $this->getForm($note, $this->user);
		if($this->form->isValid()) {
			$this->form->save();
			$this->getFlash()->addSuccess('Note is successfully updated.');
			return $this->response->redirect($note->url());
		}
	}

	/**
	 * @Route(":note_id/delete")
	*/
	public function deleteAction($request) {
		$this->container['html']->setTitle($this->note);

		if($request->method() == 'POST') {
			$this->note->destroy();
			$this->getFlash()->addSuccess('Note is successfully deleted.');
			return $this->response->redirect($this->url(['General\Controllers\DefaultController', 'index']));
		}
	}

	protected function getForm($note) {
		$form = $this->container->make('entityform', [$note]);
		$user = $this->user;
		$form->addRelation('pad', function($orm) use($user) {
			$orm->where('user_id', $user->id);
		});
		return $form;
	}
}