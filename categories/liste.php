<?php
// categories/liste.php - Liste des catégories
require_once '../verif_session.php';
require_once '../db.php';

// Autorisation
if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom ASC')->fetchAll();
?>
<?php require_once '../entete.php'; ?>
<main>
    <div class="conteneur">
        <h2>Gestion des catégories</h2>
        <a href="ajouter.php" class="btn btn-primaire">Ajouter une catégorie</a>

        <table class="tableau">
            <thead>
                <tr><th>ID</th><th>Nom</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat['id']); ?></td>
                        <td><?php echo htmlspecialchars($cat['nom']); ?></td>
                        <td>
                            <a href="modifier.php?id=<?php echo $cat['id']; ?>">Modifier</a> |
                            <a href="supprimer.php?id=<?php echo $cat['id']; ?>" class="btn-supprimer-categorie" data-nom="<?php echo htmlspecialchars($cat['nom']); ?>">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once '../pied.php'; ?>