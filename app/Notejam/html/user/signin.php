    <div class="sixteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
        <?=$form['email']->labelTag()?>
        <?=$form['email']->def()?>
        <?=$form['password']->labelTag()?>
        <?=$form['password']->password()?>
        <?=$form->submit('Sign In')?> or <a href="<?=$this->getContainer()['resolver']->url(['Notejam\Controllers\UserController', 'signup'])?>">Sign up</a>
        <hr />
        <p><a href="<?=$this->getContainer()['resolver']->url(['Notejam\Controllers\UserController', 'forgotPassword'])?>" class="small-red">Forgot password?</a></p>
      <?=$form->close()?>
    </div>