    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <?=\Notejam\Viewable\Fragments::sFragment('notes', [$request, $user->notes()])?>
      <a href="<?=$this->url(['Notejam\Controllers\NoteController', 'create'])?>" class="button">New note</a>
    </div>