    <?=\Notejam\Viewable\Fragments::sFragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <p class="empty">Create your first note.</p>
      <a href="#" class="button">New note</a>
    </div>