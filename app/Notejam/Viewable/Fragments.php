<?php
namespace Notejam\Viewable;

class Fragments {
	use \Asgard\Templating\ViewableTrait;

	public function pads($controller, $user) {
		if($user): ?>
			<div class="three columns">
				<h4 id="logo">My pads</h4>
				<nav>
					<ul>
						<?php foreach($user->pads()->get() as $pad): ?>
						<li><a href="<?=$pad->url()?>"><?=$pad?></a></li>
						<?php endforeach ?>
					</ul>
					<hr />
					<a href="<?=$controller->getContainer()['resolver']->url(['Notejam\Controllers\PadController', 'create'])?>">New pad</a>
				</nav>
			</div>
		<?php endif;
	}

	public function notes($request, $orm) {
		$url = $request->url;
		$dir = $request->get->get('dir', 'asc');

		if($request->get['sort'] == 'note') {
			if($dir === 'desc')
				$orm->orderBy('name DESC');
			else
				$orm->orderBy('name ASC');
		}
		elseif($request->get['sort'] == 'note') {
			if($dir === 'desc')
				$orm->orderBy('updated_at DESC');
			else
				$orm->orderBy('updated_at ASC');
		}
		$notes = $orm->get();
		?>
		<table class="notes">
			<tr>
				<th class="note">Note <a href="<?=$url->full(['sort'=>'note', 'dir'=>'asc'])?>" class="sort_arrow" >&uarr;</a><a href="<?=$url->full(['sort'=>'note', 'dir'=>'desc'])?>" class="sort_arrow" >&darr;</a></th>
				<th>Pad</th>
				<th class="date">Last modified <a href="<?=$url->full(['sort'=>'date', 'dir'=>'asc'])?>" class="sort_arrow" >&uarr;</a><a href="<?=$url->full(['sort'=>'date', 'dir'=>'desc'])?>" class="sort_arrow" >&darr;</a></th>
			</tr>
			<?php foreach($notes as $note): ?>
			<tr>
				<td><a href="<?=$note->url()?>"><?=$note?></a></td>
				<td class="pad"><?=($note->pad ? '<a href="'.$note->pad->url().'">'.$note->pad.'</a>':'No pad')?></td>
				<td class="hidden-text date"><?php
				if($note->updated_at->isToday())
				echo 'Today at '.$note->updated_at->format('H:i');
				elseif($note->updated_at->isYesterday())
				echo 'Yesterday';
				elseif(($days = -$note->updated_at->diffInDays(new \DateTime('now'))) <= 7)
				echo $days.' days ago';
				else
				echo $note->updated_at->format('d M. Y');
				?></td>
			</tr>
			<?php endforeach ?>
		</table>
		<?php
	}
}