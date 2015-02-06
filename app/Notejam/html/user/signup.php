    <div class="thirteen columns content-area">
      <?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
        <?=$form['email']->labelTag()?>
        <?=$form['email']->def()?>
        <?=($error=$form['email']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['password']->labelTag()?>
        <?=$form['password']->password()?>
        <?=($error=$form['password']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['confirm']->labelTag('Confirm password')?>
        <?=$form['confirm']->password()?>
        <?=($error=$form['confirm']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form->submit('Sign Up')?> or <a href="<?=$this->url(['Notejam\Controllers\UserController', 'signin'])?>">Sign in</a>
      <?=$form->close()?>
    </div>