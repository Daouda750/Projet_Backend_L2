<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
?>

<nav>
    <ul>

        <li><a href="/accueil.php">Accueil</a></li>
        <li><a href="/utilisateurs/liste.php">Utilisateurs</a></li>

        <?php if ($role === 'editeur' || $role === 'administrateur') : ?>
            <li><a href="/articles/ajouter.php">Ajouter un article</a></li>
            <li><a href="/categories/liste.php">Gérer les catégories</a></li>
        <?php endif; ?>

        <?php if ($role === 'administrateur') : ?>
            <li><a href="/utilisateurs/liste.php">Gérer les utilisateurs</a></li>
        <?php endif; ?>

        <?php if ($role !== null) : ?>
            <li><a href="/deconnexion.php">Déconnexion</a></li>
        <?php else : ?>
            <li><a href="/connexion.php">Connexion</a></li>
        <?php endif; ?>

    </ul>
</nav>