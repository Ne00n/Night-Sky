<ul class="nav nav-tabs">
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'dashboard' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=dashboard&server=<?php echo Page::escape($serverID); ?>">Dashboard</a>
   </li>
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'cpu' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=cpu&server=<?php echo Page::escape($serverID); ?>">CPU</a>
   </li>
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'memory' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=memory&server=<?php echo Page::escape($serverID); ?>">Memory</a>
   </li>
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'disk' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=disk&server=<?php echo Page::escape($serverID); ?>">Disk</a>
   </li>
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'network' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=network&server=<?php echo Page::escape($serverID); ?>">Network</a>
   </li>
   <li role="presentation" class="<?php echo ($_GET["resource"] == 'alerts' ? 'active' : ''); ?>">
     <a href="index.php?p=server&resource=alerts&server=<?php echo Page::escape($serverID); ?>">Alerts</a>
   </li>
   <li class="col-sm-6 pull-right">
     <div class="row">
     <form method="POST" id="timestamp">
      <input type="hidden" name="timestamp" id="timestamp_box" value="<?php $time = (empty($_SESSION['timestamp_overview']) ? time() : $_SESSION['timestamp_overview']); echo Page::escape($time); ?>" />
      <input type="hidden" name="timeframe" id="timeframe_box" value="<?php $timeframe = (empty($_SESSION['timeframe_overview']) ? "2H" : $_SESSION['timeframe_overview']); echo Page::escape($timeframe); ?>" />
         <div class="form-group">
             <div class='input-group date' id='datetimepicker'>
                 <input type='text' class="form-control" />
                 <span class="input-group-addon">
                     <span class="glyphicon glyphicon-calendar"></span>
                 </span>
                 <span class="input-group-btn">
                   <div class="btn-group">
                      <button id='dropselect' type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php $timeframe = (empty($_SESSION['timeframe_overview']) ? "2H" : $_SESSION['timeframe_overview']); echo Page::escape($timeframe); ?></button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#">1H</a></li>
                        <li><a href="#">2H</a></li>
                        <li><a href="#">4H</a></li>
                        <li><a href="#">6H</a></li>
                        <li><a href="#">12H</a></li>
                        <li><a href="#">24H</a></li>
                        <li><a href="#">48H</a></li>
                      </ul>
                    </div>
                    <button class="btn btn-default" type="button" id="timestamp_reset"><i class="fa fa-undo" aria-hidden="true"></i></button>
                    <button class="btn btn-default" type="button" id="timestamp_submit"><i class="fa fa-play" aria-hidden="true"></i></button>
                 </span>
             </div>
         </div>
       </form>
         <script type="text/javascript">
         $(function () {
             $('#datetimepicker').datetimepicker({
                defaultDate: new Date(<?php $time = (empty($_SESSION['timestamp_overview']) ? time() : $_SESSION['timestamp_overview']); echo Page::escape($time); ?>*1000),
                format: 'DD/MM/YYYY HH:mm',
             });
         });

         $('#datetimepicker').on('dp.change', function (e) {
         document.getElementById('timestamp_box').value = e.date.unix();
         });

         $(".dropdown-menu li a").click(function(){
            $(".btn:first-child").text($(this).text());
            document.getElementById('timeframe_box').value = $(this).text();
         });

         var form = document.getElementById("timestamp");
         document.getElementById("timestamp_submit").addEventListener("click", function () {
             form.submit();
         });

         document.getElementById("timestamp_reset").addEventListener("click", function () {
             document.getElementById('timestamp_box').value = 'reset';
             form.submit();
         });
     </script>
    </div>
   </li>
</ul>
