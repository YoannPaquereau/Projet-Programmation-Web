<?php
  /* Ajouter les informations concernant la note d'évaluation de l'utilisateur
     Nos locations, .. */

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

      require "myparam.inc.php";


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
      $req = $bdd->prepare('SELECT nom, prenom, DATE_FORMAT(date_naissance, \'%d/%m/%Y\') AS date_n,
                            DATE_FORMAT(date_inscription, \'%d/%m/%Y, à %Hh%i\') as date_i FROM users WHERE login = :login');
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
      $date_naissance = $resultat['date_n'];
      $derniere_connexion = $_SESSION['last_connection'];
      $date_inscription = $resultat['date_i'];

      // On ferme la requête
      $req->CloseCursor();


    ?>

    <!-- On affiche les valeurs que l'on vient de récupérer sous forme de liste -->
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
