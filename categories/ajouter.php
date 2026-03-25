<?php
// categories/ajouter.php - Ajouter une catégorie
require_once '../verif_session.php';
require_once '../db.php';

if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    } elseif (strlen($nom) > 100) {
        $erreurs[] = 'Le nom ne peut pas dépasser 100 caractères.';
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (:nom)');
        $stmt->execute([':nom' => $nom]);
        $succes = true;
    }
}
?>
<?php require_once '../entete.php'; ?>
<main>
    <div class="conteneur">
        <h2>Ajouter une catégorie</h2>

        <?php if ($succes): ?>
            <div class="message-succes">Catégorie ajoutée avec succès. <a href="liste.php">Retour à la liste</a></div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="message-erreur"><ul><?php foreach ($erreurs as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" action="ajouter.php">
            <div class="champ">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
            </div>
            <div class="champ">
                <button type="submit" class="btn btn-primaire">Créer</button>
                <a href="liste.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>
</main>
<?php require_once '../pied.php'; ?>