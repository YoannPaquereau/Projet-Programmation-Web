<?php
  session_start();
  session_destroy();
  header('Location: /Projet/index.php');
?>
