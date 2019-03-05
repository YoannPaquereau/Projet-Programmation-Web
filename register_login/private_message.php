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
      <h2>Messages priv&eacute;s</h2>



      <!-- Lister tous les messages privés de l'utilisateur -->
      <?php

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

      setlocale(LC_TIME, "fr_FR");
      $req = $bdd->prepare('SELECT expediteur, destinataire, DATE_FORMAT(date_envoi, \'%d/%m/%Y à %H:%i:%s\') AS date_envoi, message FROM messages_prives WHERE expediteur = :expediteur OR destinataire = :destinataire ORDER BY date_envoi DESC');
      // On exécute la requête avec nos valeurs
      $req->execute(array(
        'expediteur' => $_SESSION['user'],
        'destinataire' => $_SESSION['user']
      ));

      while ($donnees = $req->fetch()) {
        if ($donnees['expediteur'] == $_SESSION['user'])
          echo $donnees['date_envoi'].'<br/>Envoy&eacute; &agrave; '.$donnees['destinataire'].'<br />'.$donnees['message'].'<br /><br /><br />';
        else
          echo $donnees['date_envoi'].'<br/>De '.$donnees['destinataire'].'<br />'.$donnees['message'].'<br /><br /><br />';
      }

      $req->CloseCursor();
      ?>









      <h3>Nouveau message</h3>
      <form action="send_message.php" method="post">
        Destinataire : <input type="text" name="destinataire" required><br /><br />
        <textarea name="message" rows="8" cols="45"></textarea>
        <input type="submit" name="Envoyer">
      </form>

<!-- Fusionner avec le fichier send_message.php -->
