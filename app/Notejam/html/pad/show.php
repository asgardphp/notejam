    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <!--<div class="alert-area">-->
        <!--<div class="alert alert-success">Note is sucessfully saved</div>-->
      <!--</div>-->
      <?=\Notejam\Viewable\Fragments::sFragment('notes', [$request, $pad->notes()])?>
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\NoteController', 'create'])?>" class="button">New note</a>&nbsp;
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\PadController', 'edit'], ['pad_id'=>$pad->id])?>" class="button">Edit pad</a>&nbsp;
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\PadController', 'delete'], ['pad_id'=>$pad->id])?>" class="button">Delete pad</a>
    </div>