<?php

if (php_sapi_name() != 'cli') { die(); }

include 'vendor/autoload.php';

use MatthiasMullie\Minify;

$minifier = new Minify\JS('js/jquery.min.js');
$minifier->add('js/bootstrap.min.js');
$minifier->add('js/bootstrap-select.min.js');
$minifier->minify('js/night.js');

$minifier = new Minify\CSS('css/raleway.css');
$minifier->add('content/font-awesome-4.7.0/css/font-awesome.min.css');
$minifier->add('css/bootstrap.min.css');
$minifier->add('css/style.css');
$minifier->add('css/signin.css');
$minifier->add('css/bootstrap-select.min.css');

$minifier->minify('css/night.css');

?>
