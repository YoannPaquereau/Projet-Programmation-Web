<?php
  /* Ajouter les informations concernant la note d'évaluation de l'utilisateur */

  session_start();
?>

<html>
  <?php
    require "../header.php";
  ?>
    <meta charset="utf-8" />
    <title>Mon compte</title>
    <link rel="stylesheet" type='text/css' href="/Projet/CSS/annonce.css" />
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

      if (isset($_GET['user'])) $user = $_GET['user'];
      else $user = $_SESSION['user'];

      // On prépare la requête d'insertion
      $req = $bdd->prepare('SELECT nom, prenom, DATE_FORMAT(date_naissance, \'%d/%m/%Y\') AS date_n,
                            DATE_FORMAT(date_inscription, \'%d/%m/%Y, à %Hh%i\') AS date_i, DATE_FORMAT(derniere_connexion, \'%d/%m/%Y, à %Hh%i\') AS date_c FROM users WHERE login = :login');


      // On exécute la requête avec nos valeurs
      $req->execute(array(
        'login' => $user
      ));
      // On met le résultat de notre requête dans une variable
      $resultat = $req->fetch();

      // On met les informations que l'on vient de récupérer dans des variables
      $user = $user;
      $nom = $resultat['nom'];
      $prenom = $resultat['prenom'];
      $date_naissance = $resultat['date_n'];
      if (!isset($_GET['user'])) $derniere_connexion = $_SESSION['last_connection'];
      else $derniere_connexion = $resultat['date_c'];
      $date_inscription = $resultat['date_i'];

      // On ferme la requête
      $req->CloseCursor();


    ?>

    <!-- On affiche les valeurs que l'on vient de récupérer sous forme de liste -->
    <h2>Informations du compte</h2>
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

    <?php
    if (isset($_GET['user']))
      echo '<a href="private_message.php?for='.$_GET['user'].'">Contacter cette personne ?</a>';
    ?>

    <br>
    <h2>Annonce(s)</h2>

    <?php

    try
    {
      $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
    }
    catch(Exception $e)
    {
      die('erreur : '.$e->getmessage());
    }

    $req = $bdd->prepare('SELECT * FROM annonces WHERE auteur = :auteur');

    $req->execute(array(
      'auteur' => $user
    ));

    require "../include/f_annonce.php";
    echo '<ul class="liste_annonces">';

    while ($donnees = $req->fetch()) {
        afficheInfosAnnonces($donnees);
    }
    echo "</ul>";
    $req->CloseCursor();


    if (!isset($_GET['user'])) {
      ?>
      <h2>Mes r&eacute;servations</h2>
      <?php
        $req = $bdd->prepare('SELECT * FROM annonces, reservation WHERE client = :client AND id_annonce = annonce');
        $req->execute(array(
          'client' => $_SESSION['user']
        ));

        while ($donnees = $req->fetch()) {
            afficheInfosAnnonces($donnees);
        }
    }

    ?>
  </body>
</html>
