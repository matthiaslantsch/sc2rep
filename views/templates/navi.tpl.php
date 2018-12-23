<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?=linkTo()?>">SC2REP</a>
      <p class="navbar-text">Yet another Starcraft II replay analysis tool</p>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <div class="test"></div>
      <ul class="nav navbar-nav navbar-right">
        <li id="loading"></li>
        <li><a href="<?=linkTo()?>">DASHBOARD</a></li>
        <li><a href="<?=linkTo("matches")?>">MATCHES</a></li>
        <!--
        <li><a href="#">Builds</a></li>
        <li><a href="#">Settings</a></li>
        -->
      </ul>
      <form class="navbar-form navbar-right">
        <input id="searchField" type="text" class="form-control" placeholder="Search for player...">
      </form>
      <form class="navbar-form navbar-right">
        <button id="uploadBtn" type="button" class="btn btn-default btn-success btn-block">
          <span class="glyphicon glyphicon-upload"></span> Upload
        </button>
      </form>
    </div>
    <div class="navbar-center">
      <div class="alertArea"></div>
    </div>
  </div>
</nav>