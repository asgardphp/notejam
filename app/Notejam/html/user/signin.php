	<div class="sixteen columns content-area">
		<?php $this->getFlash()->showAll() ?>
		<?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
			<?=$form['email']->labelTag()?>
			<?=$form['email']->def()?>
			<?=$form['password']->labelTag()?>
			<?=$form['password']->password()?>
			<?=$form->submit('Sign In')?> or <a href="<?=$this->url(['Notejam\Controller\User', 'signup'])?>">Sign up</a>
			<hr />
			<p><a href="<?=$this->url(['Notejam\Controller\User', 'forgotPassword'])?>" class="small-red">Forgot password?</a></p>
		<?=$form->close()?>
	</div>