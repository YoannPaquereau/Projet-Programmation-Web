<?php



// Fonction permetant de savoir si une annonce est disponible pour une réservation
function estDispo($id_annonce, $dateDebut, $dateFin) {
  try
  {
    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }

  // On met la date de début de la réservation souhaitée
  $date_traite = $dateDebut;

  // On créé le début de notre requête permettant de comparer nos dates de réservation
  $recherche_resa = "SELECT annonce FROM reservation WHERE '$date_traite' BETWEEN date_debut AND date_fin";

  // Tant qu'on arrive pas à la date de fin
  while ($date_traite < $dateFin) {

    // On incrémente notre date traitée de 1 jour
    $date_traite = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_traite)) . " +1 day"));

    // On vérifie que cette date ne se trouve pas déjà dans un intervalle
    $recherche_resa = "$recherche_resa OR '$date_traite' BETWEEN date_debut AND date_fin";
  }

  // On prépare notre requête entière
  $req = $bdd->prepare("SELECT COUNT(*) AS nbr FROM annonces
                        WHERE id_annonce = :id
                        AND date_dispo_debut <= :recherche_datedebut
                        AND date_dispo_fin >= :recherche_datefin AND id_annonce IN ($recherche_resa)");

  // On l'exécute avec nos variables
  $req->execute(array(
    'id' => $id_annonce,
    'recherche_datedebut' => $dateDebut,
    'recherche_datefin' => $dateFin
  ));

  // On récupère le résultat de notre requête
  $donnees = $req->fetch();

  // On la ferme
  $req->closeCursor();

  if ($donnees['nbr'] == 0) return true;    // Si le count nous retourne 0 (qu'aucune date de notre plage de réservation n'est dans notre base de données)
  else return false;    // sinon c'est réservé
}




// Fonction permettant d'afficher les détails d'une annonce (Prix, localisation,...)
function afficheInfosAnnonces($donnees) {
  try
  {
    $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
  }
  catch(Exception $e)
  {
    die('erreur : '.$e->getmessage());
  }

    // Si on utilise la recherche d'annonces (dans la page annonces.php)
    if (isset($_POST['recherche_datedebut'])) echo '<li><a href="?annonce='.$donnees['id_annonce'].'&datedebut='.$_POST['recherche_datedebut'].'&datefin='.$_POST['recherche_datefin'].'">';

    else echo '<li><a href="/Projet/annonces.php?annonce='.$donnees['id_annonce'].'">';

    // On affiche les données de notre annonces
    echo 'Type : '.$donnees['type'];
    echo '<br>Ville :'.$donnees['ville'];
    echo '<br>Prix : '.$donnees['prix'].'€';
    echo '<br>Auteur : '.$donnees['auteur'];

    $id_annonce = $donnees['id_annonce'];

    // On prépare notre requête permettant de récupérer le nom de la 1ère image de notre annonce
    $req2 = $bdd->prepare('SELECT nom_image FROM image WHERE annonce=:id_annonce LIMIT 0,1');

    // On l'exécute (avec l'id de notre annonce)
    $req2->execute(array(
      'id_annonce' => $id_annonce
    ));

    // On récupère les données de notre requête
    $donnees2=$req2->fetch();

    // On met le chemin pour ouvrir notre image
    $image ='/Projet/images/'.$id_annonce.'/'.$donnees2['nom_image'];

    // On l'affiche
    echo '<br><img src="'.$image.'"width="400" height="200"><br>';
    echo '</a></li>';
}




  // Fonction permettant d'afficher les détails d'une réservation
  function afficheResaAnnonces($donnees) {
    try
    {
      $bdd = new PDO('mysql:host='.MYHOST.';dbname='.MYBASE.';charset=utf8',MYUSER,MYPASS);
    }
    catch(Exception $e)
    {
      die('erreur : '.$e->getmessage());
    }

    // Lien permettant de voir les détails de l'annonce réservée
    echo '<li><a href="/Projet/annonces.php?annonce='.$donnees['id_annonce'].'">';

    // Détails de la réservation
    echo 'Date de d&eacute : '.$donnees['date_debut'].'<br>';
    echo 'Date de fin '.$donnees['date_fin'].'<br>';

  }
?>
