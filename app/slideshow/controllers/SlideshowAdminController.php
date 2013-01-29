<?php
/**
@Prefix('admin/slideshow')
*/
class SlideshowAdminController extends \App\Admin\Libs\Controller\AdminParentController {
	public function formConfigure() {
		$controller = $this;
		$form = new \App\Admin\Libs\Form\AdminSimpleForm($this);
		$form->images = new DynamicGroup(function($data) use($controller) {
			if($data !== null)
				if($data === '' || (is_array($data) && !array_filter(Tools::flateArray($data))))
					return;
			return new AdminModelForm(new Slide, $controller);
		});
		foreach(Slide::orderBy('id ASC')->get() as $k=>$a){
			$form->images[$k] = new \App\Admin\Libs\Form\AdminModelForm($a, $this);
		}
		$form->hasfile = true;

		$controller = $this;
		$form->images->setDefaultRender(function($field) use($form, $controller) {
			return 	'<div class="slide">'.$form->h4('Image'.($field->getModel()->isOld() ? ' <a href="'.$controller->url_for('delete', array('id'=>$field->getModel()->id)).'" style="font-size:10px">Delete</a>':'')).
			$field->image->def().
			$field->description->textarea().
			'</div>';
		});

		return $form;
	}

	/**
	@Route(':id/delete')
	*/
	public function deleteAction($request) {
		if(Slide::destroyOne($request['id']))
			\Flash::addSuccess('Slide deleted with success.');
		return \Response::back();
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->form = $this->formConfigure();

		if($this->form->isSent()) {
			try {
				$this->form->save();
				Flash::addSuccess('The slideshow was saved successfully.');
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		$this->setRelativeView('form.php');
	}
}