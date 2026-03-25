<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /accueil.php");
    exit();
}

require_once 'db.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login        = trim($_POST['login']        ?? '');
    $mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

    if ($login === '' || $mot_de_passe === '') {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $sql  = "SELECT * FROM users WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':login' => $login]);
        $utilisateur = $stmt->fetch();

        if (!$utilisateur) {
            $erreur = "Login ou mot de passe incorrect.";
        } else {
            $authOk = false;

            // Mot de passe hashé normal
            if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                $authOk = true;
            }

            // Cas temporaire : mot de passe stocké en clair (migration)
            if (!$authOk && $utilisateur['mot_de_passe'] === $mot_de_passe) {
                $authOk = true;
                $nouveauHash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt2 = $pdo->prepare('UPDATE users SET mot_de_passe = :hash WHERE id = :id');
                $stmt2->execute([':hash' => $nouveauHash, ':id' => $utilisateur['id']]);
            }

            if ($authOk) {
                $_SESSION['user_id']     = $utilisateur['id'];
                $_SESSION['user_login']  = $utilisateur['login'];
                $_SESSION['user_nom']    = $utilisateur['nom'];
                $_SESSION['user_prenom'] = $utilisateur['prenom'];
                $_SESSION['user_role']   = $utilisateur['role'];

                header("Location: /accueil.php");
                exit();
            } else {
                $erreur = "Login ou mot de passe incorrect.";
            }
        }
    }
}
?>
<?php require_once 'entete.php'; ?>

<main>
    <div class="conteneur">

        <h2>Connexion</h2>

        <?php if (!empty($erreur)) : ?>
            <div class="message-erreur">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>

        <form id="form-connexion" class="formulaire" method="POST" action="connexion.php">

            <div class="champ">
                <label for="login">Login</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    placeholder="Votre login"
                    value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
            </div>

            <div class="champ">
                <label for="mot_de_passe">Mot de passe</label>
                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    placeholder="Votre mot de passe">
            </div>

            <div class="champ">
                <button type="submit" class="btn btn-primaire">Se connecter</button>
            </div>

        </form>

    </div>
</main>

<?php require_once 'pied.php'; ?>
<script src="/validation_connexion.js"></script>