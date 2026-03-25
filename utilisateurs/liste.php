<?php
// ============================================================
// utilisateurs/liste.php - Liste de tous les utilisateurs
// Réservé aux administrateurs uniquement
// Étudiant 1
// ============================================================

// verif_admin.php démarre déjà la session et vérifie le rôle admin
// Pas besoin de session_start() ici car verif_admin.php s'en occupe
require_once 'verif_admin.php';

// Connexion à la base de données
require_once '../db.php';

// --- Récupérer tous les utilisateurs ---
// Requête simple sans paramètre externe donc pas besoin de requête préparée ici
// Mais on utilise quand même PDO pour être cohérent
$sql = "SELECT id, nom, prenom, login, role FROM users ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll(); // fetchAll() récupère TOUS les résultats d'un coup
?>
<?php require_once '../entete.php'; ?>

<main>
<div class="conteneur">
    <h2>Gestion des utilisateurs</h2>

    <!-- Lien pour ajouter un nouvel utilisateur -->
    <a href="ajouter.php">+ Ajouter un utilisateur</a>
    <br><br>

    <!-- Message de succès si une action vient d'être effectuée -->
    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </p>
    <?php endif; ?>

    <!-- Tableau des utilisateurs -->
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Login</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($utilisateurs)): ?>
                <tr>
                    <td colspan="6">Aucun utilisateur trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <!-- htmlspecialchars() protège contre les failles XSS -->
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['login']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="modifier.php?id=<?php echo $user['id']; ?>">Modifier</a>
                            |
                            <!-- Confirmation avant suppression -->
                            <a href="supprimer.php?id=<?php echo $user['id']; ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</main>

<?php require_once '../pied.php'; ?>
