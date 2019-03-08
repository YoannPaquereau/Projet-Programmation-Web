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
  <form action="pub_annonces.php" method="post">
  Type: <select name="type">
          <option value="maison">Maison</option>
          <option value="studio">Studio</option>
          <option value="appartement">Appartement</options>
       </select><br>
  Ville: <imput type="text" name="ville" required><br>

  </form>

</body>
</html
