<?php
// ============================================================
// utilisateurs/ajouter.php - Ajouter un nouvel utilisateur
// Réservé aux administrateurs
// Étudiant 1
// ============================================================

require_once 'verif_admin.php';
require_once '../db.php';

$erreurs = []; // Tableau pour stocker les erreurs de validation

// --- Traitement du formulaire ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $mot_de_passe = trim($_POST['mot_de_passe'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // --- Validation côté serveur ---
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $erreurs[] = "Le prénom est obligatoire.";
    }
    if (empty($login)) {
        $erreurs[] = "Le login est obligatoire.";
    } elseif (strlen($login) < 3) {
        $erreurs[] = "Le login doit contenir au moins 3 caractères.";
    }
    if (empty($mot_de_passe)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    } elseif (strlen($mot_de_passe) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    // On vérifie que le rôle choisi est bien l'une des valeurs autorisées
    $roles_autorises = ['editeur', 'administrateur'];
    if (!in_array($role, $roles_autorises)) {
        $erreurs[] = "Le rôle sélectionné est invalide.";
    }

    // Si pas d'erreurs, on peut insérer dans la BDD
    if (empty($erreurs)) {

        // On vérifie si le login existe déjà
        $sql = "SELECT id FROM users WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':login' => $login]);
        if ($stmt->fetch()) {
            $erreurs[] = "Ce login est déjà utilisé. Choisissez-en un autre.";
        } else {
            // On hashe le mot de passe avant de le stocker
            // password_hash() crée un hash sécurisé et unique
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Requête préparée pour insérer l'utilisateur
            $sql = "INSERT INTO users (nom, prenom, login, mot_de_passe, role)
                    VALUES (:nom, :prenom, :login, :mot_de_passe, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'          => $nom,
                ':prenom'       => $prenom,
                ':login'        => $login,
                ':mot_de_passe' => $hash,
                ':role'         => $role
            ]);

            // On redirige vers la liste avec un message de succès
            header("Location: liste.php?message=Utilisateur ajouté avec succès");
            exit();
        }
    }
}
?>
<?php require_once '../entete.php'; ?>

<main>
<div class="conteneur">
    <h2>Ajouter un utilisateur</h2>

    <!-- Affichage des erreurs serveur -->
    <?php if (!empty($erreurs)): ?>
        <ul style="color: red;">
            <?php foreach ($erreurs as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="ajouter.php" onsubmit="return validerFormUser()">
        <div>
            <label>Nom :</label>
            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
            <span id="err_nom" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Prénom :</label>
            <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
            <span id="err_prenom" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Login :</label>
            <input type="text" name="login" id="login" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
            <span id="err_login" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe">
            <span id="err_mdp" style="color:red;"></span>
        </div>
        <br>
        <div>
            <label>Rôle :</label>
            <select name="role" id="role">
                <option value="">-- Choisir un rôle --</option>
                <option value="editeur">Éditeur</option>
                <option value="administrateur">Administrateur</option>
            </select>
            <span id="err_role" style="color:red;"></span>
        </div>
        <br>
        <button type="submit">Ajouter</button>
        <a href="liste.php">Annuler</a>
    </form>
</div>

<script>
function validerFormUser() {
    var nom = document.getElementById('nom').value.trim();
    var prenom = document.getElementById('prenom').value.trim();
    var login = document.getElementById('login').value.trim();
    var mdp = document.getElementById('mot_de_passe').value.trim();
    var role = document.getElementById('role').value;
    var valide = true;

    // Efface les anciens messages
    document.getElementById('err_nom').textContent = '';
    document.getElementById('err_prenom').textContent = '';
    document.getElementById('err_login').textContent = '';
    document.getElementById('err_mdp').textContent = '';
    document.getElementById('err_role').textContent = '';

    if (nom === '') {
        document.getElementById('err_nom').textContent = 'Le nom est obligatoire.';
        valide = false;
    }
    if (prenom === '') {
        document.getElementById('err_prenom').textContent = 'Le prénom est obligatoire.';
        valide = false;
    }
    if (login.length < 3) {
        document.getElementById('err_login').textContent = 'Le login doit faire au moins 3 caractères.';
        valide = false;
    }
    if (mdp.length < 6) {
        document.getElementById('err_mdp').textContent = 'Le mot de passe doit faire au moins 6 caractères.';
        valide = false;
    }
    if (role === '') {
        document.getElementById('err_role').textContent = 'Veuillez choisir un rôle.';
        valide = false;
    }

    return valide;
}
</script>

</main>
<?php require_once '../pied.php'; ?>
