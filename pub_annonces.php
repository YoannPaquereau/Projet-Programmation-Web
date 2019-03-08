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
  if (isset($_POST["fichier0"]))

  {
    include "myparam.inc.php";
    try{

    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }
  $requete= $bdd->prepare("INSERT INTO annonces()")












  elseif (isset($_POST["type"]) && isset($_POST["ville"]) && isset($_POST["prix"]) && isset($_POST["nombre_images"]))
  {
    $type = $_POST["type"];
    $ville = $_POST["ville"];
    $prix = $_POST["prix"];
    $nbr_images = $_POST["nombre_images"];
    ?>
    <form action="pub_annonces.php" method="post">
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
