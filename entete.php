<?php
// On démarre la session ici pour que toutes les pages y aient accès
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Site d'actualité</title>
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/menu.php'; ?>