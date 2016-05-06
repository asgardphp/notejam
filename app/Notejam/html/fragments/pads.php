			<div class="three columns">
				<h4 id="logo">My pads</h4>
				<nav>
					<ul>
						<?php foreach($user->pads() as $pad): ?>
						<li><a href="<?=$pad->url()?>"><?=$pad?></a></li>
						<?php endforeach ?>
					</ul>
					<hr />
					<a href="<?=$controller->url(['Notejam\Controller\Pad', 'create'])?>">New pad</a>
				</nav>
			</div>
