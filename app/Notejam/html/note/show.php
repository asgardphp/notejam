    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <p class="hidden-text">Last edited <?php
        if($note->updated_at->isToday())
          echo 'at '.$note->updated_at->format('H:i');
        elseif($note->updated_at->isYesterday())
          echo 'yesterday';
        elseif(($days = -$note->updated_at->diffInDays(new \DateTime('now'))) <= 7)
          echo $days.' days ago';
        else
          echo 'on '.$note->updated_at->format('d M. Y');
        ?></p>
      <div class="note">
        <?=$note->text?>
      </div>
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\NoteController', 'edit'], ['note_id'=>$note->id])?>" class="button">Edit</a>
      <a href="<?=$this->container['resolver']->url(['Notejam\Controllers\NoteController', 'delete'], ['note_id'=>$note->id])?>" class="delete-note">Delete it</a>
    </div>