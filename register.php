<?php

  // On met les valeurs rentrées dans notre formulaire dans des variables (pas obligatoire)
  $user = $_POST['user'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hashage du mot de passe, pour pas à l'avoir en clair dans notre BD


  // On accède à notre base de données
  try
  {
    $bdd = new PDO('mysql:host=localhost;dbname=projet;charset=utf8', 'root', '');
  }
  catch (Exception $e)
  {
    die('Erreur : ' . $e->getMessage());
  }

  // On prépare la requête d'insertion
  $req = $bdd->prepare('INSERT INTO users(login, password) VALUES (:login, :password)');
  // On exécute la requête avec nos valeurs
  $req->execute(array(
    'login' => $user,
    'password' => $password
  ));

  echo "Inscription de $user réussie<br>$password";
?>
