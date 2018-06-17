<div id="mainContainer" class="container">
  <div class="page-header">
    <h1><?=$match->getTitle()?></h1>
    <input id="idMatch" type="hidden" value="<?=$match->id?>">
  </div>
  <div class="row">
    <div class="col-sm-10">
      <input type="hidden" id="mapField" value="<?=$match->tags["map"]->map->identifier?>"
        data-sizeX="<?=$match->tags["map"]->map->sizeX?>"
        data-sizeY="<?=$match->tags["map"]->map->sizeY?>">
      <h2>Map: <a target="_blank" href="//wiki.teamliquid.net/starcraft2/<?=$match->tags["map"]?>"><?=$match->tags["map"]?></a></h2>
      <h2>Players:</h2>
      <div id="playerArea" class="row">
        <?php foreach($match->getTeams() as $team): ?>
          <div class="col-sm-6">
            <?php foreach($team as $pl): ?>
              <div class="media">
                <div class="media-left media-top">
                  <?php if($pl->player->bnet != 0): ?>
                    <a href="<?=linkTo("player/{$pl->player->id}")?>">
                      <div class="portrait_border"
                        style="background: url('http://eu.battle.net/sc2/static/images/profile/portrait-summary-<?=strtolower($pl->currentLeague)?>.png')
                          no-repeat 50% 50%;">
                        <div <?=$pl->player->portrait?>></div>
                      </div>
                    </a>
                  <?php else: ?>
                    <div class="portrait_border"></div>
                  <?php endif; ?>
                </div>
                <div class="media-body centered">
                  <div>
                    <?php if($pl->player->bnet != 0): ?>
                      <a href="<?=linkTo("player/{$pl->player->id}")?>">
                        <strong id="name_<?=$pl->sid?>"><?=htmlspecialchars("{$pl->player->fullname}")?></strong>
                      </a>
                      <span><?=$pl->isWin ? '<span class="glyphicon glyphicon-star-empty" title="Winner"></span>' : "" ?></span>
                    <?php else: ?>
                      <strong id="name_<?=$pl->sid?>"><?=htmlspecialchars("{$pl->player->name}")?></strong>
                    <?php endif; ?>
                  </div>
                  <?php if($pl->points !== null): ?>
                    <div>Points: <?=$pl->points?></div>
                  <?php endif; ?>
                  <?php if($pl->divisionRank !== null): ?>
                    <div>Rank: <?=$pl->divisionRank?></div>
                  <?php endif; ?>
                  <?php if($pl->leagueRank !== null): ?>
                    <div>League Rank: <?=$pl->leagueRank?></div>
                  <?php endif; ?>
                  <?php if($pl->serverRank !== null): ?>
                    <div>Region Rank: <?=$pl->serverRank?></div>
                  <?php endif; ?>
                  <?php if($pl->globalRank !== null): ?>
                    <div>Global Rank: <?=$pl->globalRank?></div>
                  <?php endif; ?>
                  <?php if($pl->winrate !== null): ?>
                    <div>Winrate: <?=$pl->winrate?>%</div>
                  <?php endif; ?>
                  <?php if($pl->player->bnet != 0): ?>
                    <div><a href="<?=$pl->player->url?>">Battle.net Profile</a></div>
                  <?php endif; ?>
                </div>
                <div class="media-right media-middle">
                  <img src="<?=linkTo("public/gfx/{$pl->pickedRace}-icon.png")?>" alt="<?=$pl->pickedRace?>">
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <h3>Played: <?=date("j M o g:i a", strtotime($match->played))?>, a <?=constructTimeString($match->length)?> game</h3>
      <hr>
      <h2>Performance:</h2>
        <table class="table">
        <thead>
          <tr>
            <td>Player</td>
            <td class="dataColumn pointer" help="APM, or better known as <b>A</b>ction <b>p</b>er <b>M</b>inute can be used as a 
              basic tool to determine how good the multitasking ability of a player is">
                APM
            </td>
            <td class="dataColumn pointer" help="AUR, or better known as <b>a</b>verage <b>u</b>nspent <b>r</b>essources is the average of floated ressources in a game">
              Unspent ressources (AUR)
            </td>
            <td class="dataColumn pointer" help="RCR, or better known as <b>r</b>essource <b>c</b>ollection <b>r</b>ate is the average income rate of a player during a game">
              Income (RCR)
            </td>
            <td class="dataColumn pointer" help="Spending quotient (generally referred to as Spending skill) is a method to quantitatively assess a player’s economic management in a game.
              It calculates the relationship between unspent ressources and income in order to quantify how efficient a player spends his money <br><i>Click to read more</i>">
                <a target="_blank" href="//wiki.teamliquid.net/starcraft2/Spending_quotient">Spending skill (SQ)</a>
            </td>
        </thead>
        <tbody>
          <?php foreach($match->performances as $perf): ?>
            <tr>
              <td>
                <?php if($perf->player->bnet != 0): ?>
                  <a href="<?=linkTo("player/{$pl->player->id}")?>">
                    <strong><?=htmlspecialchars("{$perf->player->fullname}")?></strong>
                  </a>
                <?php else: ?>
                    <strong><?=htmlspecialchars("{$perf->player->fullname}")?></strong>
                <?php endif; ?>
              </td>
              <td class="dataColumn"><?=(is_null($perf->APM) ? "-" : $perf->APM)?></td>
              <td class="dataColumn"><?=(is_null($perf->AU) ? "-" : $perf->AU)?></td>
              <td class="dataColumn"><?=(is_null($perf->RCR) ? "-" : $perf->RCR)?></td>
              <td class="dataColumn"><?=(is_null($perf->SQ) ? "-" : $perf->SQ)?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <hr>
      <h2>Economy/Saturation:</h2>
      <div id="oldChartArea" data-players="<?=htmlspecialchars(json_encode($playersJson))?>" class="row">
        <div id="baseTimingChart" class="col-sm-4 oldchart"></div>
        <div id="saturationTimingChart" class="col-sm-5 oldchart"></div>
        <div id="workersBuilt" class="col-sm-3 oldchart"></div>
      </div>
    </div>
    <div class="col-sm-2">
      <a href="<?=linkTo("download/{$match->id}")?>"><button class="btn btn-success">Download</button></a>
      <h2>Tags</h2>
      <?php foreach($match->tags as $group => $tag): ?>
        <h4><?=ucfirst($group)?></h4>
        <?php if(is_array($tag)): ?>
          <?php foreach($tag as $tg): ?>
            <a class="tag" href="<?=linkTo("tag/{$tg->id}")?>"><?=$tg->name?></a>
          <?php endforeach; ?>
        <?php else: ?>
          <a class="tag" href="<?=linkTo("tag/{$tag->id}")?>"><?=$tag->name?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <hr>
  <div class="row">
    <h2 class="pointer">Details <div class="pull-right"><span id="detailsBtn" class="glyphicon glyphicon-menu-down"></span></div></h2>
  </div>
  <hr>
</div>
<div id="detailsArea" class="collapse">
  <div class="container">
    <h2>Army value/Composition:</h2>
    <div id="armyValChart" class="chart"></div>
  </div>
  <div id="statsBar" class="container-fluid">
    <div class="row statsRow">
      <?php foreach($match->getTeams() as $i => $team): ?>
        <?php if($i % 2 != 0): ?>
          <div class="col-sm-5">
            <?php foreach($team as $pl): ?>
              <div class="row playerRow statsRow">
                <div class="col-sm-3">
                  <span>
                    <strong><?=htmlspecialchars("{$pl->player->fullname}")?></strong>
                  </span>
                </div>
                <div class="col-sm-9">
                  <div class="row topBorder dataRow statsRow">
                    <div class="col-xs-3">Army:</div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="armyMins_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/min.png")?>" alt="Minerals">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="armyGas_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-gas.png")?>" alt="Vespene gas">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div id="comp_<?=$pl->sid?>" class="comp">
                        <button class="btn" type="button">Details</button>
                      </div>
                    </div>
                  </div>
                  <div class="row bottomBorder dataRow">
                    <div class="col-xs-3">Bank:</div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="mins_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/min.png")?>" alt="Minerals">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="gas_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-gas.png")?>" alt="Vespene gas">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="supply_<?=$pl->sid?>" class="media-body">---</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-food.png")?>" alt="Supply">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="col-sm-2">
            <span id="timeField" data-time="-1" class="tag">--:--</span>
          </div>
        <?php else: ?>
          <div class="col-sm-5">
            <?php foreach($team as $pl): ?>
              <div class="row playerRow statsRow">
                <div class="col-sm-9">
                  <div class="row topBorder dataRow statsRow">
                    <div class="col-xs-3">Army:</div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="armyMins_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/min.png")?>" alt="Minerals">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="armyGas_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-gas.png")?>" alt="Vespene gas">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div id="comp_<?=$pl->sid?>" class="comp">
                        <button class="btn" type="button">Details</button>
                      </div>
                    </div>
                  </div>
                  <div class="row bottomBorder dataRow">
                    <div class="col-xs-3">Bank:</div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="mins_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/min.png")?>" alt="Minerals">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="gas_<?=$pl->sid?>" class="media-body">----</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-gas.png")?>" alt="Vespene gas">
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-3">
                      <div class="media">
                        <div id="supply_<?=$pl->sid?>" class="media-body">---</div>
                        <div class="media-right media-middle">
                          <img class="media-object" src="<?=linkTo("public/gfx/{$pl->playedRace}-food.png")?>" alt="Supply">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <span>
                    <strong><?=htmlspecialchars("{$pl->player->fullname}")?></strong>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="container">
    <div id="tabs" class="tabs">
      <ul>
        <li><a href="#tabs-1">Overview/Multitasking</a></li>
        <li><a href="#tabs-2">Economy</a></li>
        <li><a href="#tabs-3">Army trading</a></li>
        <li><a href="#tabs-4">Investment</a></li>
        <li><a href="<?=linkTo("loadData/{$match->id}/msg")?>">Chat history</a></li>
        <?php foreach($match->performances as $perf): ?>
          <li>
            <a href="<?=linkTo("loadData/{$match->id}/advStats_{$perf->sid}")?>">
              <?=$perf->player->name?> stats
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <hr>
      <div id="tabs-1">
        <div class="row">
          <div id="apmChart" class="col-sm-4 chart threeChart"></div>
          <div id="structuresGraph" class="col-sm-4 chart threeChart"></div>
          <div id="unitsGraph" class="col-sm-4 chart threeChart"></div>
        </div>
      </div>
      <div id="tabs-2">
        <div class="row">
          <div id="incomeChartMin" class="col-sm-4 chart threeChart"></div>
          <div id="incomeChartGas" class="col-sm-4 chart threeChart"></div>
          <div id="workerCountChart" class="col-sm-4 chart threeChart"></div>
        </div>
      </div>
      <div id="tabs-3">
        <div class="row">
          <div id="resLostChart" class="col-sm-6 chart twoChart"></div>
          <div id="resKilledArmyChart" class="col-sm-6 chart twoChart"></div>
        </div>
      </div>
      <div id="tabs-4">
        <div class="row">
          <div id="spendTechChart" class="col-sm-6 chart twoChart"></div>
          <div id="baseCountChart" class="col-sm-6 chart twoChart"></div>
        </div>
      </div>
    </div>
    <hr>
  </div>
</div>
<script src="<?=linkJs("match")?>"></script>
<script src="<?=linkJs("mychart")?>"></script>