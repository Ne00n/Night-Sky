<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Night Sky</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="images/favicon.ico">

<?php

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
  //CSS
  echo '<link href="css/raleway.css" rel="stylesheet" type="text/css">';
  echo '<link rel="stylesheet" href="content/font-awesome-4.7.0/css/font-awesome.min.css">';
  echo '<link rel="stylesheet" href="css/bootstrap.min.css">';
  echo '<link rel="stylesheet" href="css/style.css">';
  echo '<link href="css/signin.css" rel="stylesheet">';
  echo '<link href="css/bootstrap-select.min.css" rel="stylesheet">';
  //JS
  echo '<script src="js/jquery.min.js"></script>';
  echo '<script src="js/bootstrap.min.js"></script>';
  echo '<script src="js/bootstrap-select.min.js"></script>';
} else {
  echo '<link href="css/night.css" rel="stylesheet">';
  echo '<script src="js/night.js"></script>';
}

?>

</head>
