<?php
  session_start();
?>

<html>

  <?php
    require "header.php"
  ?>
    <title> les annonces </title>
  </head>

<body>
  <?php
  if (isset($_FILES["fichier0"]))

  {
    include "register_login/myparam.inc.php";
    try{

    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }
  $req= $bdd->prepare("INSERT INTO annonces(type,ville,prix,date_publication,auteur)
                           VALUES(:type,:ville,:prix,NOW(),:auteur) ");



  // On exécute la requête avec nos valeurs
  $req->execute(array(
 'type' => $_POST["type"],
 'ville' => $_POST["ville"],
 'prix' => $_POST["prix"],
 'auteur' => $_SESSION["user"]
));

  echo $_SESSION['user'].' vous avez ajoutez avec succès une publication de  type '.$_POST['type'].', a  '.$_POST['ville'].' au prix  '.$_POST['prix'].' <br>';
  $maxtaille = 10000000;
  $tabextension=array("png","jpeg","jpg");
  for($i=0;$i<$_POST['nombre_images'];$i++)

{

  if   ($_FILES['fichier'.$i]['size'] > $maxtaille)
       echo 'votre fichier '.$_FILES['fichier'.$i]['name'].' d&eacute;passe la taille maximale.<br>';
  else{
  $ext = strtolower(  substr(  strrchr($_FILES['fichier'.$i]['name'], '.')  ,1)  );
     if (!in_array($ext,$tabextension) ) echo "fichier non pris encharge Extension incorrecte";
     else {
      $req= $bdd->prepare("SELECT id_annonce FROM annonces WHERE type=:type AND ville=:ville AND prix=:prix AND auteur=:auteur");
      $req->execute(array(
     'type' => $_POST["type"],
     'ville' => $_POST["ville"],
     'prix' => $_POST["prix"],
     'auteur' => $_SESSION["user"]
 ));
 $donnees=$req->fetch();
 $id=$donnees["id_annonce"];


 $req->CloseCursor();
    $req=$bdd->prepare("INSERT INTO image(nom_image,annonce)VALUES(:nom_image,:annonce)");
    $req->execute(array(
      'nom_image' => $_FILES['fichier'.$i]['name'],
      'annonce' => $id
    ));
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
