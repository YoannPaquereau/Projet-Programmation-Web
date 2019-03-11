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
  echo $_SESSION['user'].' vous avez ajoutez avec succès un fichier de  type '.$_POST['type'].', a  '.$_POST['ville'].' au prix  '.$_POST['prix'].' <br>';
   echo $_FILES['fichier0']['size'];
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
