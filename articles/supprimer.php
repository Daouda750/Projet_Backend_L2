<?php
// Sécurité : vérification session (démarre aussi la session)
require_once '../verif_session.php';
require_once '../db.php';

// Vérifier que le rôle est éditeur ou administrateur
if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

// Récupérer l'id de l'article depuis l'URL
$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: ../accueil.php');
    exit;
}

// Vérifier que l'article existe avant de supprimer
$stmt = $pdo->prepare("SELECT id, titre FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: ../accueil.php');
    exit;
}

// Suppression
$stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);

// Redirection vers l'accueil après suppression
header('Location: ../accueil.php?message=article_supprime');
exit;