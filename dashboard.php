<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Protéger la page
requireLogin();

$userId = getCurrentUserId();
$userProfile = getUserProfile($userId);

// Vérifier si le profil est complet
$hasProfile = hasCompletedProfile($userId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Tableau de Bord - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- En-tête -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1>🏀 Basketball Training</h1>
                <nav>
                    <a href="index.php" class="btn btn-outline">Accueil</a>
                    <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container dashboard-container">
        <div class="welcome-section">
            <h1>Bienvenue, <?php echo cleanOutput($userProfile['email']); ?> !</h1>
            
            <?php if (!$hasProfile): ?>
                <div class="alert alert-warning">
                    <strong>Action requise :</strong> Veuillez compléter votre profil pour accéder à votre programme personnalisé.
                    <br>
                    <a href="profile.php" class="btn btn-primary" style="margin-top: 10px;">
                        Compléter mon profil
                    </a>
                </div>
            <?php else: ?>
                <div class="profile-summary">
                    <h3>Votre profil</h3>
                    <div class="profile-stats">
                        <div class="stat">
                            <span class="stat-label">Poids</span>
                            <span class="stat-value"><?php echo $userProfile['poids']; ?> kg</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Taille</span>
                            <span class="stat-value"><?php echo $userProfile['taille']; ?> cm</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Poste</span>
                            <span class="stat-value"><?php echo cleanOutput($userProfile['poste_nom']); ?></span>
                        </div>
                    </div>
                    <a href="profile.php" class="btn btn-outline">Modifier mon profil</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-grid">
            <!-- Programme personnalisé -->
            <div class="dashboard-card">
                <h2>🎯 Mon Programme</h2>
                <p>Entraînements adaptés à votre poste de jeu</p>
                <?php if ($hasProfile): ?>
                    <a href="program.php" class="btn btn-primary">Accéder à mon programme</a>
                <?php else: ?>
                    <p class="disabled-text">Complétez votre profil pour débloquer</p>
                <?php endif; ?>
            </div>
            
            <!-- Exercices publics -->
            <div class="dashboard-card">
                <h2>💪 Exercices Généraux</h2>
                <p>Étirements, pré-game et prévention blessures</p>
                <a href="index.php#exercises" class="btn btn-secondary">Voir les exercices</a>
            </div>
            
            <!-- Favoris (fonctionnalité future) -->
            <div class="dashboard-card">
                <h2>⭐ Mes Favoris</h2>
                <p>Vos exercices sauvegardés</p>
                <button class="btn btn-outline" disabled>Bientôt disponible</button>
            </div>
            
            <!-- Statistiques (fonctionnalité future) -->
            <div class="dashboard-card">
                <h2>📊 Mes Statistiques</h2>
                <p>Suivez votre progression</p>
                <button class="btn btn-outline" disabled>Bientôt disponible</button>
            </div>
        </div>
        
        <!-- Conseils rapides -->
        <div class="tips-section">
            <h2 id = "tips">💡 Conseils du jour</h2>
            <div class="tips-grid">
                <div class="tip-card">
                    <h4>Échauffement</h4>
                    <p>Toujours commencer par 5-10 minutes d'échauffement cardiovasculaire avant l'entraînement.</p>
                </div>
                <div class="tip-card">
                    <h4>Hydratation</h4>
                    <p>Buvez régulièrement, même avant d'avoir soif. 500ml toutes les 15-20 minutes d'exercice.</p>
                </div>
                <div class="tip-card">
                    <h4>Récupération</h4>
                    <p>Le repos est aussi important que l'entraînement. Dormez 7-9h par nuit.</p>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2026 Basketball Training. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>