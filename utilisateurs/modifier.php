<?php
// ============================================================
// utilisateurs/modifier.php - Modifier un utilisateur existant
// Réservé aux administrateurs
// Étudiant 1
// ============================================================

require_once 'verif_admin.php';
require_once '../db.php';

$erreurs = [];

// --- Récupérer l'ID depuis l'URL ---
// Ex: modifier.php?id=3
$id = intval($_GET['id'] ?? 0); // intval() convertit en entier pour éviter les injections
if ($id <= 0) {
    header("Location: liste.php");
    exit();
}

// --- Récupérer les infos actuelles de l'utilisateur ---
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    // Utilisateur non trouvé, on retourne à la liste
    header("Location: liste.php");
    exit();
}

// --- Traitement du formulaire de modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $nouveau_mdp = trim($_POST['mot_de_passe'] ?? ''); // Peut être vide (on ne change pas le mdp)
    $role = trim($_POST['role'] ?? '');

    // Validation
    if (empty($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (empty($login) || strlen($login) < 3) $erreurs[] = "Le login doit faire au moins 3 caractères.";
    if (!in_array($role, ['editeur', 'administrateur'])) $erreurs[] = "Rôle invalide.";

    // Si le nouveau mot de passe est fourni, on vérifie sa longueur
    if (!empty($nouveau_mdp) && strlen($nouveau_mdp) < 6) {
        $erreurs[] = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    }

    if (empty($erreurs)) {
        if (!empty($nouveau_mdp)) {
            // Si un nouveau mot de passe est fourni, on le hashe et on l'inclut dans la requête
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nom=:nom, prenom=:prenom, login=:login, mot_de_passe=:mdp, role=:role WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':login'  => $login,
                ':mdp'    => $hash,
                ':role'   => $role,
                ':id'     => $id
            ]);
        } else {
            // Sinon on met à jour tout sauf le mot de passe
            $sql = "UPDATE users SET nom=:nom, prenom=:prenom, login=:login, role=:role WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':login'  => $login,
                ':role'   => $role,
                ':id'     => $id
            ]);
        }

        header("Location: liste.php?message=Utilisateur modifié avec succès");
        exit();
    }
}
?>
<?php require_once '../entete.php'; ?>

<main>
<div class="conteneur">
    <h2>Modifier l'utilisateur</h2>

    <?php if (!empty($erreurs)): ?>
        <ul style="color: red;">
            <?php foreach ($erreurs as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="modifier.php?id=<?php echo $id; ?>" onsubmit="return validerModif()">
        <div>
            <label>Nom :</label>
            <!-- On pré-remplit avec les valeurs actuelles de l'utilisateur -->
            <input type="text" name="nom" id="nom"
                   value="<?php echo htmlspecialchars($_POST['nom'] ?? $user['nom']); ?>">
            <span id="err_nom" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Prénom :</label>
            <input type="text" name="prenom" id="prenom"
                   value="<?php echo htmlspecialchars($_POST['prenom'] ?? $user['prenom']); ?>">
            <span id="err_prenom" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Login :</label>
            <input type="text" name="login" id="login"
                   value="<?php echo htmlspecialchars($_POST['login'] ?? $user['login']); ?>">
            <span id="err_login" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe">
            <span id="err_mdp" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Rôle :</label>
            <select name="role" id="role">
                <option value="editeur" <?php echo ($user['role'] === 'editeur') ? 'selected' : ''; ?>>Éditeur</option>
                <option value="administrateur" <?php echo ($user['role'] === 'administrateur') ? 'selected' : ''; ?>>Administrateur</option>
            </select>
            <span id="err_role" style="color:red;"></span>
        </div>
        <br>
        <button type="submit">Enregistrer les modifications</button>
        <a href="liste.php">Annuler</a>
    </form>
</div>

<script>
function validerModif() {
    var nom = document.getElementById('nom').value.trim();
    var prenom = document.getElementById('prenom').value.trim();
    var login = document.getElementById('login').value.trim();
    var mdp = document.getElementById('mot_de_passe').value.trim();
    var valide = true;

    document.getElementById('err_nom').textContent = '';
    document.getElementById('err_prenom').textContent = '';
    document.getElementById('err_login').textContent = '';
    document.getElementById('err_mdp').textContent = '';

    if (nom === '') {
        document.getElementById('err_nom').textContent = 'Le nom est obligatoire.';
        valide = false;
    }
    if (prenom === '') {
        document.getElementById('err_prenom').textContent = 'Le prénom est obligatoire.';
        valide = false;
    }
    if (login.length < 3) {
        document.getElementById('err_login').textContent = 'Login trop court (3 min).';
        valide = false;
    }
    // On ne valide le mdp que s'il est rempli
    if (mdp !== '' && mdp.length < 6) {
        document.getElementById('err_mdp').textContent = 'Mot de passe trop court (6 min).';
        valide = false;
    }

    return valide;
}
</script>

</main>
<?php require_once '../pied.php'; ?>
