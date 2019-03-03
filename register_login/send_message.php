<?php
  session_start();


  // Inclusion des paramètres de connexion de notre BD
  include_once("myparam.inc.php");



  // On accède à notre base de données
  try
  {
    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8', MYUSER, MYPASS);
  }
  catch (Exception $e)
  {
    die('Erreur : ' . $e->getMessage());
  }

  // On vérifie si le destinataire existe
  $req = $bdd->prepare('SELECT count(*) AS nbr FROM users WHERE login = :login');
  // On exécute la requête avec nos valeurs
  $req->execute(array(
    'login' => $_POST['destinataire']
  ));

  $nbr = $req->fetch();
  $req->CloseCursor();
  if ($nbr == 1) {
    echo 'true';
    $req = $bdd->prepare('INSERT INTO messages_privees(expediteur, destinataire, message, date_envoi)
                          VALUES (:expediteur, :destinataire, :message, NOW())');
    $req->execute(array(
      'expediteur' => $_SESSION['user'],
      'destinataire' => $_POST['destinataire'],
      'message' => $_POST['message']
    ));
  }

 ?>
