<?php
// ============================================================
// generate_hash.php - Utilitaire pour générer des hash de mots de passe
// IMPORTANT : Ce fichier est uniquement pour les tests en développement
// Supprime-le ou protège-le en production !
// Étudiant 1
// ============================================================

// Mots de passe à hasher (modifie selon tes besoins)
$mots_de_passe = [
    'admin123',
    'editeur123',
    'editeur234',
    'editeur345'
];

echo "<h2>Génération de hash de mots de passe</h2>";
echo "<p>Copie ces hash dans ton script SQL pour les données initiales.</p>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Mot de passe</th><th>Hash à mettre dans la BDD</th></tr>";

foreach ($mots_de_passe as $mdp) {
    $hash = password_hash($mdp, PASSWORD_DEFAULT);
    echo "<tr>";
    echo "<td>" . htmlspecialchars($mdp) . "</td>";
    echo "<td style='font-size:11px;'>" . $hash . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<br>";
echo "<p style='color:red;'>N'oublie pas de supprimer ce fichier après utilisation !</p>";
?>