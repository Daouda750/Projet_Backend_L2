<?php
// ============================================================
// db.php - Connexion à la base de données avec PDO
// Ce fichier est inclus dans toutes les pages qui ont besoin de la BDD
// Étudiant 1
// ============================================================

// --- Paramètres de connexion ---
// Tu modifies ces valeurs selon ta configuration locale (WAMP, XAMPP, etc.)
$hote = "localhost";          // Adresse du serveur MySQL (presque toujours localhost)
$nom_bdd = "site_actualite";  // Nom de la base de données
$utilisateur = "root";        // Nom d'utilisateur MySQL (root par défaut sur WAMP/XAMPP)
$mot_de_passe_bdd = "";       // Mot de passe MySQL (vide par défaut sur WAMP/XAMPP)

// --- Tentative de connexion ---
// On met le code dans un try/catch pour gérer les erreurs proprement
try {
    // On crée l'objet PDO avec le DSN (Data Source Name)
    // Le DSN contient le type de BDD, l'hôte, le nom de la BDD et l'encodage
    $pdo = new PDO(
        "mysql:host=$hote;dbname=$nom_bdd;charset=utf8",
        $utilisateur,
        $mot_de_passe_bdd
    );

    // Cette ligne dit à PDO de lancer une exception en cas d'erreur SQL
    // C'est important pour pouvoir détecter les bugs facilement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cette ligne fait que PDO retourne les résultats sous forme de tableaux associatifs
    // Ex: $ligne['titre'] au lieu de $ligne[0]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la connexion échoue, on affiche un message d'erreur et on arrête le script
    // En production, on n'afficherait pas les détails de l'erreur pour des raisons de sécurité
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
// Après ce fichier, la variable $pdo est disponible pour faire des requêtes
?>