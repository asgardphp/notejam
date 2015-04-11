	<?=$fragments->fragment('pads', [$this, $user])?>
	<div class="thirteen columns content-area">
		<div class="alert-area">
			<?php $this->getFlash()->showAll() ?>
		</div>
		<?=$form->open(['attrs'=>['class'=>'pad']])?>
		<?=$form['name']->labelTag()?>
		<?=$form['name']->def()?>
		<?=($error=$form['name']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
		<?=$form->submit('Save')?>
		<?=$form->close()?>
	</div>