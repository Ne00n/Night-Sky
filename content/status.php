<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">
      <div class="col-md-6 col-md-offset-3">
        <div class="table-responsive table-hover">
          <table class="table">
          <thead>
            <tr>
              <th>Location</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>

          <?php

          $USER_ID = $Login->getUserID();

          $query = "SELECT Location,Online FROM remote";
          $stmt = $DB->GetConnection()->prepare($query);
          $stmt->execute();
          $result = $stmt->get_result();
          while ($row = $result->fetch_assoc()) {
            echo '<tr class="'.($row['Online'] ? 'success' : 'danger').'">';
            echo '<td class="text-left">'.Page::escape($row['Location']).'</td>';
            echo '<td class="text-left">'.($row['Online'] ? 'Online' : 'Offline').'</td>';
            echo '</tr>';
          } ?>

          </tbody>
        </table>
      </div>
      </div>
      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
