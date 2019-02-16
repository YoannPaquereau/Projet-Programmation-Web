<?php
  session_start();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Accueil</title>
  </head>

  <body>
    <p>
      <?php
        if (!isset($_SESSION['user'])) echo "Vous n'êtes pas connecté<br/><a href=\"login.php\">Connexion</a>";
        else echo 'Bonjour '.$_SESSION['user'].'<br/>Dernière connexion : '.$_SESSION['last_connection'].'<br/><a href="logout.php">D&eacute;connexion</a>';
      ?>
    </p>


  </body>
</html>
