<?php
// Informations de connexion à la base de données
$serveur = 'localhost';
$utilisateur = 'root';
$motdepasse = '';
$basededonnees = 'logescom';

// Connexion à la base de données
$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);

// Vérifier la connexion
if ($connexion->connect_error) {
    die('Erreur de connexion à la base de données : ' . $connexion->connect_error);
}

// Récupérer le chemin du répertoire de sauvegarde
$repertoireSauvegarde = 'C:\Users\damad\Desktop\formation/'; // Remplacez par le chemin de votre choix

// Nom du fichier de sauvegarde
$nomFichier = 'dump.sql';

// Chemin complet du fichier de sauvegarde
$cheminFichier = $repertoireSauvegarde . $nomFichier;

// Exécution de la commande de dump
$commandeDump = 'mysqldump --host=' . $serveur . ' --user=' . $utilisateur . ' --password=' . $motdepasse . ' ' . $basededonnees . ' > ' . $cheminFichier;
system($commandeDump, $resultat);

// Vérifier si la commande s'est exécutée avec succès
if ($resultat === false) {
    die('Erreur lors de la sauvegarde de la base de données : ' . $resultat);
}

echo 'La base de données a été sauvegardée avec succès dans le fichier ' . $cheminFichier;

// Fermer la connexion à la base de données
$connexion->close();
?>
