<?php
  session_start();
?>

<html>
    <?php
      require "../header.php";
    ?>
    <title>ShareMyHouse - Connexion</title>
  </head>
  <body>

    <p>
      <form action="login.php" method="post">
        Nom d'utilisateur : <input type="text" name="user" required><br>
        Mot de passe : <input type="password" name="password" required><br>
        <input type="submit" value="Connexion">
      </form>


      <?php
        if (isset($_POST['user']) && isset($_POST['password'])) {
          // On met les valeurs rentrées dans notre formulaire dans des variables (pas obligatoire)
          $user = $_POST['user'];
          $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hashage du mot de passe, pour pas à l'avoir en clair dans notre BD

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

          $message = '';

          // On prépare la requête d'insertion
          $req = $bdd->prepare('SELECT login, password FROM users WHERE login = :login');
          // On exécute la requête avec nos valeurs
          $req->execute(array(
            'login' => $user,
          ));
          // On met le résultat de notre requête dans une variable
          $resultat = $req->fetch();

          // On vérifie si le mot de passe passé dans le formulaire est indentique à celui de la BD
          $isPasswordCorrect = password_verify($_POST['password'], $resultat['password']);

          // On ferme la requête
          $req->CloseCursor();

          // Si le mot de passe et/ou le nom d'utilisateur est incorrect
          if (!$isPasswordCorrect) $message = 'Mauvais identifiant ou mot de passe !';

          // Sinon on se connecte
          else {
            $_SESSION['user'] = $user;

            // On récupère la date de la dernière connexion avant de la mettre à jour
            $req = $bdd->prepare('SELECT DATE_FORMAT(derniere_connexion,  \'%d/%m/%Y, à %Hh%i\') AS derniere_connexion FROM users WHERE login = :login');
            // On exécute la requête avec nos valeurs
            $req->execute(array(
              'login' => $user,
            ));
            // On met le résultat de notre requête dans une variable
            $resultat = $req->fetch();
            $_SESSION['last_connection'] = $resultat['derniere_connexion'];
            $req->CloseCursor();

            // On met à jour la date de la dernière connexion dans notre BD
            $req = $bdd->prepare('UPDATE users SET derniere_connexion = NOW() WHERE login = :login');
            $req->execute(array(
              'login' => $user
            ));
            header('Location: /Projet/index.php');
          }



          echo $message;
        }
      ?>

    </p>
  </body>
</html>
