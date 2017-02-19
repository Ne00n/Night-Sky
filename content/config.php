<?php

//Domain
define("_Domain","night.x8e.ru");

date_default_timezone_set('Europe/Amsterdam');
session_set_cookie_params(0,'/','.'._Domain,true,true);

//User Limit
define("_user_limit",2);

//Global IP Limit
define("_ip_limit_global",4);

//Mail Sender
define("_mail_sender","noreply@night.x8e.ru");

//Database
define("_db_host", "localhost");
define("_db_user", "night");
define("_db_password", "x2xQIpu3WvTixHtV");
define("_db_database", "night");

 ?>
