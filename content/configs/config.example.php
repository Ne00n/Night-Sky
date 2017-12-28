<?php

//Domain
define("_Domain","night-sky.local");

date_default_timezone_set('Europe/Amsterdam');
session_set_cookie_params(0,'/','.'._Domain,true,true);

//User Limit
define("_user_limit",2);

//Global IP Limit (same checks on the same IP)
define("_ip_limit_global",4);

//Global Checks Limit (all users added together)
define("_checks_limit_global",50);

//Groups Limit (ammount of groups that can be connected at once to any contact or check, the ammount that can be created has a user defined limit in the DB)
define("_groups_limit_global",15);

//Signup codes
define('_signUpCodes', array('LET'));

//Mail Sender
define("_mail_sender","noreply@yourdomain.net");

//Database
define("_db_host", "localhost");
define("_db_user", "night");
define("_db_password", "night");
define("_db_database", "night");

 ?>
