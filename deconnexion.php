<?php
// ============================================================
// deconnexion.php - Déconnexion de l'utilisateur
// Étudiant 1
// ============================================================

// On démarre la session pour pouvoir y accéder et la détruire
session_start();

// On efface toutes les variables de session
$_SESSION = array();

// On détruit complètement la session
session_destroy();

// On redirige vers la page de connexion
header("Location: connexion.php");
exit();
?>