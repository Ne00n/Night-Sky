<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#downtime">Downtime</a></li>
  <li><a data-toggle="tab" href="#uptime">Uptime</a></li>
  <li><a data-toggle="tab" href="#method">Method</a></li>
</ul>
<div class="tab-content">
  <div id="downtime" class="tab-pane fade in active pt-20">
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-external-link"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $urlDown = $WH->getUrlDown(); } elseif (isset($_POST['urlDown'])) { $urlDown = ($_POST['urlDown']);} else { $urlDown = 'https://discordapp.com/api/webhooks/XXXX/XXXXXXXX'; } ?>
          <input value="<?php echo Page::escape($urlDown); ?>" type="text" class="form-control input-sm" name="urlDown">
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-file-code-o"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $jsonDown = $WH->getJsonDown(); } elseif (isset($_POST['jsonDown'])) { $jsonDown = ($_POST['jsonDown']);} else { $jsonDown = '{ "content": "wololo! server went to nuts."}'; } ?>
          <textarea type="text" class="form-control input-sm" name="jsonDown"><?php echo Page::escape($jsonDown); ?></textarea>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-file-text-o"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $headersDown = $WH->getHeadersDown(); } elseif (isset($_POST['headersDown'])) { $headersDown = ($_POST['headersDown']);} else { $headersDown = '"Content-Type: application/json"'; } ?>
          <textarea type="text" class="form-control input-sm" name="headersDown" rows="3"><?php echo Page::escape($headersDown); ?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div id="uptime" class="tab-pane fade pt-20">
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-external-link"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $urlUp = $WH->getUrlUp(); } elseif (isset($_POST['urlUp'])) { $urlUp = ($_POST['urlUp']);} else { $urlUp = 'https://discordapp.com/api/webhooks/XXXX/XXXXXXXX'; } ?>
          <input value="<?php echo Page::escape($urlUp); ?>" type="text" class="form-control input-sm" name="urlUp">
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-file-code-o"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $jsonUp = $WH->getJsonUp(); } elseif (isset($_POST['jsonUp'])) { $jsonUp = ($_POST['jsonUp']);} else { $jsonUp = '{ "content": "wololo! its back!."}'; } ?>
          <textarea type="text" class="form-control input-sm" name="jsonUp"><?php echo Page::escape($jsonUp); ?></textarea>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-file-text-o"></span>
         </div>
          <?php if (Page::startsWith($p,"webhook?edit=")) { $headersUp = $WH->getHeadersUp(); } elseif (isset($_POST['headersUp'])) { $headersUp = ($_POST['headersUp']);} else { $headersUp = '"Content-Type: application/json"'; } ?>
          <textarea type="text" class="form-control input-sm" name="headersUp" rows="3"><?php echo Page::escape($headersUp); ?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div id="method" class="tab-pane fade pt-20">
    <div class="form-group">
          <div class="col-sm-8 col-sm-offset-2">
            <div class="input-group">
              <div class="input-group-addon">
             <span class="fa fa-send-o"></span>
              </div>
              <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="method">
                <?php if (Page::startsWith($p,"webhook?edit=")) {
                  echo '<option '.("GET" == $WH->getMethod() ? "selected" : "").' value="1">GET</option>';
                  echo '<option '.("POST" == $WH->getMethod() ? "selected" : "").' value="2">POST</option>';
                  echo '<option '.("PUT" == $WH->getMethod() ? "selected" : "").' value="3">PUT</option>';
                } else {
                  echo '<option  value="1">GET</option><option  value="2">POST</option><option  value="3">PUT</option>';
                }
                ?>
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
         <?php if (Page::startsWith($p,"webhook?edit=")) { $name = $WH->getName(); } elseif (isset($_POST['name'])) { $name = ($_POST['name']);} else { $name = ''; } ?>
          <input value="<?php echo Page::escape($name); ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion">
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
                  if (Page::startsWith($p,"webhook?edit=")) {
                    echo '<option '.($db_group_id == $WH->getGroupID() ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                  } else {
                    echo '<option '.($db_group_id == $WH->getGroupID() ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';

                  }
                }
                $stmt->close(); ?>
              </select>
             </div>
        </div>
    </div>
    <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
  </div>
</div>
