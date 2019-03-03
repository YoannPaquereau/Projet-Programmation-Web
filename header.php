<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type='text/css' href="/Projet/style.css" />
  <ul class="menu">
    <li><a href="/Projet">Accueil</a></li>
    <?php
    if (!isset($_SESSION['user'])) { ?>
      <li><a href="/Projet/register_login/register.php">Inscription</a></li>
      <li><a href="/Projet/register_login/login.php">Connexion</a></li>
    <?php
    } else { ?>
      <li><a href="annonce.php">Annonces</a></li>
      <li><a href="/Projet/register_login/account_info.php">Mon compte</a></li>
      <li><a href="/Projet/register_login/private_message.php">Messages</a></li>
      <li><a href="/Projet/register_login/logout.php">D&eacute;connexion</a></li>
      <?php
    }
      ?>
      <li><a href="about.php">A propos</a></li>
    </ul>
