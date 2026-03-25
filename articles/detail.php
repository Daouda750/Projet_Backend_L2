<?php
// articles/detail.php - Affichage complet d'un article
require_once '../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../accueil.php');
    exit;
}

// Vérifier si la colonne image existe (compatibilité table sans colonne image)
$imageColumn = $pdo->query("SHOW COLUMNS FROM articles LIKE 'image'")->fetch();
$imageSelect = $imageColumn ? 'a.image,' : '';

$sql = "SELECT a.id, a.titre, a.contenu, a.description_courte, $imageSelect a.date_publication,
               c.nom AS categorie, u.prenom, u.nom AS auteur
        FROM articles a
        JOIN categories c ON a.id_categorie = c.id
        JOIN users u ON a.id_auteur = u.id
        WHERE a.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: ../accueil.php');
    exit;
}

// article précédent / suivant
$prevStmt = $pdo->prepare('SELECT id, titre FROM articles WHERE id < :id ORDER BY id DESC LIMIT 1');
$prevStmt->execute([':id' => $id]);
$prevArticle = $prevStmt->fetch();

$nextStmt = $pdo->prepare('SELECT id, titre FROM articles WHERE id > :id ORDER BY id ASC LIMIT 1');
$nextStmt->execute([':id' => $id]);
$nextArticle = $nextStmt->fetch();
?>

<?php require_once '../entete.php'; ?>
<main>
    <div class="conteneur">
        <h1><?php echo htmlspecialchars($article['titre']); ?></h1>
        <p class="meta">
            Catégorie : <?php echo htmlspecialchars($article['categorie']); ?>
            • Publié le <?php echo htmlspecialchars($article['date_publication']); ?>
            • par <?php echo htmlspecialchars($article['prenom'] . ' ' . $article['auteur']); ?>
        </p>

        <?php if (!empty($article['image'])): ?>
            <p><img class="article-image" src="../<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>" style="max-width:100%; border-radius:10px; margin-bottom:12px;"></p>
        <?php endif; ?>

        <div class="article-contenu">
            <?php echo nl2br(htmlspecialchars($article['contenu'])); ?>
        </div>

        <p>
            <a class="btn btn-secondaire" href="../accueil.php">Retour à l'accueil</a>
            <?php if ($prevArticle): ?>
                <a class="btn btn-primaire" href="detail.php?id=<?php echo $prevArticle['id']; ?>">Précédent</a>
            <?php else: ?>
                <span class="btn btn-inactif">Précédent</span>
            <?php endif; ?>

            <?php if ($nextArticle): ?>
                <a class="btn btn-primaire" href="detail.php?id=<?php echo $nextArticle['id']; ?>">Suivant</a>
            <?php else: ?>
                <span class="btn btn-inactif">Suivant</span>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'editeur' || $_SESSION['user_role'] === 'administrateur')): ?>
                <a class="btn btn-primaire" href="modifier.php?id=<?php echo $article['id']; ?>">Modifier</a>
            <?php endif; ?>
        </p>
    </div>
</main>

<?php require_once '../pied.php'; ?>