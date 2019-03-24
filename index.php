<?php
  session_start();
?>

<html>
    <title>ShareMyHouse</title>
    <?php
      require "header.php";
    ?>
  </head>

  <body>
    <p>
      <?php
        if (!isset($_SESSION['user'])) echo 'Vous n\'&ecirc;tes pas connect&eacute;<br/><a href="register_login/login.php\">Connexion</a>';
        else echo 'Bonjour '.$_SESSION['user'];
      ?>
    </p>
  </body>
</html>
