<table class="table">
	<?php 
		$messages = json_decode($dataPack, true);
		ksort($messages);
		foreach($messages as $i => $msg):
	?>
		<tr>
			<td><?=transformToTimestring($msg["time"])?></td>
			<td colspan="3"><?=$msg["user"]?></td>
			<?php if($msg["type"] == "PingEvent"): ?>
				<td colspan="6">pinged <?=$msg["loc"]["x"]." / ".$msg["loc"]["y"]?></td>
			<?php else: ?>
				<td><?=$msg["rec"]?></td>
				<td colspan="5"><?=$msg["msg"]?></td>
			<?php endif; ?>
	<?php endforeach; ?>
</table>