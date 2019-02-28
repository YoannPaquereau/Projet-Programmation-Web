<html>
    <link rel="stylesheet" href="style.css" />
    <ul id="menu">
      <li><a href="/Projet/index.php">Accueil</a></li>
      <?php
      if (!isset($_SESSION['user'])) { ?>
        <li><a href="/Projet/register_login/register.php">Inscription</a></li>
        <li><a href="/Projet/register_login/login.php">Connexion</a></li>
      <?php
      } else { ?>
        <li><a href="annonce.php">Annonces</a></li>
        <li><a href="/Projet/register_login/account_info.php">Mon compte</a></li>
        <li><a href="/Projet/register_login/logout.php">D&eacute;connexion</a></li>
        <?php
      }
       ?>
       <li><a href="about.php">A propos</a></li>
     </ul>

  </html>
