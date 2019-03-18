<?php
  session_start();
?>

<html>

  <?php
    require "header.php"
  ?>
    <title>Annonces</title>
  </head>

  <body>

    <?php
    include "register_login/myparam.inc.php";

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
    while ($donnees = $req->fetch()) {
      echo 'Type : '.$donnees['type'];
      echo '<br>Prix : '.$donnees['prix'].'€';
      echo '<br>date_envoi : '.$donnees['date_publication'];
      echo '<br>Auteur : '.$donnees['auteur'];
      $id_annonce = $donnees['id_annonce'];
      $req2 = $bdd->prepare('SELECT nom_image FROM image WHERE annonce=:id_annonce');
      $req2->execute(array(
        'id_annonce' => $id_annonce
      ));
    while ($donnees2=$req2->fetch()){
      $image ='images/'.$id_annonce.'/'.$donnees2['nom_image'];
      echo '<br><img src="'.$image.'">';
    }



    }








    if (!isset($_POST["type"]) || !isset($_POST["ville"]) || !isset($_POST["prix"]) || !isset($_POST["nombre_images"])) { ?>
      <h1>Annonces</h1>
      <p>
        <form action="annonces.php" method="post">
          Type: <select name="type">
                  <option value="maison">Maison</option>
                  <option value="studio">Studio</option>
                  <option value="appartement">Appartement</option>
               </select><br>
          Ville: <input type="text" name="ville" required><br>
          Prix: <input type="number" name="prix" placeholder="Prix" step="0.01" min="20"  required><br>
          Nombre image(s): <input type="number" name="nombre_images" min="1" max="8" required><br>
          <input type="submit" value="Envoyer">
        </form>
      </p>

      <?php
    }



    elseif (!isset($_FILES['fichier0']))
    {
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
        <input type="hidden" name="nombre_images" value="<?php echo $nbr_images; ?>">
        <input type="submit" value="Envoyer">

    <?php
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

        $req= $bdd->prepare("INSERT INTO annonces(type,ville,prix,date_publication,auteur)
                             VALUES(:type,:ville,:prix,NOW(),:auteur)");


        // On exécute la requête avec nos valeurs
        $req->execute(array(
          'type' => $_POST["type"],
          'ville' => $_POST["ville"],
          'prix' => $_POST["prix"],
          'auteur' => $_SESSION["user"]
        ));

        // On prépare notre requête permettant de récupérer l'ID de notre annonce (qui est en Auto-Incrémentation)
        $req= $bdd->prepare("SELECT id_annonce FROM annonces
                             WHERE type=:type AND ville=:ville AND prix=:prix AND auteur=:auteur");

        // On l'exécute
        $req->execute(array(
          'type' => $_POST["type"],
          'ville' => $_POST["ville"],
          'prix' => $_POST["prix"],
          'auteur' => $_SESSION["user"]
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
