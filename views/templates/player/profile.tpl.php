<nav class="navbar navbar-default">
  <div id="mainContainer" class="container">
    <div class="row statsRow">
      <div class="col-md-5">
        <div class="media">
          <div class="media-left media-top">
            <a href="<?=linkTo("player/{$player->id}")?>">
              <div class="portrait_border"
                style="background: url('<?=linkTo("gfx/portrait-summary-".strtolower($player->currentLeague).".png")?>')
                  no-repeat 50% 50%;">
                <img src="<?=linkTo("gfx/unknown_portrait.jpg")?>" alt="portrait">
              </div>
            </a>
          </div>
          <div class="media-body centered">
            <h3 id="name" class="media-heading filter-on" data-tag-key="player1" data-tag-value="<?=$player->id?>"><?=htmlspecialchars($player->fullname)?></h3>
            <?php if($player->curLeague !== null): ?>
              <div class="media nomargin">
                <div class="media-left media-middle">
                  <img src="<?=linkTo("gfx/{$player->currentLeague}.png")?>" alt="<?=$player->currentLeague?>">
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
              <img src="<?=linkTo("gfx/{$race}-struct.png")?>" alt="<?=$race?>">
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
              <td><h3><strong><?=\holonet\common\readableDurationString($profileData["totalTime"])?></strong></h3></td>
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
<script src="<?=linkJs("player")?>"></script>
<script>
  initCharts();
</script>
