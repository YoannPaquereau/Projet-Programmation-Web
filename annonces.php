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

    if (isset($_GET['annonce'])) {
      try
      {
        $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
      }
      catch(Exception $e)
      {
        die('erreur : '.$e->getmessage());
      }

      $req = $bdd->prepare('SELECT * FROM annonces WHERE id_annonce=:id');
      $req->execute(array(
        'id' => $_GET['annonce']
      ));

      $donnees = $req->fetch();

      echo 'Type : '.$donnees['type'];
      echo '<br>Ville :'.$donnees['ville'];
      echo '<br>Prix : '.$donnees['prix'].'€';
      echo '<br>date_envoi : '.$donnees['date_publication'];
      echo '<br>Auteur : '.$donnees['auteur'];

      $req->CloseCursor();
      $req = $bdd->prepare('SELECT nom_image FROM image WHERE annonce = :id');

      $req->execute(array(
        'id' => $_GET['annonce']
      ));


      $dossier = 'images/'.$_GET['annonce'].'/';
      echo '<ul>';
      while($donnees = $req->fetch()) {
        echo '<li><img src="'.$dossier.$donnees['nom_image'].'"width="400" height="200"></li>';
      }
      echo '</ul>';





      $req->CloseCursor();
    }


    elseif (!isset($_POST["type"]) || !isset($_POST["ville"]) || !isset($_POST["prix"]) || !isset($_POST["nombre_images"])) { ?>
      <p>
        <?php
        try
        {
          $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
        }
        catch(Exception $e)
        {
          die('erreur : '.$e->getmessage());
        }

        $req = $bdd->prepare('SELECT * FROM annonces');
        $req->execute();

        echo '<ul class="liste_annonces">';
        while ($donnees = $req->fetch()) {
          echo '<li><a href="?annonce='.$donnees['id_annonce'].'">';
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
        echo '</ul><br><br>';

        $datemin= date("Y-m-d");
        $datemax= date("Y-m-d", strtotime(date("Y-m-d", strtotime($datemin)) . " +1 year"));

        echo date("Y-m-d", strtotime($datemax));

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
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        <input type="hidden" name="ville" value="<?php echo $ville; ?>">
        <input type="hidden" name="prix" value="<?php echo $prix; ?>">
        <input type="hidden" name="datedebut" value="<?php echo $datedebut;?>">
        <input type ="hidden" name="datefin" value="<?php echo $datefin;?>">
        <input type="hidden" name="nombre_images" value="<?php echo $nbr_images; ?>">
        <input type="submit" value="Envoyer">

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
