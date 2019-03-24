<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type='text/css' href="/Projet/CSS/style.css" />
  <link href="https://fonts.googleapis.com/css?family=Cantarell" rel="stylesheet">
  <ul class="menu">
    <li><a href="/Projet/index.php">Accueil</a></li>
    <?php
    if (!isset($_SESSION['user'])) { ?>
      <li><a href="/Projet/register_login/register.php">Inscription</a></li>
      <li><a href="/Projet/register_login/login.php">Connexion</a></li>
    <?php
    } else { ?>
      <li><a href="/Projet/annonces.php">Annonces</a></li>
      <li><a href="/Projet/register_login/account_info.php">Mon compte</a></li>
      <li><a href="/Projet/register_login/private_message.php">Messages</a></li>
      <li><a href="/Projet/register_login/logout.php">D&eacute;connexion</a></li>
      <?php
    }
      ?>
      <li><a href="/Projet/about.php">A propos</a></li>
    </ul>
