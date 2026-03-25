<?php
// ============================================================
// verif_admin.php - Vérification du rôle administrateur
// Inclus en haut des pages réservées aux admins uniquement
// Étudiant 1
// ============================================================

// On inclut d'abord la vérification de session classique
require_once __DIR__ . '/../verif_session.php';

// Ensuite on vérifie que l'utilisateur connecté est bien un administrateur
if ($_SESSION['user_role'] !== 'administrateur') {
    header("Location: /accueil.php?erreur=acces_interdit");
    exit();
}
?>