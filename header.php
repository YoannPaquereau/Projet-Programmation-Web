<html>
    <link rel="stylesheet" href="style.css" />
    <ul id="menu">
      <li><a href="index.php">Accueil</a></li>
      <?php
      if (!isset($_SESSION['user'])) { ?>
        <li><a href="register.php">Inscription</a></li>
        <li><a href="login.php">Connexion</a></li>
      <?php
      } else { ?>
        <li><a href="annonce.php">Annonce</a></li>
        <li><a href="account_info.php">Mon compte</a></li>
        <li><a href="logout.php">D&eacute;connexion</a></li>
        <?php
      }
       ?>
       <li><a href="about.php">A propos</a></li>
     </ul>

  </html>
