    <div class="sixteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
        <?=$form['email']->labelTag()?>
        <?=$form['email']->def()?>
        <?=($error=$form['email']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form->submit('Generate password')?>
      <?=$form->close()?>
    </div>