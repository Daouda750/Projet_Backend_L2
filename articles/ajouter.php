<?php
// Sécurité : vérification session (démarre aussi la session)
require_once '../verif_session.php';
require_once '../db.php';

// Vérifier que le rôle est éditeur ou administrateur
if ($_SESSION['user_role'] !== 'editeur' && $_SESSION['user_role'] !== 'administrateur') {
    header('Location: /connexion.php');
    exit;
}

// Traitement du formulaire (POST)
$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre        = trim($_POST['titre']       ?? '');
    $contenu      = trim($_POST['contenu']     ?? '');
    $description  = trim($_POST['description'] ?? '');
    $id_categorie = intval($_POST['categorie'] ?? 0);

    // Validation PHP côté serveur
    if ($titre === '')          $erreurs[] = 'Le titre est obligatoire.';
    if (strlen($titre) < 5)     $erreurs[] = 'Le titre doit contenir au moins 5 caractères.';
    if (strlen($titre) > 200)   $erreurs[] = 'Le titre ne peut pas dépasser 200 caractères.';
    if ($contenu === '')        $erreurs[] = 'Le contenu est obligatoire.';
    if (strlen($contenu) < 20)  $erreurs[] = 'Le contenu doit contenir au moins 20 caractères.';
    if ($id_categorie === 0)    $erreurs[] = 'Veuillez sélectionner une catégorie.';

    // Insertion en BDD si pas d'erreur
    // Vérifier si la colonne image existe (compatibilité table sans colonne image)
    $imageColumn = $pdo->query("SHOW COLUMNS FROM articles LIKE 'image'")->fetch();
    $hasImage   = (bool) $imageColumn;

    // Gestion de l'image uploadée
    $imagePath = $hasImage ? null : null;
    if ($hasImage && isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $isImage = getimagesize($_FILES['image']['tmp_name']);
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if ($isImage && in_array($ext, $allowed)) {
                if (!is_dir(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0755, true);
                }
                $filename = uniqid('article_') . '.' . $ext;
                $destination = __DIR__ . '/../uploads/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $imagePath = 'uploads/' . $filename;
                } else {
                    $erreurs[] = 'Impossible de déplacer le fichier depuis tmp vers ' . $destination . ' (code ' . $_FILES['image']['error'] . ')';
                }
            } else {
                $erreurs[] = 'Type de fichier image non autorisé (jpg, jpeg, png, gif).';
            }
        } else {
            $erreurs[] = 'Erreur lors de l\'upload de l\'image (code ' . $_FILES['image']['error'] . ').';
        }
    }

    if (empty($erreurs)) {

        if ($hasImage) {
            $sql = "INSERT INTO articles 
                        (titre, description_courte, contenu, image, id_categorie, id_auteur) 
                    VALUES 
                        (:titre, :description_courte, :contenu, :image, :id_categorie, :id_auteur)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre'             => $titre,
                ':description_courte'=> $description,
                ':contenu'           => $contenu,
                ':image'             => $imagePath,
                ':id_categorie'      => $id_categorie,
                ':id_auteur'         => $_SESSION['user_id'],
            ]);
        } else {
            $sql = "INSERT INTO articles 
                        (titre, description_courte, contenu, id_categorie, id_auteur) 
                    VALUES 
                        (:titre, :description_courte, :contenu, :id_categorie, :id_auteur)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre'             => $titre,
                ':description_courte'=> $description,
                ':contenu'           => $contenu,
                ':id_categorie'      => $id_categorie,
                ':id_auteur'         => $_SESSION['user_id'],
            ]);
        }

        $succes = true;
    }
}

// Récupérer les catégories pour le select
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
?>

<?php require_once '../entete.php'; ?>

<main>
    <div class="conteneur">

        <h2>Ajouter un article</h2>

        <?php if ($succes) : ?>
            <div class="message-succes">
                Article publié avec succès.
                <a href="../accueil.php">Retour à l'accueil</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($erreurs)) : ?>
            <div class="message-erreur">
                <ul>
                    <?php foreach ($erreurs as $e) : ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="form-ajouter-article" class="formulaire" method="POST" action="ajouter.php" enctype="multipart/form-data">

            <div class="champ">
                <label for="titre">Titre *</label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    placeholder="Titre de l'article"
                    value="<?php echo htmlspecialchars($titre ?? ''); ?>">
            </div>

            <div class="champ">
                <label for="description">Description courte</label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    placeholder="Résumé affiché sur l'accueil (300 caractères max)"
                    value="<?php echo htmlspecialchars($description ?? ''); ?>">
            </div>

            <div class="champ">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie">
                    <option value="0">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat) : ?>
                        <option
                            value="<?php echo $cat['id']; ?>"
                            <?php echo (isset($id_categorie) && $id_categorie == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="champ">
                <label for="image">Image (optionnelle)</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="champ">
                <label for="contenu">Contenu *</label>
                <textarea
                    id="contenu"
                    name="contenu"
                    rows="10"
                    placeholder="Rédigez le contenu complet de l'article..."><?php echo htmlspecialchars($contenu ?? ''); ?></textarea>
            </div>

            <div class="champ">
                <button type="submit" class="btn btn-primaire">Publier l'article</button>
                <a href="../accueil.php" class="btn btn-secondaire">Annuler</a>
            </div>

        </form>

    </div>
</main>

<?php require_once '../pied.php'; ?>
<script src="/validation_articles.js"></script>