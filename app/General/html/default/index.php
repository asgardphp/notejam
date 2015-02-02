    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
          <!--<div class="alert-area">-->
        <!--<div class="alert alert-success">Note is sucessfully saved</div>-->
      <!--</div>-->
      <?=\Notejam\Viewable\Fragments::sFragment('notes', [$request, $user->notes()])?>
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\NoteController', 'create'])?>" class="button">New note</a>
    </div>