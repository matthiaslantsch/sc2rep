<nav class="navbar navbar-default">
  <div id="mainContainer" class="container">
    <div class="row statsRow">
      <div class="col-md-5">
        <div class="media">
          <div class="media-left media-top">
            <a href="<?=linkTo("player/{$player->id}")?>">
              <div class="portrait_border"
                style="background: url('http://eu.battle.net/sc2/static/images/profile/portrait-summary-<?=strtolower($player->currentLeague)?>.png')
                  no-repeat 50% 50%;">
                <div <?=$player->portrait?>></div>
              </div>
            </a>
          </div>
          <div class="media-body centered">
            <h3 id="name" class="media-heading filter-on" data-tag-key="player1" data-tag-value="<?=$player->id?>"><?=htmlspecialchars($player->fullname)?></h3>
            <?php if(isset($player->curLeague)): ?>
              <div class="media nomargin">
                <div class="media-left media-middle">
                  <img src="<?=linkTo("public/gfx/{$player->currentLeague}.png")?>" alt="<?=$player->currentLeague?>">
                </div>
                <div class="media-body centered">
                  <h3>1v1</h3>
                </div>
              </div>
            <?php endif; ?>
            <a href="<?=$player->url?>">battle.net</a>
          </div>
        </div>
      </div>
      <div class="col-md-1">
        <?php foreach($profileData["races"] as $race => $count): ?>
          <div class="media pointer" help="<?=$player->name?> played <?=$count?> matche(s) as <?=$race?>">
            <div class="media-left media-middle">
              <img src="<?=linkTo("public/gfx/{$race}-struct.png")?>" alt="<?=$race?>">
            </div>
            <div class="media-body centered">
              <?=$count?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="col-md-6">
        <table class="table dataRow">
          <thead>
            <tr>
              <td><h3><strong><?=floor($profileData["spendingSkill"])?></strong></h3></td>
              <td><h3><strong><?=floor($profileData["apm"])?></strong></h3></td>
              <td><h3><strong><?=floor($profileData["winrate"])?>%</strong></h3></td>
              <td><h3><strong><?=floor($profileData["matchCount"])?></strong></h3></td>
              <td><h3><strong><?=constructTimeString($profileData["totalTime"])?></strong></h3></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>SPENDING SKILL</td>
              <td>APM</td>
              <td>WINRATE</td>
              <td>MATCHES PLAYED</td>
              <td>TIME PLAYED</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</nav>
<div class="container">
  <input id="sampler" type="hidden" value="10">
  <div class="row">
    <div class="col-sm-4">
      <div id="apmChart" class="chart threeChart"></div>
      <div class="legendRow">
        <span class="pull-left">
          <strong id="apmAvg">--</strong> APM
        </span>
        <span class="pull-right">
          <strong id="apmRecent">--</strong> RECENT
        </span>
      </div>
    </div>
    <div class="col-sm-4">
      <div id="winRateChart" class="chart threeChart"></div>
      <div class="legendRow">
        <span class="pull-left">
          <strong id="winRateAvg">--</strong> WINRATE
        </span>
        <span class="pull-right">
          <strong id="winRateRecent">--</strong> RECENT
        </span>
      </div>
    </div>
    <div class="col-sm-4">
      <div id="spendingChart" class="chart threeChart"></div>
      <div class="legendRow">
        <span class="pull-left">
          <strong id="spendingAvg">--</strong> SPENDING
        </span>
        <span class="pull-right">
          <strong id="spendingRecent">--</strong> RECENT
        </span>
      </div>
    </div>
  </div>
  <hr>
  <div class="row statsRow">
    <div class="col-md-3">
      <input class="filterTf form-control" type="text" placeholder="any map" id="mapInput">
    </div>
    <div class="col-md-3">
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
    </div>
    <div class="col-md-2">
      <div class="tag dataColumn">vs</div>
    </div>
    <div class="col-md-3">
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
    </div>
    <div class="col-md-1">
      <div class="pointer filter filter-on" data-tag-key="isLadder" data-tag-value="true">
        <strong>LADDER</strong>
      </div>
    </div>
  </div>
  <div class="row statsRow">
    <div class="col-md-3">
      <select class="form-control" id="seasonSelect">
        <option value="" selected>any season...</option>
        <?php foreach($seasons as $season): ?>
          <option value="<?=$season->id?>"><?=$season->name?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-8">
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
    </div>
    <div class="col-md-1">
      <div class="pointer filter filter-on" data-tag-key="isVSAi" data-tag-value="false">
        <strong>not vs AI</strong>
      </div>
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
        <td><strong>LENGTH</strong></td>
        <td><strong>PLAYED</strong></td>
        <td><strong>APM</strong></td>
        <td><strong>SPENDING SKILL</strong></td>
        <td><strong>RESULT</strong></td>
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
<script>
  function dispatch() {
    loadProfileData(<?=$player->id?>);
  }
</script>
<script src="<?=linkJs("filter")?>"></script>
<script>
  initCharts();
</script>