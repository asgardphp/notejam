	<?=$fragments->fragment('pads', [$this, $user])?>
	<div class="thirteen columns content-area">
		<?php $this->getFlash()->showAll() ?>
		<?=$fragments->fragment('notes', [$request, $pad->notes()])?>
		<a href="<?=$this->url(['Notejam\Controller\Note', 'create'])?>" class="button">New note</a>&nbsp;
		<a href="<?=$this->url(['Notejam\Controller\Pad', 'edit'], ['pad_id'=>$pad->id])?>" class="button">Edit pad</a>&nbsp;
		<a href="<?=$this->url(['Notejam\Controller\Pad', 'delete'], ['pad_id'=>$pad->id])?>" class="button">Delete pad</a>
	</div>