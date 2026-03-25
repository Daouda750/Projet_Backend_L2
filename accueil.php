<?php
// accueil.php - Page d'accueil du site d'actualité
require_once 'db.php';

// Récupération des catégories pour le filtre et compteur
$categories = $pdo->query('SELECT c.id, c.nom, COUNT(a.id) AS article_count
                           FROM categories c
                           LEFT JOIN articles a ON a.id_categorie = c.id
                           GROUP BY c.id, c.nom
                           ORDER BY c.nom ASC')->fetchAll();

$search = trim($_GET['search'] ?? '');

// Pagination
$parPage = 5;
$page = max(1, intval($_GET['page'] ?? 1));
$catId = intval($_GET['categorie'] ?? 0);

$where = 'WHERE TRUE';
$params = [];

if ($catId > 0) {
    $where .= ' AND a.id_categorie = :catId';
    $params[':catId'] = $catId;
}

if ($search !== '') {
    $where .= ' AND (a.titre LIKE :search OR a.description_courte LIKE :search OR a.contenu LIKE :search OR c.nom LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

// Vérifier si la colonne image existe (pour compatibilité SQL) :
$imageColumn = $pdo->query("SHOW COLUMNS FROM articles LIKE 'image'")->fetch();
$imageSelect = $imageColumn ? 'a.image,' : '';

// Nombre total d'articles (filtrés)
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM articles a
                          JOIN categories c ON a.id_categorie = c.id
                          JOIN users u ON a.id_auteur = u.id
                          $where");
$totalStmt->execute($params);
$totalArticles = intval($totalStmt->fetchColumn());

$totalPages = max(1, ceil($totalArticles / $parPage));
if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $parPage;

// Récupération des articles pour la page actuelle
$sql = "SELECT a.id, a.titre, a.description_courte, $imageSelect a.date_publication, c.nom AS categorie, u.prenom, u.nom AS auteur
        FROM articles a
        JOIN categories c ON a.id_categorie = c.id
        JOIN users u ON a.id_auteur = u.id
        $where
        ORDER BY a.date_publication DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    if ($k === ':catId') {
        $stmt->bindValue($k, $v, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
}
$stmt->bindValue(':limit', $parPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
?>

<?php require_once 'entete.php'; ?>
<main>
    <div class="conteneur">
        <h1>Actualités</h1>

        <form method="GET" action="accueil.php" class="form-filtre">
            <input type="text" name="search" placeholder="Rechercher un article" value="<?php echo htmlspecialchars($search); ?>" style="width: 220px; padding: 7px;" />
            <button type="submit" class="btn btn-primaire" style="margin-left:10px;">Rechercher</button>
            &nbsp;&nbsp;
            <label for="categorie">Filtrer :</label>
            <select id="categorie" name="categorie" onchange="this.form.submit()" style="padding:7px;">
                <option value="0">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $catId === intval($cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nom']); ?> (<?php echo $cat['article_count']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit">Filtrer</button></noscript>
        </form>

        <?php if (empty($articles)): ?>
            <p>Aucun article trouvé.</p>
        <?php else: ?>
            <div class="grid-articles">
                <?php foreach ($articles as $article): ?>
                    <article class="carte-article">
                        <h2><a href="articles/detail.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['titre']); ?></a></h2>
                        <p class="meta">
                            Catégorie : <?php echo htmlspecialchars($article['categorie']); ?>
                            • Publié le <?php echo htmlspecialchars($article['date_publication']); ?>
                            • par <?php echo htmlspecialchars($article['prenom'] . ' ' . $article['auteur']); ?>
                        </p>
                        <?php if (!empty($article['image'])): ?>
                            <p><img class="article-image" src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>" /></p>
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($article['description_courte'])); ?></p>

                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'editeur' || $_SESSION['user_role'] === 'administrateur')): ?>
                            <div class="actions-article">
                                <a class="btn btn-secondaire" href="articles/modifier.php?id=<?php echo $article['id']; ?>">Modifier</a>
                                <a class="btn btn-danger" href="articles/supprimer.php?id=<?php echo $article['id']; ?>" onclick="return confirm('Confirmer la suppression de cet article ?');">Supprimer</a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <a class="btn" href="accueil.php?categorie=<?php echo $catId; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo max(1, $page - 1); ?>" <?php echo $page <= 1 ? 'aria-disabled="true" style="pointer-events:none;opacity:0.6;"' : ''; ?>>Précédent</a>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a class="btn <?php echo $p == $page ? 'actif' : ''; ?>" href="accueil.php?categorie=<?php echo $catId; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                <?php endfor; ?>
                <a class="btn" href="accueil.php?categorie=<?php echo $catId; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo min($totalPages, $page + 1); ?>" <?php echo $page >= $totalPages ? 'aria-disabled="true" style="pointer-events:none;opacity:0.6;"' : ''; ?>>Suivant</a>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'editeur' || $_SESSION['user_role'] === 'administrateur')): ?>
            <p>
                <a class="btn btn-primaire" href="articles/ajouter.php">Ajouter un article</a>
                <a class="btn btn-primaire" href="categories/liste.php">Gérer les catégories</a>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'administrateur'): ?>
            <p><a class="btn btn-primaire" href="utilisateurs/liste.php">Gérer les utilisateurs</a></p>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'pied.php'; ?>