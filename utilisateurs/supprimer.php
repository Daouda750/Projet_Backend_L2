<?php
// ============================================================
// utilisateurs/supprimer.php - Supprimer un utilisateur
// Réservé aux administrateurs
// Étudiant 1
// ============================================================

session_status(); // session déjà gérée par verif_admin.php
require_once 'verif_admin.php';
require_once '../db.php';

// Récupération de l'ID depuis l'URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: liste.php");
    exit();
}

// Sécurité : on empêche l'admin de se supprimer lui-même
if ($id === $_SESSION['user_id']) {
    header("Location: liste.php?message=Vous ne pouvez pas supprimer votre propre compte.");
    exit();
}

// On vérifie que l'utilisateur existe avant de le supprimer
$sql = "SELECT id FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    header("Location: liste.php");
    exit();
}

// Suppression de l'utilisateur
$sql = "DELETE FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

// Redirection avec message de succès
header("Location: liste.php?message=Utilisateur supprimé avec succès.");
exit();
?>