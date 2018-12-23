<div id="mainContainer" class="container">
	<?php if(isset($tag)): ?>
		<h2 class="filter-on" data-tag-key="other" data-tag-value="<?=$tag->name?>">
			SC2REP Starcraft II matches - <?=ucfirst($tag->group)?> - <?=$tag->name?>:
		</h2>
	<?php else: ?>
		<h2>SC2REP Starcraft II matches:</h2>
	<?php endif; ?>
	<hr>
	<div class="row statsRow">
		<div class="col-md-2 pointer">
			<?php if(!isset($tag) || $tag->group != "map"): ?>
				<input class="filterTf form-control" type="text" placeholder="any map" id="mapInput">
			<?php else: ?>
				<input class="filterTf form-control" disabled type="text" placeholder="any map" id="mapInput" value="<?=$tag->name?>">
			<?php endif; ?>
		</div>
		<div class="col-md-2">
			<?php if(!isset($tag) || $tag->group != "matchup"): ?>
				<div class="row">
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team1" data-tag-value="P">
						<img src="<?=linkTo("public/gfx/Protoss-struct.png")?>" alt="Protoss">
					</div>
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team1" data-tag-value="T">
						<img src="<?=linkTo("public/gfx/Terran-struct.png")?>" alt="Terran">
					</div>
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team1" data-tag-value="Z">
						<img src="<?=linkTo("public/gfx/Zerg-struct.png")?>" alt="Zerg">
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-1">
			<?php if(!isset($tag) || $tag->group != "matchup"): ?>
				<div class="tag dataColumn">vs</div>
			<?php endif; ?>
		</div>
		<div class="col-md-2">
			<?php if(!isset($tag) || $tag->group != "matchup"): ?>
				<div class="row">
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team2" data-tag-value="Z">
						<img src="<?=linkTo("public/gfx/Zerg-struct.png")?>" alt="Zerg">
					</div>
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team2" data-tag-value="T">
						<img src="<?=linkTo("public/gfx/Terran-struct.png")?>" alt="Terran">
					</div>
					<div class="col-md-4 pointer filter filter-off" data-tag-key="team2" data-tag-value="P">
						<img src="<?=linkTo("public/gfx/Protoss-struct.png")?>" alt="Protoss">
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
			<?php if(!isset($tag) || $tag->group != "gametype"): ?>
				<div class="row">
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="1v1">
						<strong>1v1</strong>
					</div>
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="2v2">
						<strong>2v2</strong>
					</div>
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="3v3">
						<strong>3v3</strong>
					</div>
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="4v4">
						<strong>4v4</strong>
					</div>
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="FFA">
						<strong>FFA</strong>
					</div>
					<div class="col-md-2 pointer filter filter-off" data-tag-key="matchup" data-tag-value="Archon">
						<strong>Archon</strong>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-1">
			<div class="pointer filter filter-on" data-tag-key="isLadder" data-tag-value="true">
				<strong>LADDER</strong>
			</div>
		</div>
	</div>
	<div class="row statsRow">
		<div class="col-md-2">
			<?php if(!isset($tag) || $tag->group != "season"): ?>
				<select class="form-control" id="seasonSelect">
					<option value="" selected>any season...</option>
					<?php foreach($seasons as $season): ?>
						<option value="<?=$season->id?>"><?=$season->name?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>
		<div class="col-md-2">
			<input type="text" class="playerField filterTf form-control" placeholder="any player" id="player1">
		</div>
		<div class="col-md-1">
			<div class="tag dataColumn">vs</div>
		</div>
		<div class="col-md-2">
			<input type="text" class="playerField filterTf form-control" placeholder="any player" id="player2">
		</div>
		<div class="col-md-4">
			<?php if(!isset($tag) || $tag->group != "league"): ?>
				<div class="row">
					<div class="col-md-1 col-md-offset-1 pointer filter filter-off" data-tag-key="league" data-tag-value="bronze">
						<img class="filterIcon" src="<?=linkTo("public/gfx/bronze-32.png")?>" alt="Bronze">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="silver">
						<img class="filterIcon" src="<?=linkTo("public/gfx/silver-32.png")?>" alt="Silver">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="gold">
						<img class="filterIcon" src="<?=linkTo("public/gfx/gold-32.png")?>" alt="Gold">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="platinum">
						<img class="filterIcon" src="<?=linkTo("public/gfx/platinum-32.png")?>" alt="Platinum">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="diamond">
						<img class="filterIcon" src="<?=linkTo("public/gfx/diamond-32.png")?>" alt="Diamond">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="master">
						<img class="filterIcon" src="<?=linkTo("public/gfx/master-32.png")?>" alt="Master">
					</div>
					<div class="col-md-1 pointer filter filter-off" data-tag-key="league" data-tag-value="grandmaster">
						<img class="filterIcon" src="<?=linkTo("public/gfx/grandmaster-32.png")?>" alt="Grandmaster">
					</div>
					<div class="col-md-4 statsRow">
						<?php if(!isset($tag) || $tag->group != "player"): ?>
							<div class="pointer filter filter-off" data-tag-key="proGames" data-tag-value="true">
								<strong>PRO GAMES</strong>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="row">
					<div class="col-md-4 col-md-offset-7 statsRow">
						<?php if(!isset($tag) || $tag->group != "player"): ?>
							<div class="pointer filter filter-off" data-tag-key="proGames" data-tag-value="true">
								<strong>PRO GAMES</strong>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-1">
			<?php if(!isset($tag) || $tag->name != "vs AI"): ?>
				<div class="pointer filter filter-on" data-tag-key="isVSAi" data-tag-value="false">
					<strong>not vs AI</strong>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<hr>
	<table class="table">
		<thead>
			<tr>
				<td><strong>#</strong></td>
				<td><strong>MAP</strong></td>
				<td><strong>TYPE</strong></td>
				<td><strong>MATCHUP</strong></td>
				<td><strong>PLAYERS</strong></td>
				<td><strong>vs PLAYERS</strong></td>
				<td><strong>LEAGUES</strong></td>
				<td><strong>LENGTH</strong></td>
				<td><strong>PLAYED</strong></td>
			</tr>
		</thead>
		<tbody id="tableContent"></tbody>
	</table>
	<div class="row">
		<div class="pull-right">
			<input type="hidden" id="pager" value="1">
			<nav>
				<ul class="pagination pagination-sm">
				<li>
					<a id="prevBtn" ref="#" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<li>
					<a id="nextBtn" href="#" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
				</ul>
			</nav>
		</div>
	</div>
</div>
<script src="<?=linkJs("filter")?>"></script>
<script>
	function dispatch() {
		loadMatches();
	}
</script>