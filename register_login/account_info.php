<?php
  session_start();
?>

<html>
  <?php
    require "../header.php";
  ?>
    <meta charset="utf-8" />
    <title>Mon compte</title>
  </head>

  <body>
    <?php
      // Inclusion des paramètres de connexion de notre BD
      include_once("myparam.inc.php");

      // On accède à notre base de données
      try
      {
        $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8', MYUSER, MYPASS);
      }
      catch (Exception $e)
      {
        die('Erreur : ' . $e->getMessage());
      }

      // On prépare la requête d'insertion
      $req = $bdd->prepare('SELECT nom, prenom, date_naissance, date_inscription FROM users WHERE login = :login');
      // On exécute la requête avec nos valeurs
      $req->execute(array(
        'login' => $_SESSION['user'],
      ));
      // On met le résultat de notre requête dans une variable
      $resultat = $req->fetch();

      // On met les informations que l'on vient de récupérer dans des variables
      $user = $_SESSION['user'];
      $nom = $resultat['nom'];
      $prenom = $resultat['prenom'];
      $date_naissance = $resultat['date_naissance'];
      $derniere_connexion = $_SESSION['last_connection'];
      $date_inscription = $resultat['date_inscription'];

      // On ferme la requête
      $req->CloseCursor();
    ?>


    <h2>Mon compte</h2>
    <p>
      <ul>
        <li>Nom d'utilisateur : <?php echo $user;?></li>
        <li>Nom : <?php echo $nom;?></li>
        <li>Pr&eacute;nom : <?php echo $prenom;?></li>
        <li>Date de naissance : <?php echo $date_naissance;?></li>
        <li>Derni&egrave;re connexion : <?php echo $derniere_connexion;?></li>
        <li>Date d'Inscription : <?php echo $date_inscription;?></li>
      </ul>
    </p>
  </body>
</html>
