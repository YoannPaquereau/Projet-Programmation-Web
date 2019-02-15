<?php

  $user = $_POST['user'];
  $password = $_POST['password'];

  try
  {
    $bdd = new PDO('mysql:host=localhost;dbname=projet;charset=utf8', 'root', '');
  }
  catch (Exception $e)
  {
    die('Erreur : ' . $e->getMessage());
  }

  $req = $bdd->prepare('INSERT INTO Users(Login, Password) VALUES (:Login, :Password)');
  $req->execute(array(
    'Login' => $user,
    'Password' => $password
  ));

  echo "Inscription de $user réussie";
?>
