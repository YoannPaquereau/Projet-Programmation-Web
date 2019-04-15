<?php
// Code par TRAORE Abdoul Aziz - PAQUEREAU Yoann


  session_start();
?>

<html>

  <?php
    require "header.php"
  ?>
    <link rel="stylesheet" type='text/css' href="/Projet/CSS/annonce.css" />
    <title>Annonces</title>
  </head>

  <body>
    <h1>Annonces</h1>
    <?php

    // Si on est pas connecté, la page n'est pas accessible
    if (!isset($_SESSION['user'])) echo "Page not found";
    else {

        // On inclut nos paramètres de connexion à notre base de données
        require "register_login/myparam.inc.php";

        // idem pour les fonctions pour l'affichage des annonces
        require "include/f_annonce.php";

        // Si on est sur une annonce particulier
        if (isset($_GET['annonce'])) {
          try
          {
            $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
          }
          catch(Exception $e)
          {
            die('erreur : '.$e->getmessage());
          }

          // Si on a utilisé le formulaire pour réserver
          if (isset($_POST['resa_debut'])) {

            if ($_POST['resa_fin'] < $_POST['resa_debut']) {
              header('Refresh:3;url=annonces.php?annonce='.$_GET['annonce']);
              echo "Date de fin est après la date de d&eacute;but. Veuillez r&eacuteessayer";
            }

            // Si le logement est disponible entre les dates données
            elseif (estDispo($_GET['annonce'], $_POST['resa_debut'], $_POST['resa_fin'])) {

              // Requête permettant d'insérer la réservation
              $req = $bdd->prepare('INSERT INTO reservation(client, auteur, date_debut, date_fin, annonce)
                                    VALUES (:client, :auteur, :date_debut, :date_fin, :annonce)');

              $req->execute(array(
                'client' => $_SESSION['user'],
                'auteur' => $_POST['resa_auteur'],
                'date_debut' => $_POST['resa_debut'],
                'date_fin' => $_POST['resa_fin'],
                'annonce' => $_GET['annonce']
              ));

              // Redirection vers la page d'accueil des annonces
              header('Refresh:3;url=annonces.php');
              echo 'Annonce r&eacute;serv&eacute;e !<br><br>Redirection dans 3 secondes...<br>Trop long ? <a href="annonces.php">Clique ici</a>';
            } else {

              // On revient sur la même page d'annonce
              header('Refresh:3;url=annonces.php?annonce='.$_GET['annonce']);
              echo "Logement non disponible pour cette p&eacute;riode. Veuillez r&eacuteessayer";
            }
          }

          // Si on a pas réservé
          else {

            // On récupère l'annonce qu'on a sélectionné dans notre base de données
            $req = $bdd->prepare('SELECT * FROM annonces WHERE id_annonce=:id');
            $req->execute(array(
              'id' => $_GET['annonce']
            ));


            // On récupère le résultat de notre requête
            $donnees = $req->fetch();
            $auteur = $donnees['auteur'];

            // Que l'on va afficher
            echo 'Type : '.$donnees['type'];
            echo '<br>Ville :'.$donnees['ville'];
            echo '<br>Prix : '.$donnees['prix'].'€';
            if ($donnees['auteur'] != $_SESSION['user']) echo '<br>Auteur : '.$donnees['auteur'].' (<a href="/Projet/register_login/account_info.php?user='.$donnees['auteur'].'">Voir son profil</a>)';

            $req->CloseCursor();

            // On récupère le nom de toutes les images de notre annonce
            $req = $bdd->prepare('SELECT nom_image FROM image WHERE annonce = :id');

            $req->execute(array(
              'id' => $_GET['annonce']
            ));

            // On met le chemin pour ouvrir notre image dans une variable
            $dossier = 'images/'.$_GET['annonce'].'/';

            // On va afficher nos images sous forme de liste
            echo '<ul class="annonce">';
            while($donnees = $req->fetch()) {
              echo '<li><img src="'.$dossier.$donnees['nom_image'].'"width="400" height="200"></li>';
            }
            echo '</ul>';

            $req->CloseCursor();

            // On affiche le formulaire de réservation uniquement si ce n'est pas notre propre annonce
            if ($auteur != $_SESSION['user']) {
              $datemin= date("Y-m-d");
              $datemax= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 year"));
              ?>
              <h2>R&eacute;server</h2>
              <form action="annonces.php?annonce=<?php echo $_GET['annonce'];?>" method="post" class="reservation">
                Date de d&eacute;but : <input type="date" name="resa_debut" value="<?php echo (isset($_GET['datedebut'])) ? $_GET['datedebut'] : $datemin;?>" min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required><br>
                Date de fin : <input type="date" name="resa_fin" value="<?php echo (isset($_GET['datefin'])) ? $_GET['datefin'] : $datemin;?>" min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required><br>
                <input type="hidden" name="resa_auteur" value="<?php echo $auteur;?>">
                <input type="submit" value="Réserver">
              </form>
        <?php
            }


            // Requête permettant de récupérer le nombre d'avis pour une annonce
            $req2 = $bdd->prepare('SELECT count(*) AS nbr FROM avis, reservation WHERE reservation.id_reservation=avis.reservation AND annonce=:id_annonce');
            $req2->execute(array(
              'id_annonce' => $_GET['annonce']
            ));

            // On récupère nos données
            $donnees2 = $req2->fetch();

            // S'il y a au moins un avis, on les affiche et on fait la moyenne des notes
            if ($donnees2['nbr']) {

              // On fait la moyenne des notes
              $req = $bdd->prepare('SELECT AVG(note) AS myne FROM avis, reservation WHERE reservation.id_reservation=avis.reservation AND annonce=:id_annonce');

              // Sur une annonce
              $req->execute(array(
                'id_annonce' => $_GET['annonce']
              ));

              // On l'affiche
              $donnees2 = $req->fetch();

              // Requête permettant de récupérer tous les avis d'une annonce
              $req = $bdd->prepare('SELECT note, avis FROM reservation, avis WHERE reservation.id_reservation=avis.reservation AND annonce=:id_annonce');

              $req->execute(array(
                'id_annonce' => $_GET['annonce']
              ));


              echo "<h2>Avis</h2>";

              echo 'Moyenne des Notes :'.round($donnees2['myne'], 1).'<br><br>';

              // On récupère nos avis
              while($donnees2 = $req->fetch()){
                echo 'Note :'.$donnees2['note'].'<br>Avis : '.$donnees2['avis'].'<br><br>';
              }


            }
          }
        }



        // Si on est dans l'accueil des annonces et qu'on a pas créé une annonce
        elseif (!isset($_POST["type"]) || !isset($_POST["ville"]) || !isset($_POST["prix"]) || !isset($_POST["nombre_images"])) {

          // On met date minimum à celle d'aujourd'hui, et celle max dans 1 an
          $datemin= date("Y-m-d");
          $datemax= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 year"));
          ?>
          <p>
            <?php

              // Si on a effectué une recherche
              if (isset($_POST['recherche_ville'])) {
                $ville = $_POST['recherche_ville'];
                $prix = $_POST['recherche_prix'];
                $type = $_POST['recherche_type'];
                $datedebut = $_POST['recherche_datedebut'];
                $datefin = $_POST['recherche_datefin'];
              }


              // On affiche le formulaire de recherche, avec les valeurs de notre recherche précédente (s'il il y en a une)
              ?>
            <form action='annonces.php' method="post">
              <select name="recherche_ville">
                <option value="<?php if (isset($ville)) echo $ville; ?>"><?php echo (isset($ville) ? $ville : 'Ville');?></option>
                <option value="Amiens">Amiens</option>
                <option value="Paris">Paris</option>
                <option value="Lille">Lille</option>
                <option value="Rennes">Rennes</option>
                <option value="Bordeaux">Bordeaux</option>
              </select>

              <select name="recherche_type">
                <option value="<?php if (isset($type)) echo $type; ?>"><?php echo (isset($type) ? $type : 'Type de logement');?></option>
                <option value="Maison">Maison</option>
                <option value="Studio">Studio</option>
                <option value="Appartement">Appartement</option>
              </select>

              <input type="number" name="recherche_prix" <?php if (isset($_POST['recherche_prix'])) echo 'value="'.$_POST['recherche_prix'].'"'; else echo 'placeholder="Prix maximum"';?>  step="1" min="20"  required>
              <input type="submit" name="submit" value="Rechercher"><br>
              D&eacute;but : <input type ="date" name="recherche_datedebut" value="<?php echo (isset($datedebut) ? $datedebut : $datemin);?>" min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required>
              Fin : <input type ="date" name="recherche_datefin" value="<?php echo (isset($datefin) ? $datefin : $datemin);?>"    min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required>
            </form>



            <?php

            try
            {
              $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
            }
            catch(Exception $e)
            {
              die('erreur : '.$e->getmessage());
            }

            // Si on a effectué une recherche
            if (isset($_POST['recherche_prix'])) {

              // Requête récupérant les annonces correspondant aux restrictions de notre recherche
              $req = $bdd->prepare("SELECT * FROM annonces
                                    WHERE prix <=:prix
                                    AND type = :type
                                    AND ville = :ville
                                    AND date_dispo_debut <= :recherche_datedebut
                                    AND date_dispo_fin >= :recherche_datefin AND auteur <> :auteur");

              $req->execute(array(
                'prix' => $_POST['recherche_prix'],
                'ville' => $_POST['recherche_ville'],
                'type' => $_POST['recherche_type'],
                'recherche_datedebut' => $_POST['recherche_datedebut'],
                'recherche_datefin' => $_POST['recherche_datefin'],
                'auteur' => $_SESSION['user']
              ));

              // On affiche ces annonces sous forme de liste
              echo '<ul class="liste_annonces">';
              while ($donnees = $req->fetch()) {
                if (estDispo($donnees['id_annonce'], $_POST['recherche_datedebut'], $_POST['recherche_datefin'])) {
                  echo '<li>';
                  afficheInfosAnnonces($donnees);
                  echo '</li>';
                }
              }
              echo "</ul></br></br>";
              $req->CloseCursor();

            }

            // Si pas de recherche
            else {

              // On récupère toutes les annonces (hormis les notres)
              $req = $bdd->prepare('SELECT * FROM annonces WHERE auteur <> :auteur');

              $req->execute(array(
                'auteur' => $_SESSION['user']
              ));

              // On affiche les annonces sous forme de liste
              echo '<ul class="liste_annonces">';
              while ($donnees = $req->fetch()) {
                echo '<li>';
                afficheInfosAnnonces($donnees);
                echo '</li>';
              }
              echo '</ul><br><br>';
            }



            // Date min et max pour la création d'une annonce (à partir de demain)
            $datemin2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 day"));
            $datemax2= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemax)) . " +1 day"));
            ?>

            <h2>Cr&eacute;ation d'une annonce</h2>

            <?php // Formulaire de création d'une annonce ?>
            <form action="annonces.php" method="post">
              Type: <select name="type">
                      <option value="Maison">Maison</option>
                      <option value="Studio">Studio</option>
                      <option value="Appartement">Appartement</option>
                   </select><br>
              Ville: <select name="ville">
                        <option value="Amiens">Amiens</option>
                        <option value="Paris">Paris</option>
                        <option value="Lille">Lille</option>
                        <option value="Rennes">Rennes</option>
                        <option value="Bordeaux">Bordeaux</option>
                    </select><br>
              Prix: <input type="number" name="prix" placeholder="Prix" step="0.01" min="20"  required><br>

              Date de debut: <input type ="date" name="datedebut" min="<?php echo $datemin2;?>" max="<?php echo $datemax2;?>" required><br>

              Date de fin: <input type ="date" name="datefin"min="<?php echo $datemin2;?>" max="<?php echo $datemax2;?>" required><br>
              Nombre image(s): <input type="number" name="nombre_images" min="1" max="8" required><br>
              <input type="submit" value="Envoyer">
            </form>
          </p>

          <?php
        }


        // Si on a créé une annonce, mais pas encore envoyé d'image(s)
        elseif (!isset($_FILES['fichier0']))
        {

          // On récupère les dates
          $datedebut= $_POST["datedebut"];
          $datefin= $_POST["datefin"];

          // On vérifie que la date de fin est bien après la date de début
          if ((strtotime($datedebut) >= strtotime($datefin))) echo("ERREUR,Date de fin antérieur à la date de debut") ;
          else {

          // On récupère toutes les informations de notre formulaire
          $type = $_POST["type"];
          $ville = $_POST["ville"];
          $prix = $_POST["prix"];
          $nbr_images = $_POST["nombre_images"];


          // On recréé un formulaire permettant l'envoi de nos images (en fonction du nombre donné) ?>
          <form method="post" action="annonces.php" enctype="multipart/form-data">
            <?php
            for ($i=0;$i<$nbr_images;$i++)
            { ?>
              <input type="file" name="fichier<?php echo $i;?>" required><br><?php
            } ?>
            <input type = "hidden" name = "type" value = "<?php echo $type; ?>">
            <input type = "hidden" name = "ville" value = "<?php echo $ville; ?>">
            <input type = "hidden" name = "prix" value = "<?php echo $prix; ?>">
            <input type = "hidden" name = "datedebut" value = "<?php echo $datedebut;?>">
            <input type = "hidden" name = "datefin" value = "<?php echo $datefin;?>">
            <input type = "hidden" name = "nombre_images" value = "<?php echo $nbr_images; ?>">
            <input type = "submit" value = "Envoyer">

        <?php
        }
      }


        // Si on a envoyéau moins une image
        elseif (isset($_FILES["fichier0"]))
        {

          try
          {
            $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
          }
          catch(Exception $e)
          {
            die('erreur : '.$e->getmessage());
          }

          // On déclare une variable contenant la taille maximale acceptée pour une image (en octets)
          $maxtaille = 10000000;

          // Idem pour les extensions mais sous forme de tableau
          $tabextension=array("png","jpeg","jpg");

          // On va traiter toutes les images envoyées par l'utilisateur
          for($i=0; $i<$_POST['nombre_images']; $i++)
          {

            if ($_FILES['fichier'.$i]['size'] > $maxtaille) {
              echo 'votre fichier '.$_FILES['fichier'.$i]['name'].' d&eacute;passe la taille maximale.<br>';
              $good = false;
            }

            else
            {

              // On récupère l'extension de notre image uniquement
              $ext = strtolower(substr(strrchr($_FILES['fichier'.$i]['name'], '.'), 1));

              // On vérifie si elle est dans notre tableau d'extensions

              // Si elle n'y est pas
              if (!in_array($ext,$tabextension)) {
                echo "fichier non pris encharge Extension incorrecte";
                $good = false;
              }
            }
          }

          if (!isset($good)) {

            $req= $bdd->prepare("INSERT INTO annonces(type,ville,prix,date_publication,auteur,date_dispo_debut, date_dispo_fin)
                                 VALUES(:type,:ville,:prix,NOW(),:auteur,:datedebut,:datefin)");


            // On exécute la requête avec nos valeurs
            $req->execute(array(
              'type' => $_POST["type"],
              'ville' => $_POST["ville"],
              'prix' => $_POST["prix"],
              'auteur' => $_SESSION["user"],
              'datedebut'=> $_POST["datedebut"],
              'datefin'=> $_POST["datefin"]
            ));

            // On prépare notre requête permettant de récupérer l'ID de notre annonce (qui est en Auto-Incrémentation)
            $req= $bdd->prepare("SELECT id_annonce FROM annonces
                                 WHERE type=:type AND ville=:ville AND prix=:prix AND
                                 auteur=:auteur AND date_dispo_debut=:datedebut AND date_dispo_fin=:datefin");

            // On l'exécute
            $req->execute(array(
              'type' => $_POST["type"],
              'ville' => $_POST["ville"],
              'prix' => $_POST["prix"],
              'auteur' => $_SESSION["user"],
              'datedebut'=> $_POST["datedebut"],
              'datefin'=> $_POST["datefin"]
            ));

            // On met le résultat dans une variable
            $donnees=$req->fetch();

            // On récupère notre ID
            $id = $donnees["id_annonce"];

            // On ferme notre requête
            $req->CloseCursor();

            // On prépare notre requête d'insertion
            $req=$bdd->prepare("INSERT INTO image(nom_image,annonce)
                                VALUES(:nom_image, :annonce)");


            for ($i=0; $i<$_POST['nombre_images']; $i++)
            {
              $ext = strtolower(substr(strrchr($_FILES['fichier'.$i]['name'], '.'), 1));

              // On l'exécute
              $req->execute(array(
                'nom_image' => $i.'.'.$ext,
                'annonce' => $id
              ));

              // On met le path de notre dossier dans une variable
              $dossier = "images/$id";

              // S'il n'existe pas, on le créé
              if (!is_dir($dossier))
                mkdir($dossier, 0777, true);

              // On met maintenant le chemin permettant d'ouvrir notre image (ex : image/14/image.png)
              $dossier = $dossier.'/'.$i.'.'.$ext;

              // On transfère notre image dans notre dossier
              $resultat = move_uploaded_file($_FILES['fichier'.$i]['tmp_name'], $dossier);
            }
          }

          // Une fois que notre annonce a bien été enregistrée, on fait une redirection
          header('Refresh:3;url=annonces.php');
          echo 'Annonce publiée !<br><br>Redirection dans 3 secondes...<br>Trop long ? <a href="annonces.php">Clique ici</a>';
        }
    }
    ?>
  </body>
</html>
