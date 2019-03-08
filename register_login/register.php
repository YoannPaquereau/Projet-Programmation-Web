<?php
  session_start();
 ?>

<html>
  <?php
    require "../header.php";
  ?>
    <title>AirB&B</title>
  </head>
  <body>
    <p>
      <?php
      if (!isset($_SESSION['user'])) { ?>
      <form action="register.php" method="post" class="form_register">
        <label for="login">Nom d'utilisateur :</label><input type="text" name="user" required><br>
        <label for="paassword">Mot de passe :</label><input type="password" name="password" required><br>
        <label for="nom">Nom :</label><input type="text" name="last_name" required><br>
        <label for="prenom">Pr&eacute;nom :</label><input type="text" name="first_name" required><br>
        <label for="date_naissance">Date de naissance :</label><input type="date" name="date" required><br><br>
        <input type="submit" value="S'inscrire" id="submit">
      </form>


      <?php
        if (isset($_POST['user']) && isset($_POST['password'])) {
          // On met les valeurs rentrées dans notre formulaire dans des variables (pas obligatoire)
          $user = $_POST['user'];
          $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hashage du mot de passe, pour pas à l'avoir en clair dans notre BD
          $nom = $_POST['last_name'];
          $prenom = $_POST['first_name'];
          $date_naissance = $_POST['date'];


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

          // On prépare la requête d'insertion
          $req = $bdd->prepare('INSERT INTO users(login, password, nom, prenom, date_naissance, date_inscription)
                                VALUES (:login, :password, :nom, :prenom, :date_naissance, NOW() )');
          // On exécute la requête avec nos valeurs
          $req->execute(array(
            'login' => $user,
            'password' => $password,
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $date_naissance
          ));

          echo "Inscription de $user réussie<br><a href=\"login.php\">Se connecter</a>";
        }
      }
      else echo "Error<br />Page not found";
      ?>

    </p>
  </body>
</html>
