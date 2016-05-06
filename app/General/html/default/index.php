    <?=$fragments->fragment('pads', [$this, $user])?>
    <div class="thirteen columns content-area">
      <?php $this->getFlash()->showAll() ?>
      <?=$fragments->fragment('notes', [$request, $user->notes()])?>
      <a href="<?=$this->url(['Notejam\Controller\Note', 'create'])?>" class="button">New note</a>
    </div>