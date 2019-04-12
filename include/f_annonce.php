<?php
function estDispo($id_annonce, $dateDebut, $dateFin) {
  try
  {
    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }

  $date_traite = $dateDebut;
  $recherche_resa = "SELECT annonce FROM reservation WHERE '$date_traite' BETWEEN date_debut AND date_fin";
  while ($date_traite < $dateFin) {
    $date_traite = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_traite)) . " +1 day"));
    $recherche_resa = "$recherche_resa OR '$date_traite' BETWEEN date_debut AND date_fin";
  }

  $req = $bdd->prepare("SELECT COUNT(*) AS nbr FROM annonces
                        WHERE id_annonce = :id
                        AND date_dispo_debut <= :recherche_datedebut
                        AND date_dispo_fin >= :recherche_datefin AND id_annonce IN ($recherche_resa)");

  $req->execute(array(
    'id' => $id_annonce,
    'recherche_datedebut' => $dateDebut,
    'recherche_datefin' => $dateFin
  ));

  $donnees = $req->fetch();
  $req->closeCursor();

  if ($donnees['nbr'] == 0) return true;
  else return false;
}


function afficheInfosAnnonces($donnees) {
  try
  {
    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }

    if (isset($_POST['recherche_datedebut'])) echo '<li><a href="?annonce='.$donnees['id_annonce'].'&datedebut='.$_POST['recherche_datedebut'].'&datefin='.$_POST['recherche_datefin'].'">';
    else echo '<li><a href="/Projet/annonces.php?annonce='.$donnees['id_annonce'].'">';
    echo 'Type : '.$donnees['type'];
    echo '<br>Ville :'.$donnees['ville'];
    echo '<br>Prix : '.$donnees['prix'].'€';
    echo '<br>Auteur : '.$donnees['auteur'];
    $id_annonce = $donnees['id_annonce'];
    $req2 = $bdd->prepare('SELECT nom_image FROM image WHERE annonce=:id_annonce');
    $req2->execute(array(
      'id_annonce' => $id_annonce
    ));
    $donnees2=$req2->fetch();
    $image ='/Projet/images/'.$id_annonce.'/'.$donnees2['nom_image'];
    echo '<br><img src="'.$image.'"width="400" height="200"><br>';
    echo '</a></li>';
}

  function afficheResaAnnonces($donnees) {
    try
    {
      $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
    }
    catch(Exception $e)
    {
      die('erreur : '.$e->getmessage());
    }

    echo '<li><a href="/Projet/annonces.php?annonce='.$donnees['id_annonce'].'">';
    echo 'Date de d&eacute : '.$donnees['date_debut'].'<br>';
    echo 'Date de fin '.$donnees['date_fin'].'<br>';

  }
?>
