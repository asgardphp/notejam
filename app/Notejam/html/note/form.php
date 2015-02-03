    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <?=$form->open(['attrs'=>['class'=>'note']])?>
        <?=$form['name']->labelTag()?>
        <?=$form['name']->def()?>
        <?=($error=$form['name']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['text']->labelTag('Text')?>
        <?=$form['text']->textarea()?>
        <?=($error=$form['text']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form['pad']->labelTag('Select Pad', 'list')?>
        <?=$form['pad']->def(['id'=>'list'])?>
        <?=($error=$form['pad']->error()) ? '<ul class="errorlist"><li>'.$error.'</li></ul>':''?>
        <?=$form->submit('Save')?>
      <?=$form->close()?>
    </div>