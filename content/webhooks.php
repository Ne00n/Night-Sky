<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="row aligned-row">
          <div class="col-md-6 text-left">
            <h3>Webhoooks</h3>
          </div>
          <div class="col-md-6 text-right">
            <a href="index.php?p=webhook?add"><button class="btn btn-primary btn-xs"  type="button"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
          </div>
        </div>
      <?php

      $WH = new WebHook($DB,$Login);

      if (Page::startsWith($p,"webhook?remove=")) {

      $webhookID = str_replace("webhook?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->setID($webhookID);
            $WH->removeHook();
            if ($WH->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
            }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this WebHook?</p>

          <form class="form-horizontal" action="index.php?p=webhook?remove=<?= Page::escape($webhookID) ?>" method="post">
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=contact"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>

          <?php
        }
      }

     if (Page::startsWith($p,"webhook?edit=")) {

       $webhookID = str_replace("webhook?edit=", "", $p);

       $WH->setID($webhookID);
       $WH->getData();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->editHook($_POST['name'],$_POST['method'],$_POST['urlDown'],$_POST['jsonDown'],$_POST['headersDown'],$_POST['urlUp'],$_POST['jsonUp'],$_POST['headersUp'],$_POST['groups']);
             if ($WH->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        }
        $WH->getData();
         ?>

        <form class="form-horizontal" action="index.php?p=webhook?edit=<?php echo Page::escape($webhookID); ?>" method="post">
            <p>What should happen, when the server goes dark?</p>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-external-link"></span>
                 </div>
                  <input value="<?php echo Page::escape($WH->getUrlDown()); ?>" type="text" class="form-control input-sm" name="urlDown">
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-file-code-o"></span>
                 </div>
                  <textarea type="text" class="form-control input-sm" name="jsonDown"><?php echo Page::escape($WH->getJsonDown()); ?></textarea>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-file-text-o"></span>
                 </div>
                  <textarea type="text" class="form-control input-sm" name="headersDown" rows="3"><?php echo Page::escape($WH->getHeadersDown()); ?></textarea>
                </div>
              </div>
            </div>
            <p>Its ALIVE, what now?</p>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-external-link"></span>
                 </div>
                  <input value="<?php echo Page::escape($WH->getUrlUp()); ?>" type="text" class="form-control input-sm" name="urlUp">
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-file-code-o"></span>
                 </div>
                  <textarea type="text" class="form-control input-sm" name="jsonUp"><?php echo Page::escape($WH->getJsonUp()); ?></textarea>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-file-text-o"></span>
                 </div>
                  <textarea type="text" class="form-control input-sm" name="headersUp" rows="3"><?php echo Page::escape($WH->getHeadersUp()); ?></textarea>
                </div>
              </div>
            </div>
            <div class="form-group">
                  <div class="col-sm-8 col-sm-offset-2">
                    <div class="input-group">
                      <div class="input-group-addon">
                     <span class="fa fa-send-o"></span>
                      </div>
                      <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="method">
                        <?php var_dump($WH->getMethod()); ?>
                        <option <?php echo ("GET" == $WH->getMethod() ? "selected" : ""); ?>  value="1">GET</option>
                        <option <?php echo ("POST" == $WH->getMethod() ? "selected" : ""); ?> value="2">POST</option>
                        <option <?php echo ("PUT" == $WH->getMethod() ? "selected" : ""); ?> value="3">PUT</option>
                      </select>
                     </div>
                </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-pencil"></span>
                 </div>
                  <input value="<?php echo Page::escape($WH->getName()); ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion">
                </div>
              </div>
            </div>
            <div class="form-group">
                  <div class="col-sm-8 col-sm-offset-2">
                    <div class="input-group">
                      <div class="input-group-addon">
                     <span class="fa fa-group"></span>
                      </div>
                      <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups">
                        <?php
                        $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                        $USER_ID = $Login->getUserID();
                        $stmt = $DB->GetConnection()->prepare($query);
                        $stmt->bind_param('i', $USER_ID);
                        $stmt->execute();
                        $stmt->bind_result($db_group_id, $db_group_name);
                        while ($stmt->fetch()) {
                             echo '<option '.($db_group_id == $WH->getGroupID() ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                        }
                        $stmt->close(); ?>
                      </select>
                     </div>
                </div>
            </div>
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Update</button>
            </div>
          </form>

  <?php }

      if ($p == "webhook?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->addHook($_POST['name'],$_POST['method'],$_POST['urlDown'],$_POST['jsonDown'],$_POST['headersDown'],$_POST['urlUp'],$_POST['jsonUp'],$_POST['headersUp'],$_POST['groups']);
             if ($WH->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } ?>

        <form class="form-horizontal" action="index.php?p=webhook?add" method="post">
          <p>What should happen, when the server goes dark?</p>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-external-link"></span>
               </div>
                <input value="<?php if(isset($_POST['urlDown'])) {echo Page::escape($_POST['urlDown']);} else { echo 'https://discordapp.com/api/webhooks/XXXX/XXXXXXXX'; } ?>" type="text" class="form-control input-sm" name="urlDown">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-file-code-o"></span>
               </div>
                <textarea type="text" class="form-control input-sm" name="jsonDown"><?php if(isset($_POST['jsonDown'])) {echo Page::escape($_POST['jsonDown']);} else { echo '{ "content": "wololo! server went to nuts."}'; } ?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-file-text-o"></span>
               </div>
                <textarea type="text" class="form-control input-sm" name="headersDown" rows="3"><?php if(isset($_POST['headersDown'])) {echo Page::escape($_POST['headersDown']);} else { echo '"Content-Type: application/json"'; } ?></textarea>
              </div>
            </div>
          </div>
          <p>Its ALIVE, what now?</p>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-external-link"></span>
               </div>
                <input value="<?php if(isset($_POST['urlUp'])) {echo Page::escape($_POST['urlUp']);} else { echo 'https://discordapp.com/api/webhooks/XXXX/XXXXXXXX'; } ?>" type="text" class="form-control input-sm" name="urlUp">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-file-code-o"></span>
               </div>
                <textarea type="text" class="form-control input-sm" name="jsonUp"><?php if(isset($_POST['jsonUp'])) {echo Page::escape($_POST['jsonUp']);} else { echo '{ "content": "wololo! its back!."}'; } ?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-file-text-o"></span>
               </div>
                <textarea type="text" class="form-control input-sm" name="headersUp" rows="3"><?php if(isset($_POST['headersUp'])) {echo Page::escape($_POST['headersUp']);} else { echo '"Content-Type: application/json"'; } ?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-send-o"></span>
                    </div>
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="method">
                      <option  value="1">GET</option>
                      <option  value="2">POST</option>
                      <option  value="3">PUT</option>
                    </select>
                   </div>
              </div>
          </div>
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-pencil"></span>
               </div>
                <input value="<?php if(isset($_POST['name'])) {echo Page::escape($_POST['name']);} ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion">
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-group"></span>
                    </div>
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups">
                      <?php
                      $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id, $db_group_name);
                      while ($stmt->fetch()) {
                           echo '<option '.($WH->getGroupID() == $db_group_id ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                      }
                      $stmt->close(); ?>
                    </select>
                   </div>
              </div>
          </div>
          <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
          <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Create</button>
          </div>
        </form>

  <?php } ?>

          <div class="table-responsive table-hover">
            <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>

            <?php

            $USER_ID = $Login->getUserID();

            $query = "SELECT ID,Name FROM webhooks WHERE UserID = ? ";
            $stmt = $DB->GetConnection()->prepare($query);
            $stmt->bind_param('i', $USER_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
              echo '<tr>';
              echo '<td class="text-left">'.Page::escape($row['Name']).'</td>';
              echo '<td class="text-left col-md-3"><a href="index.php?p=webhook?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
              echo '<a href="index.php?p=webhook?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
              echo '</tr>';
            } ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
