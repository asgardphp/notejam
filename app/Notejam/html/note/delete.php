	<?=$fragments->fragment('pads', [$this, $user])?>
	<div class="thirteen columns content-area">
		<p>Are you sure you want to delete <?=$note?> note?</p>
		<form action="" method="post" style="display: inline;">
		<input type="submit" class="button red" value="Yes, I want to delete this note">&nbsp;
		</form>
		<a href="javascript:window.history.back()">Cancel</a>
	</div>