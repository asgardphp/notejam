    <div class="sixteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
        <?=$form['current']->labelTag('Current password')?>
        <?=$form['current']->password()?>
        <?=($error=$form['current']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['new']->labelTag('New password')?>
        <?=$form['new']->password()?>
        <?=($error=$form['new']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['confirm']->labelTag('Confirm new password')?>
        <?=$form['confirm']->password()?>
        <?=$form->submit('Save')?>
      <?=$form->close()?>
    </div>