<?php

  $user = $_POST['user'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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
