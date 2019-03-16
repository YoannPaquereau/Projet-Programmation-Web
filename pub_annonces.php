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
  if (isset($_FILES["fichier0"]))
  {
    include "register_login/myparam.inc.php";

    try
    {
      $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
    }
    catch(Exception $e)
    {
      die('erreur : '.$e->getmessage());
    }

    $req= $bdd->prepare("INSERT INTO annonces(type,ville,prix,date_publication,auteur)
                         VALUES(:type,:ville,:prix,NOW(),:auteur)");


    // On exécute la requête avec nos valeurs
    $req->execute(array(
      'type' => $_POST["type"],
      'ville' => $_POST["ville"],
      'prix' => $_POST["prix"],
      'auteur' => $_SESSION["user"]
    ));

    //echo $_SESSION['user'].' vous avez ajoutez avec succès une publication de  type '.$_POST['type'].', a  '.$_POST['ville'].' au prix  '.$_POST['prix'].' <br>';

    // On déclare une variable contenant la taille maximale acceptée pour une image (en octets)
    $maxtaille = 10000000;

    // Idem pour les extensions mais sous forme de tableau
    $tabextension=array("png","jpeg","jpg");

    // On va traiter toutes les images envoyées par l'utilisateur
    for($i=0;$i<$_POST['nombre_images'];$i++)
    {

      if ($_FILES['fichier'.$i]['size'] > $maxtaille)
        echo 'votre fichier '.$_FILES['fichier'.$i]['name'].' d&eacute;passe la taille maximale.<br>';

      else
      {

        // On récupère l'extension de notre image uniquement
        $ext = strtolower(substr(strrchr($_FILES['fichier'.$i]['name'], '.'), 1));

        // On vérifie si elle est dans notre tableau d'extensions

        // Si elle n'y est pas
        if (!in_array($ext,$tabextension))
          echo "fichier non pris encharge Extension incorrecte";

        // Si elle y est
        else
        {
          // On prépare notre requête permettant de récupérer l'ID de notre annonce (qui est en Auto-Incrémentation)
          $req= $bdd->prepare("SELECT id_annonce FROM annonces WHERE type=:type AND ville=:ville AND prix=:prix AND auteur=:auteur");

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

          // On l'exécute
          $req->execute(array(
            'nom_image' => $_FILES['fichier'.$i]['name'],
            'annonce' => $id
          ));

          $dossier = "images/$id";
          if (!is_dir($dossier)) {
            mkdir($dossier, 0777);
          }

          $dossier = $dossier.'/'.$i.'.'.$ext;
          $resultat = move_uploaded_file($_FILES['fichier'.$i]['tmp_name'], $dossier);
          if ($resultat) echo "Transfert réussi";
       }
      }
    }
  }


  elseif (isset($_POST["type"]) && isset($_POST["ville"]) && isset($_POST["prix"]) && isset($_POST["nombre_images"]))
  {
    $type = $_POST["type"];
    $ville = $_POST["ville"];
    $prix = $_POST["prix"];
    $nbr_images = $_POST["nombre_images"];
    ?>
    <form method="post" action="pub_annonces.php" enctype="multipart/form-data">
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
  else {
    echo "Erreur d'envoi";
  }
