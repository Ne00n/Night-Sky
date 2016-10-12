<?php

$Login = new Verify($DB);
if ($Login->isLoggedIN()) {

?>

<body>

<div class="navbar-xs">
  <div class="navbar navbar-default navbar-xs navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="../" class="navbar-brand">Bootswatch</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Themes <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="themes">
              <li><a href="../default/">Default</a></li>
              <li class="divider"></li>
              <li><a href="../cerulean/">Cerulean</a></li>
              <li><a href="../cosmo/">Cosmo</a></li>
              <li><a href="../cyborg/">Cyborg</a></li>
              <li><a href="../darkly/">Darkly</a></li>
              <li><a href="../flatly/">Flatly</a></li>
              <li><a href="../journal/">Journal</a></li>
              <li><a href="../lumen/">Lumen</a></li>
              <li><a href="../paper/">Paper</a></li>
              <li><a href="../readable/">Readable</a></li>
              <li><a href="../sandstone/">Sandstone</a></li>
              <li><a href="../simplex/">Simplex</a></li>
              <li><a href="../slate/">Slate</a></li>
              <li><a href="../spacelab/">Spacelab</a></li>
              <li><a href="../superhero/">Superhero</a></li>
              <li><a href="../united/">United</a></li>
              <li><a href="../yeti/">Yeti</a></li>
            </ul>
          </li>
          <li>
            <a href="../help/">Help</a>
          </li>
          <li>
            <a href="http://news.bootswatch.com">Blog</a>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">Sandstone <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="download">
              <li><a href="http://jsfiddle.net/bootswatch/m0nv7a0o/">Open Sandbox</a></li>
              <li class="divider"></li>
              <li><a href="./bootstrap.min.css">bootstrap.min.css</a></li>
              <li><a href="./bootstrap.css">bootstrap.css</a></li>
              <li class="divider"></li>
              <li><a href="./variables.less">variables.less</a></li>
              <li><a href="./bootswatch.less">bootswatch.less</a></li>
              <li class="divider"></li>
              <li><a href="./_variables.scss">_variables.scss</a></li>
              <li><a href="./_bootswatch.scss">_bootswatch.scss</a></li>
            </ul>
          </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
          <li><a href="http://builtwithbootstrap.com/" target="_blank">Built With Bootstrap</a></li>
          <li><a href="https://wrapbootstrap.com/?ref=bsw" target="_blank">WrapBootstrap</a></li>
        </ul>

      </div>
    </div>
  </div>
</div>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">1</th>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
          </tr>
          <tr>
            <th scope="row">2</th>
            <td>Jacob</td>
            <td>Thornton</td>
            <td>@fat</td>
          </tr>
          <tr>
            <th scope="row">3</th>
            <td>Larry</td>
            <td>the Bird</td>
            <td>@twitter</td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
