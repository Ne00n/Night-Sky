<?php

define("_regex_USERNAME","/^[a-zA-Z0-9]+$/");
define("_regex_TOKEN",_regex_USERNAME);
define("_regex_NAME","/^[a-zA-Z0-9._\-, ]+$/");
define("_regex_HEADERS",'/["][a-zA-Z0-9-: \/,"]+/x');
define("_regex_PORT","/^[0-9]+$/");
define("_regex_ID",_regex_PORT);

define("_max_Name",50);
define("_min_Name",3);

define("_min_URL",1);
define("_max_URL",200);

define("_min_Password",8);
define("_max_Password",160);

define("_min_Mail",5);
define("_max_Mail",80);

?>
