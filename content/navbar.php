<div class="navbar-xs">
  <div class="navbar navbar-default navbar-xs navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="index.php?p=main" class="navbar-brand">Night Sky</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">
          <?php

          if (Page::startsWith($p,"main")) {
             echo '<li class="active"><a href="index.php?p=main">Home</a></li>';
          } else {
            echo '<li><a href="index.php?p=main">Home</a></li>';
          }

          if (Page::startsWith($p,"group")) {
             echo '<li class="active"><a href="index.php?p=group">Groups</a></li>';
          } else {
            echo '<li><a href="index.php?p=group">Groups</a></li>';
          }

          if (Page::startsWith($p,"contact")) {
             echo '<li class="active"><a href="index.php?p=contact">Contacts</a></li>';
          } else {
            echo '<li><a href="index.php?p=contact">Contacts</a></li>';
          }

          if (Page::startsWith($p,"webhook")) {
             echo '<li class="active"><a href="index.php?p=webhook">WebHooks</a></li>';
          } else {
            echo '<li><a href="index.php?p=webhook">WebHooks</a></li>';
          }

          if (Page::startsWith($p,"status-page")) {
             echo '<li class="active"><a href="index.php?p=status-page">StatusPages</a></li>';
          } else {
            echo '<li><a href="index.php?p=status-page">StatusPages</a></li>';
          }

           ?>
        </ul>

        <ul class="nav navbar-nav navbar-right">
          <?php

          if (Page::startsWith($p,"status")) {
             echo '<li class="active"><a href="index.php?p=status">Status</a></li>';
          } else {
            echo '<li><a href="index.php?p=status">Status</a></li>';
          }

          if (Page::startsWith($p,"account")) {
             echo '<li class="active"><a href="index.php?p=account">Account</a></li>';
          } else {
            echo '<li><a href="index.php?p=account">Account</a></li>';
          }

           ?>
          <li><a href="index.php?p=logout">Logout</a></li>
        </ul>

      </div>
    </div>
  </div>
</div>
