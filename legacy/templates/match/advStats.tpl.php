<?php
	$advStats = json_decode($dataPack, true);
	$matchdetails = json_decode($match->loadDataPack("details"), true);
	$buildArr = $matchdetails["players"][$perf->sid]["composition"];
	usort($buildArr, function($a, $b) {
		return $a["started"] - $b["started"];
	});
	$build = \HIS5\sc2rep\models\BuildModel::fromArray($buildArr, "sc2rep match {$match->id}");
?>
<h3><?=$perf->player->name?></h3>
<div id="advtabs_<?=$perf->sid?>" class="tabs">
	<ul>
		<li><a href="#tabs-1">Unit Statistics</a></li>
		<li><a href="#tabs-2">Ability Statistics</a></li>
		<li><a href="#tabs-3">Buildorder</a></li>
	</ul>
	<div id="tabs-1">
		<h4>Unit statistics</h4>
		<table class="table">
			<thead>
				<tr>
					<td><strong>Unit</strong></td>
					<td><strong>Produced</strong></td>
					<td><strong>Lost</strong></td>
					<td><strong>Kills</strong></td>
					<td><strong>Max Kills</strong></td>
					<td><strong>% Lost</strong></td>
					<td><strong>Average Life Span</strong></td>
					<td><strong>Shortest Life Span</strong></td>
					<td><strong>Longest Life Span</strong></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($advStats["unitStatistics"] as $name => $stats): ?>
					<tr>
						<td><div class="unit_icons unit_icons_smaller <?=strtolower($name)?>_smaller"><span></span></div></td>
						<td><?=$stats["produced"]?></td>
						<td><?=$stats["lost"]?></td>
						<td><?=$stats["killed"]?></td>
						<td><?=$stats["maxKills"]?></td>
						<td><?=$stats["percentLost"]?> %</td>
						<td><?=transformToTimestring($stats["avgLifetime"])?></td>
						<td><?=transformToTimestring($stats["shortestLifeTime"])?></td>
						<td><?=transformToTimestring($stats["longestLifeTime"])?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div id="tabs-2">
		<h4>Ability statistics</h4>
		<table class="table">
			<thead>
				<tr>
					<td><strong>Ability</strong></td>
					<td><strong>Used</strong></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($advStats["abilities"] as $ability => $count): ?>
					<tr>
						<td><?=$ability?></td>
						<td><?=$count?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div id="tabs-3">
		<h4>Buildorder</h4>
		<div class="row">
			<div class="col-md-6">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-primary active buildorderBtn" data-hide-type="worker">
						<input type="checkbox" checked autocomplete="off"> Hide workers
					</label>
					<label class="btn btn-primary buildorderBtn" data-hide-type="army">
						<input type="checkbox" autocomplete="off"> Hide army
					</label>
					<label class="btn btn-primary buildorderBtn" data-hide-type="struct">
						<input type="checkbox" autocomplete="off"> Hide structures
					</label>
					<label class="btn btn-primary buildorderBtn" data-hide-type="upgrade">
						<input type="checkbox" autocomplete="off"> Hide upgrades
					</label>
				</div>
			</div>
			<div class="col-md-6">
				<div class="input-group">
					<input type="text" id="copyFrom" value="<?=htmlspecialchars($build->toSALT())?>"
						class="form-control" placeholder="salt build">
					<span class="btn btn-primary active input-group-addon copyButton"
						help="<?=htmlspecialchars("<h4>Want to try this build on your own?</h4>")?>
						SALT is a Starcraft II training tool which will guide you through the build order step
						by step with a build order list and audio instruction. Get valuable practice for your time and learn to match the timings of your favorite pro players!
						<?=htmlspecialchars("<br><br>")?>
						Find SALT in the custom games section of any Bnet server. Once in game go into menu->build orders->import and paste this text.
						<?=htmlspecialchars("<br><br><i>Note that due to size limitations, all workers are removed. Additionally,
							only the first 3 supply buildings are included, and only the first 10 of any other unit are included.</i>")?>">Copy SALT encoding</span>
				</div>
			</div>
		</div>
		<table class="table">
			<thead>
				<tr>
					<td><strong>Unit</strong></td>
					<td><strong>Supply</strong></td>
					<td><strong>Started</strong></td>
					<td><strong>Finished</strong></td>
					<td><strong>Killed</strong></td>
				</tr>
			</thead>
		</table>
		<div id="buildorderTable">
			<table class="table">
				<tbody>
					<?php foreach($buildArr as $i => $entry): ?>
						<?php if($entry["spawned"] > 0): ?>
							<tr class="<?=str_replace("morph-", "", $entry["type"])?>" <?=($entry["type"] == "worker" ? 'style="display:none"' : '') ?>>
								<td>
									<?php if($entry["type"] == "army" || $entry["type"] == "worker" || $entry["type"] == "morph-army"): ?>
										<div class="unit_icons unit_icons_smaller <?=strtolower($entry["name"])?>_smaller"><span></span></div>
									<?php elseif($entry["type"] == "struct" || $entry["type"] == "morph-struct"): ?>
										<div class="buildings_icons buildings_icons_smaller <?=strtolower($entry["name"])?>_smaller"><span></span></div>
									<?php else: ?>
										<div class="upgrade_icons <?=strtolower($entry["name"])?>"><span><?=(isset($entry["level"]) ? $entry["level"] : "")?></span></div>
									<?php endif; ?>
								</td>
								<td>
									<?php if(isset($entry["foodMade"]) && isset($entry["foodUsed"])): ?>
										<span><?=$entry["foodUsed"]."/".$entry["foodMade"]?></span>
									<?php else: ?>
										<span>-</span>
									<?php endif; ?>
								</td>
								<td><?=transformToTimestring($entry["started"])?></td>
								<td><?=transformToTimestring($entry["spawned"])?></td>
								<td><?=isset($entry["died"]) ? transformToTimestring($entry["died"]) : ""?></td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>initAdvancedTab(<?=$perf->sid?>)</script>