    <div class="sixteen columns content-area">
      <?php if($success): ?>
      <div class="alert-area">
        <div class="alert alert-success">Your new password was sent to your email address.</div>
      </div>
      <?php endif ?>
      <?=$form->open(['attrs'=>['class'=>'offset-by-six sign-in']])?>
        <?=$form['email']->labelTag()?>
        <?=$form['email']->def()?>
        <?=($error=$form['email']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form->submit('Generate password')?>
      <?=$form->close()?>
    </div>