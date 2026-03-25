<?php
// categories/modifier.php - Modifier une catégorie
require_once '../verif_session.php';
require_once '../db.php';

if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute([':id' => $id]);
$categorie = $stmt->fetch();
if (!$categorie) {
    header('Location: liste.php');
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
        $stmt = $pdo->prepare('UPDATE categories SET nom = :nom WHERE id = :id');
        $stmt->execute([':nom' => $nom, ':id' => $id]);
        $succes = true;
        $categorie['nom'] = $nom;
    }
}
?>
<?php require_once '../entete.php'; ?>
<main>
    <div class="conteneur">
        <h2>Modifier la catégorie</h2>

        <?php if ($succes): ?>
            <div class="message-succes">Catégorie modifiée avec succès. <a href="liste.php">Retour à la liste</a></div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="message-erreur"><ul><?php foreach ($erreurs as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" action="modifier.php?id=<?php echo $id; ?>">
            <div class="champ">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? $categorie['nom']); ?>">
            </div>
            <div class="champ">
                <button type="submit" class="btn btn-primaire">Enregistrer</button>
                <a href="liste.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>
</main>
<?php require_once '../pied.php'; ?>