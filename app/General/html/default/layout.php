  <div class="container">
    <div class="sixteen columns">
      <div class="sign-in-out-block">
        <?php if($user): ?>
        <?=$user?>:&nbsp; <a href="<?=$controller->url(['Notejam\Controller\User', 'settings'])?>">Account settings</a>&nbsp;&nbsp;&nbsp;<a href="<?=$controller->url(['Notejam\Controller\User', 'signout'])?>">Sign out</a>
        <?php else: ?>
        <a href="<?=$controller->url(['Notejam\Controller\PublicUser', 'signup'])?>">Sign up</a>&nbsp;&nbsp;&nbsp;<a href="<?=$controller->url(['Notejam\Controller\PublicUser', 'signin'])?>">Sign in</a>
        <?php endif ?>
      </div>
    </div>
    <div class="sixteen columns">
      <h1 class="bold-header"><a href="<?=$controller->url(['General\Controller\DefaultController', 'index'])?>" class="header">note<span class="jam">jam:</span></a> <span><?=$controller->getContainer()['html']->getTitle()?></span></h1>
    </div>
    <?=$content ?>
    <hr class="footer" />
    <div class="footer">
      <div>Notejam: <strong>Asgard</strong> application</div>
      <div><a href="https://github.com/komarserjio/notejam">Github</a>, <a href="https://twitter.com/komarserjio">Twitter</a>, created by <a href="https://github.com/komarserjio/">Serhii Komar</a></div>
    </div>
  </div><!-- container -->
  <a href="https://github.com/komarserjio/notejam"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub"></a>