<?php
// categories/supprimer.php - Supprimer une catégorie
require_once '../verif_session.php';
require_once '../db.php';

if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: liste.php');
exit;
?>