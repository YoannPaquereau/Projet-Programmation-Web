<?php
  session_start();
  session_destroy();
  include_once("myparam.inc.php");
  header('Location: ../index.php');
?>
