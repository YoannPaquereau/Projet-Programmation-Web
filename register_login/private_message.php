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
      <h3>Nouveau message</h3>
      <form action="send_message.php" method="post">
        Destinataire : <input type="text" name="destinataire" required><br /><br />
        <textarea name="message" rows="8" cols="45"></textarea>
        <input type="submit" name="Envoyer">
      </form>

<!-- Fusionner avec le fichier send_message.php -->
