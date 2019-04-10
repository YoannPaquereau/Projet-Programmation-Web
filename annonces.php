<?php
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
    require "register_login/myparam.inc.php";



    function estDispo($id_annonce, $dateDebut, $dateFin) {
      try
      {
        $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
      }
      catch(Exception $e)
      {
        die('erreur : '.$e->getmessage());
      }

      $date_traite = $dateDebut;
      $recherche_resa = "SELECT annonce FROM reservation WHERE '$date_traite' BETWEEN date_debut AND date_fin";
      while ($date_traite < $dateFin) {
        $date_traite = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_traite)) . " +1 day"));
        $recherche_resa = "$recherche_resa OR '$date_traite' BETWEEN date_debut AND date_fin";
      }

      $req = $bdd->prepare("SELECT COUNT(*) AS nbr FROM annonces
                            WHERE id_annonce = :id
                            AND date_dispo_debut <= :recherche_datedebut
                            AND date_dispo_fin >= :recherche_datefin AND id_annonce IN ($recherche_resa)");

      $req->execute(array(
        'id' => $id_annonce,
        'recherche_datedebut' => $dateDebut,
        'recherche_datefin' => $dateFin
      ));

      $donnees = $req->fetch();
      $req->closeCursor();

      if ($donnees['nbr'] == 0) return true;
      else return false;
    }


    function afficheAnnonces($donnees) {
      try
      {
        $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
      }
      catch(Exception $e)
      {
        die('erreur : '.$e->getmessage());
      }

        if (isset($_POST['recherche_datedebut'])) echo '<li><a href="?annonce='.$donnees['id_annonce'].'&datedebut='.$_POST['recherche_datedebut'].'&datefin='.$_POST['recherche_datefin'].'">';
        else echo '<li><a href="?annonce='.$donnees['id_annonce'].'">';
        echo 'Type : '.$donnees['type'];
        echo '<br>Ville :'.$donnees['ville'];
        echo '<br>Prix : '.$donnees['prix'].'€';
        echo '<br>Auteur : '.$donnees['auteur'];
        $id_annonce = $donnees['id_annonce'];
        $req2 = $bdd->prepare('SELECT nom_image FROM image WHERE annonce=:id_annonce');
        $req2->execute(array(
          'id_annonce' => $id_annonce
        ));
        $donnees2=$req2->fetch();
        $image ='images/'.$id_annonce.'/'.$donnees2['nom_image'];
        echo '<br><img src="'.$image.'"width="400" height="200"><br>';
        echo '</a></li>';
    }



    if (isset($_GET['annonce'])) {
      try
      {
        $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
      }
      catch(Exception $e)
      {
        die('erreur : '.$e->getmessage());
      }


      if (isset($_POST['resa_debut'])) {
        if (estDispo($_GET['annonce'], $_POST['resa_debut'], $_POST['resa_fin'])) {
          $req = $bdd->prepare('INSERT INTO reservation(client, auteur, date_debut, date_fin, annonce)
                                VALUES (:client, :auteur, :date_debut, :date_fin, :annonce)');

          $req->execute(array(
            'client' => $_SESSION['user'],
            'auteur' => $_POST['resa_auteur'],
            'date_debut' => $_POST['resa_debut'],
            'date_fin' => $_POST['resa_fin'],
            'annonce' => $_GET['annonce']
          ));

          header('Refresh:3;url=annonces.php');
          echo 'Annonce r&eacute;serv&eacute;e !<br><br>Redirection dans 3 secondes...<br>Trop long ? <a href="annonces.php">Clique ici</a>';
        } else {
          header('Refresh:3;url=annonces.php?annonce='.$_GET['annonce']);
          echo "Date de r&eacute;servation non valide. Veuillez r&eacuteessayer";
        }
      }
      else {


        $req = $bdd->prepare('SELECT * FROM annonces WHERE id_annonce=:id');
        $req->execute(array(
          'id' => $_GET['annonce']
        ));

        $donnees = $req->fetch();
        $auteur = $donnees['auteur'];

        echo 'Type : '.$donnees['type'];
        echo '<br>Ville :'.$donnees['ville'];
        echo '<br>Prix : '.$donnees['prix'].'€';
        echo '<br>Auteur : '.$donnees['auteur'].' (<a href="/Projet/register_login/private_message.php">Contacter l\'auteur</a>)';

        $req->CloseCursor();
        $req = $bdd->prepare('SELECT nom_image FROM image WHERE annonce = :id');

        $req->execute(array(
          'id' => $_GET['annonce']
        ));


        $dossier = 'images/'.$_GET['annonce'].'/';
        echo '<ul class="annonce">';
        while($donnees = $req->fetch()) {
          echo '<li><img src="'.$dossier.$donnees['nom_image'].'"width="400" height="200"></li>';
        }
        echo '</ul>';

        $req->CloseCursor();

        ?>
        <h2>Réserver</h2>
        <form action="annonces.php?annonce=<?php echo $_GET['annonce'];?>" method="post" class="reservation">
          Date de d&eacute;but : <input type="date" name="resa_debut" value="<?php echo (isset($_GET['datedebut'])) ? $_GET['datedebut'] : $datemin;?>" required><br>
          Date de fin : <input type="date" name="resa_fin" value="<?php echo (isset($_GET['datefin'])) ? $_GET['datefin'] : $datemin;?>" required><br>
          <input type="hidden" name="resa_auteur" value="<?php echo $auteur;?>">
          <input type="submit" value="Réserver">
        </form>
    <?php } }


    elseif (!isset($_POST["type"]) || !isset($_POST["ville"]) || !isset($_POST["prix"]) || !isset($_POST["nombre_images"])) {
      $datemin= date("Y-m-d");
      $datemax= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 year"));
      ?>
      <p>
        <?php
          if (isset($_POST['recherche_ville'])) {
            $ville = $_POST['recherche_ville'];
            $prix = $_POST['recherche_prix'];
            $type = $_POST['recherche_type'];
            $datedebut = $_POST['recherche_datedebut'];
            $datefin = $_POST['recherche_datefin'];
          } ?>

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
          <input type ="date" name="recherche_datedebut" value="<?php echo (isset($datedebut) ? $datedebut : $datemin);?>" min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required>
          <input type ="date" name="recherche_datefin" value="<?php echo (isset($datefin) ? $datefin : $datemin);?>"    min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required>
          <input type="submit" name="submit" value="Rechercher">
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

        if (isset($_POST['recherche_prix'])) {


          $req = $bdd->prepare("SELECT * FROM annonces
                                WHERE prix <=:prix
                                AND type = :type
                                AND ville = :ville
                                AND date_dispo_debut <= :recherche_datedebut
                                AND date_dispo_fin >= :recherche_datefin");

          $req->execute(array(
            'prix' => $_POST['recherche_prix'],
            'ville' => $_POST['recherche_ville'],
            'type' => $_POST['recherche_type'],
            'recherche_datedebut' => $_POST['recherche_datedebut'],
            'recherche_datefin' => $_POST['recherche_datefin']
          ));

          echo '<ul class="liste_annonces">';
          while ($donnees = $req->fetch()) {
            if (estDispo($donnees['id_annonce'], $_POST['recherche_datedebut'], $_POST['recherche_datefin'])) {
              afficheAnnonces($donnees);
            }
          }
          echo "</ul></br></br>";
          $req->CloseCursor();

        }

        else {
          $req = $bdd->prepare('SELECT * FROM annonces');

          $req->execute();


          echo '<ul class="liste_annonces">';
          while ($donnees = $req->fetch()) {
            afficheAnnonces($donnees);
          }
          echo '</ul><br><br>';
        }




        $datemin2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 day"));
        $datemax2= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemax)) . " +1 day"));
        ?>


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

          Date de debut: <input type ="date" name="datedebut" min="<?php echo $datemin;?>" max="<?php echo $datemax;?>" required><br>

          Date de fin: <input type ="date" name="datefin"min="<?php echo $datemin2;?>" max="<?php echo $datemax2;?>" required><br>
          Nombre image(s): <input type="number" name="nombre_images" min="1" max="8" required><br>
          <input type="submit" value="Envoyer">
        </form>
      </p>

      <?php
    }



    elseif (!isset($_FILES['fichier0']))
    {
      $datedebut= $_POST["datedebut"];
      $datefin= $_POST["datefin"];

      if ((strtotime($datedebut) >= strtotime($datefin))) echo("ERREUR,Date de fin antérieur à la date de debut") ;
      else{

      $type = $_POST["type"];
      $ville = $_POST["ville"];
      $prix = $_POST["prix"];
      $nbr_images = $_POST["nombre_images"];


      ?>
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

          $dossier = "images/$id";
          if (!is_dir($dossier))
            mkdir($dossier, 0777, true);

          $dossier = $dossier.'/'.$i.'.'.$ext;
          $resultat = move_uploaded_file($_FILES['fichier'.$i]['tmp_name'], $dossier);
        }
      }
      header('Refresh:3;url=annonces.php');
      echo 'Annonce publiée !<br><br>Redirection dans 3 secondes...<br>Trop long ? <a href="annonces.php">Clique ici</a>';
    }
    ?>
  </body>
</html>
