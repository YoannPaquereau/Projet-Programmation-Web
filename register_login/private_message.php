<?php
  session_start();
?>
<html>
  <?php
    require "../header.php"
  ?>
  <title>Messages priv&eacute;s</title>
  </head>

  <body>
    <p>
      <?php
      if (!isset($_SESSION['user'])) echo "Page not found";
      else {

        require "myparam.inc.php";


        // On accède à notre base de données
        try
        {
          $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8', MYUSER, MYPASS);
        }
        catch (Exception $e)
        {
          die('Erreur : ' . $e->getMessage());
        }

        // Si on se trouve dans un sujet de message
        if (isset($_GET['sujet']) && isset($_GET['destinataire'])) {
          $sujet = $_GET['sujet'];
          $destinataire = $_GET['destinataire'];
        }



        // On teste si on a envoyé un message
        if (isset($_POST['Envoyer'])) {

          // On vérifie si le destinataire existe
          $req = $bdd->prepare('SELECT count(*) AS nbr FROM users WHERE login = :login');

          // On attribut nos variables en fonction de la page où l'on est
          // Si on est dans un sujet
          if (isset($destinataire)) {
            $des = $destinataire;
            $suj = $sujet;
          }

          // Si on est dans le menu principal des messages
          else {
            $des = $_POST['destinataire'];
            $suj = $_POST['titre'];
          }

          // On exécute la requête avec nos valeurs
          $req->execute(array(
            'login' => $des
          ));

          // On met le résultat de notre COUNT de notre requête dans une variable
          $nbr = $req->fetch();

          // On ferme notre requête
          $req->CloseCursor();

          // On vérifie si notre COUNT est à 1 (== le destinataire existe)
          if ($nbr['nbr'] == 1) {

            // On prépare notre requête d'insertion
            $req = $bdd->prepare('INSERT INTO messages_prives(expediteur, destinataire, titre, message, date_envoi)
                                  VALUES (:expediteur, :destinataire, :titre, :message, NOW())');

            // On l'exécute avec nos valeurs passées dans le formulaire, ou celles passées en GET (dans l'URL)
            $req->execute(array(
              'expediteur' => $_SESSION['user'],
              'destinataire' => $des,
              'titre' => $suj,
              'message' => $_POST['message']
            ));
          }
        }


        // On vérifie si on est dans une discution avec un autre utilisateur
        if (isset($sujet)) {

          // On permet de revenir au menu des messages privés
          echo '<a href="private_message.php">Retour aux messages</a>';

          // On affiche le titre de la discussion
          echo '<h3> Titre : '.$sujet.'</h3><br /><br />';

          // On prépare la requête permetant de récupérer tous les messages de notre discution
          $req = $bdd->prepare('SELECT expediteur, destinataire, message, date_envoi
                                FROM messages_prives
                                WHERE titre = :titre
                                AND (expediteur = :expediteur OR destinataire = :destinataire)
                                ORDER BY date_envoi');

          // On l'exécute avec nos valeurs
          $req->execute(array(
            'titre' => $sujet,
            'expediteur' => $_SESSION['user'],
            'destinataire' => $_SESSION['user']
          ));

          // Et on les affiches
          while ($donnees = $req->fetch()) {
              echo $donnees['expediteur'].'<br />'.$donnees['date_envoi'].'<br /><br />'.$donnees['message'].'<br /><br />';
          }
        }


        // Si on n'est pas dans une discussion
        else {
          echo "<h2>Messages priv&eacute;s</h2>";

          // On prépare notre requête permettant de récupérer le dernier message envoyé de chaque discussion
          $req = $bdd->prepare('SELECT expediteur, destinataire, titre, message, date_envoi
                                FROM messages_prives
                                WHERE (expediteur = :expediteur OR destinataire = :destinataire)
                                AND date_envoi IN
                                (select MAX(date_envoi) FROM messages_prives GROUP BY titre ORDER BY MAX(date_envoi) DESC) ORDER BY date_envoi DESC');

          // On exécute la requête avec nos valeurs
          $req->execute(array(
            'expediteur' => $_SESSION['user'],
            'destinataire' => $_SESSION['user']
          ));

          // On affiche nos valeurs
          while ($donnees = $req->fetch()) {

            // Si on est l'expéditeur, on affiche le destinataire
            if ($donnees['expediteur'] == $_SESSION['user'])
              echo 'Titre : <a href="private_message.php?sujet='.$donnees['titre'].'&amp;destinataire='.$donnees['destinataire'].'">'.$donnees['titre'].'</a><br />'
              .$donnees['date_envoi'].'<br/>
              Envoy&eacute; &agrave; '.$donnees['destinataire'].'<br />'
              .$donnees['message'].'<br /><br /><br />';

            // Si on est le destinataire, on affiche l'expéditeur
            else
              echo 'Titre : <a href="private_message.php?sujet='.$donnees['titre'].'&amp;destinataire='.$donnees['expediteur'].'">'.$donnees['titre'].'</a><br />'
              .$donnees['date_envoi'].'<br/>
              De '.$donnees['expediteur'].'<br />'
              .$donnees['message'].'<br /><br /><br />';
          }

          // On ferme notre requête
          $req->CloseCursor();
      }

        // On créé notre formulaire permettant l'envoi d'un nouveau message
        ?>
        <h3>Nouveau message</h3>
        <?php

        // Si on est dans une discution
        if (isset($sujet) && isset($destinataire)) { ?>
          <form action="<?php echo 'private_message.php?sujet='.$sujet.'&amp;destinataire='.$destinataire;?>" method="post">
        <?php }

        else { ?>
          <form action="private_message.php" method="post">
          Destinataire : <input type="text" <?php if (isset($_GET['for'])) echo 'value="'.$_GET['for'].'"'; ?>name="destinataire" required><br />
          Titre : <input type="text" name="titre" required><br /><br />
        <?php }?>
          <textarea name="message" rows="8" cols="45"></textarea>
          <input type="submit" name="Envoyer">
        </form>

        <?php
      }
      ?>
  </body>
</html>
