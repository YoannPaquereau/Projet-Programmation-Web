<?php
  session_start();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>ShareMyHouse</title>
    <?php
      require "header.php";
    ?>
  </head>

  <body>
    <p>
      <?php
        if (!isset($_SESSION['user'])) echo "Vous n'&ecirc;tes pas connect&eacute;<br/><a href=\"register_login/login.php\">Connexion</a>";
        else echo 'Bonjour '.$_SESSION['user'].'<br/>Derni&egrave;re connexion : '.$_SESSION['last_connection'].'<br/><a href="register_login/logout.php">D&eacute;connexion</a>';
      ?>
    </p>


  </body>
</html>
