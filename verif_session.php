<?php
// ============================================================
// verif_session.php - Vérification de la session
// Ce fichier est inclus en haut de TOUTES les pages protégées
// Étudiant 1
// ============================================================

// On démarre la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On vérifie que l'utilisateur est connecté (que sa session existe)
if (!isset($_SESSION['user_id'])) {
    // Si pas connecté, on redirige vers la page de connexion
    // On utilise /connexion.php (chemin absolu) pour que ça marche depuis n'importe quel sous-dossier
    header("Location: /connexion.php");
    exit();
}
// Si on arrive ici, l'utilisateur est bien connecté
// La variable $_SESSION['user_role'] est disponible pour vérifier les droits
?>