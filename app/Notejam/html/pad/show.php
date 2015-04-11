	<?=$fragments->fragment('pads', [$this, $user])?>
	<div class="thirteen columns content-area">
		<?php $this->getFlash()->showAll() ?>
		<?=$fragments->fragment('notes', [$request, $pad->notes()])?>
		<a href="<?=$this->url(['Notejam\Controllers\NoteController', 'create'])?>" class="button">New note</a>&nbsp;
		<a href="<?=$this->url(['Notejam\Controllers\PadController', 'edit'], ['pad_id'=>$pad->id])?>" class="button">Edit pad</a>&nbsp;
		<a href="<?=$this->url(['Notejam\Controllers\PadController', 'delete'], ['pad_id'=>$pad->id])?>" class="button">Delete pad</a>
	</div>