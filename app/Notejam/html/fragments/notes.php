		<?php if($notes->count() === 0): ?>
			<p class="empty">Create your first note.</p>
		<?php else: ?>
		<table class="notes">
			<tr>
				<th class="note">Note <a href="<?=$url->full(['sort'=>'note', 'dir'=>'asc'])?>" class="sort_arrow"<?=($sortDir==='name ASC' ? ' style="color:red"':'')?>>&uarr;</a><a href="<?=$url->full(['sort'=>'note', 'dir'=>'desc'])?>" class="sort_arrow"<?=($sortDir==='name DESC' ? ' style="color:red"':'')?>>&darr;</a></th>
				<th>Pad</th>
				<th class="date">Last modified <a href="<?=$url->full(['sort'=>'date', 'dir'=>'asc'])?>" class="sort_arrow"<?=($sortDir==='updated_at ASC' ? ' style="color:red"':'')?>>&uarr;</a><a href="<?=$url->full(['sort'=>'date', 'dir'=>'desc'])?>" class="sort_arrow"<?=($sortDir==='updated_at DESC' ? ' style="color:red"':'')?>>&darr;</a></th>
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
				elseif(($days = $note->updated_at->diffInDays(new \Asgard\Common\DateTime('now'))) <= 7)
					echo $days.' days ago';
				else
					echo $note->updated_at->format('d M. Y');
				?></td>
			</tr>
			<?php endforeach ?>
		</table>
		<?php endif ?>