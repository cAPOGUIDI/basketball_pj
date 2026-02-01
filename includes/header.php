<?php
/**
 * En-tête réutilisable pour les pages connectées
 */

// S'assurer que auth.php est chargé
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}

startSession();
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <h1><a href="index.php" style="color: white; text-decoration: none;">🏀 Basketball Training</a></h1>
            <nav>
                <a href="index.php" class="btn btn-outline">Accueil</a>
                <a href="contact.php" class="btn btn-outline">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-secondary">Tableau de bord</a>
                    <a href="program.php" class="btn btn-outline">Mon Programme</a>
                    <a href="logout.php" class="btn btn-outline">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Connexion</a>
                    <a href="register.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>