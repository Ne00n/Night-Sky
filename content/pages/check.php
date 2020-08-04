<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#settings">Settings</a></li>
  <li><a data-toggle="tab" href="#advanced">Advanced</a></li>
</ul>
<div class="tab-content">
  <div id="settings" class="tab-pane fade in active pt-20">
    <div class="form-group">
      <div class="col-sm-6 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
           <span class="fa fa-server"></span>
         </div>
         <?php if (Page::startsWith($p,"main?edit=")) { $ip = $M->getIP(); } elseif (isset($_POST['ip'])) { $ip = ($_POST['ip']);} else { $ip = ""; } ?>
         <input value="<?php echo Page::escape($ip); ?>" type="text" class="form-control input-sm" name="ip" placeholder="127.0.0.1"/>
        </div>
      </div>
      <div class="col-sm-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-circle-o"></span>
         </div>
          <?php if (Page::startsWith($p,"main?edit=")) { $port = $M->getPort(); } elseif (isset($_POST['port'])) { $port = ($_POST['port']);} else { $port = ""; } ?>
          <input value="<?php echo Page::escape($port); ?>" type="text" class="form-control input-sm" name="port" placeholder="80"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-6 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-pencil"></span>
         </div>
          <?php if (Page::startsWith($p,"main?edit=")) { $name = $M->getName(); } elseif (isset($_POST['name'])) { $name = ($_POST['name']);} else { $name = ""; } ?>
          <input value="<?php echo Page::escape($name); ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion"/>
        </div>
      </div>
      <div class="col-sm-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-cube"></span>
         </div>
          <select class="selectpicker form-control input-sm" data-size="4" data-style="btn-default btn-sm" name="type">
            <?php if (Page::startsWith($p,"main?edit=")) {
              if ($M->getType() == 'tcp') { echo '<option selected>TCP</option>'; echo '<option>HTTP</option>';
              } elseif ($M->getType() == 'http') { echo '<option>TCP</option>'; echo '<option selected>HTTP</option>'; }
            } else {
              echo '<option>TCP</option><option>HTTP</option>';
            }
            ?>
          </select>
        </div>
      </div>
    </div>
    <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
    <div class="form-group">
          <div class="col-sm-6 col-sm-offset-2">
            <div class="input-group">
              <div class="input-group-addon">
             <span class="fa fa-group"></span>
              </div>
              <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="email[]" multiple>
                <?php
                if (Page::startsWith($p,"main?edit=")) {
                  $group_ids = array();

                  $query = "SELECT GroupID FROM groups_checks WHERE CheckID=?";
                  $stmt = $DB->GetConnection()->prepare($query);
                  $stmt->bind_param('i', $check_id);
                  $stmt->execute();
                  $stmt->bind_result($db_group_id);
                  while ($stmt->fetch()) {
                       $group_ids[] = $db_group_id;
                  }

                  $query = "SELECT ID,Name FROM groups WHERE USER_ID=? ORDER BY ID";
                  $USER_ID = $Login->getUserID();
                  $stmt = $DB->GetConnection()->prepare($query);
                  $stmt->bind_param('i', $USER_ID);
                  $stmt->execute();
                  $stmt->bind_result($db_group_id, $db_group_name);
                  while ($stmt->fetch()) {
                       echo '<option '.(in_array($db_group_id,$group_ids) ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                  }
                  $stmt->close();
                } else {
                  $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                  $USER_ID = $Login->getUserID();
                  $stmt = $DB->GetConnection()->prepare($query);
                  $stmt->bind_param('i', $USER_ID);
                  $stmt->execute();
                  $stmt->bind_result($db_group_id, $db_group_name);
                  while ($stmt->fetch()) {
                       echo '<option value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                  }
                  $stmt->close();
                } ?>
              </select>
             </div>
        </div>
        <div class="col-sm-2">
          <div class="input-group">
           <div class="input-group-addon">
          <span class="fa fa-hourglass-end"></span>
           </div>
            <select class="selectpicker form-control input-sm" data-size="4" data-style="btn-default btn-sm" name="interval">
              <?php
              if (Page::startsWith($p,"main?edit=")) {
                for ($i =10; $i <= 60; $i = $i + 10) {
                  if ($M->getInterval() == $i) {
                      echo '<option selected>'.$i.'</option>';
                  } elseif ($i != 40 && $i != 50) {
                      echo '<option>'.$i.'</option>';
                  }
                }
              } else {
                for ($i =10; $i <= 60; $i = $i + 10) {
                    if ($i != 40 && $i != 50) {
                      echo '<option>'.$i.'</option>';
                    }
                }
              }
              ?>
            </select>
          </div>
        </div>
    </div>
  </div>
  <div id="advanced" class="tab-pane fade pt-20">
    <div class="form-group">
      <div class="col-sm-6 col-sm-offset-3">
        <div class="input-group">
         <div class="input-group-addon">
           <span class="fa fa-check-circle-o"></span>
         </div>
         <?php if (Page::startsWith($p,"main?edit=")) { $httpcodes = $M->getStatusCodes(); } elseif (isset($_POST['httpCodes'])) { $httpcodes = ($_POST['httpCodes']);} else { $httpcodes = 200; } ?>
         <input value="<?php echo Page::escape($httpcodes); ?>" type="text" class="form-control input-sm" placeholder="200 or 200,300" name="httpCodes"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-3 col-sm-offset-3">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-times-circle-o"></span>
         </div>
          <select class="selectpicker form-control input-sm" data-size="4" data-style="btn-default btn-sm" name="timeout">
            <?php
            if (Page::startsWith($p,"main?edit=")) {
              for ($i =0.5; $i <= 5; $i = $i + 0.5) {
                echo '<option value="'.$i.'"'.($i == $M->getTimeout() ? ' selected' : '').'>'.$i.' s</option>';
              }
            } else {
              for ($i =0.5; $i <= 5; $i = $i + 0.5) {
                echo '<option value="'.$i.'"'.($i == 1.5 ? ' selected' : '').'>'.$i.' s</option>';
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-exchange"></span>
         </div>
          <select class="selectpicker form-control input-sm" data-size="4" data-style="btn-default btn-sm" name="connectionTimeout">
            <?php
            if (Page::startsWith($p,"main?edit=")) {
              for ($i =0.5; $i <= 5; $i = $i + 0.5) {
                echo '<option value="'.$i.'"'.($i == $M->getConnect() ? ' selected' : '').'>'.$i.' s</option>';
              }
            } else {
              for ($i =0.5; $i <= 5; $i = $i + 0.5) {
                echo '<option value="'.$i.'"'.($i == 1.5 ? ' selected' : '').'>'.$i.' s</option>';
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="col-sm-3">
          <div class="funkyradio">
              <div class="funkyradio-primary">
                  <?php
                  if (Page::startsWith($p,"main?edit=")) {
                    echo '<input type="checkbox" name="mtr" id="mtr" '.( $M->getMTR() ? ' checked' : '').'/>';
                  } else {
                    echo '<input type="checkbox" name="mtr" id="mtr"/>';
                  }
                  ?>
                  <label for="mtr">Send MTR on Downtime</label>
              </div>
        </div>
      </div>
  </div>
</div>
</div>
