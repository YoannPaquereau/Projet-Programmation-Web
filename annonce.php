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
  <h1>Annonces</h1>
  <p>
    <form action="pub_annonces.php" method="post">
    Type: <select name="type">
            <option value="maison">Maison</option>
            <option value="studio">Studio</option>
            <option value="appartement">Appartement</option>
         </select><br>
    Ville: <input type="text" name="ville" required><br>
    Prix: <input type="number" name="prix" placeholder="Prix" step="0.01" min="20"  required><br>
    Nombre image : <input type="number" name="nombre_images" min="1" max="8" required><br>
    <input type="submit" value="Envoyer">

    </form>
  </p>

</body>
</html
