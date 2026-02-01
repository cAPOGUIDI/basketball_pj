<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

startSession();

// Récupérer les catégories publiques
$categories = getPublicCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basketball Training - Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- En-tête -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1>🏀 Basketball Training</h1>
                <nav>
                    <a href="contact.php" class="btn btn-outline">Contact</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Mon Espace</a>
                        <a href="logout.php" class="btn btn-outline">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">Connexion</a>
                        <a href="register.php" class="btn btn-primary">Inscription</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h2>Améliorez votre jeu avec des programmes personnalisés</h2>
            <p class="hero-text">
                Accédez à des exercices d'échauffement, de prévention des blessures et des programmes 
                d'entraînement adaptés à votre poste de jeu.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn btn-primary btn-large">
                    Commencer gratuitement
                </a>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Exercices publics -->
    <section class="exercises-section">
        <div class="container">
            <h2>Exercices accessibles à tous</h2>
            <p class="section-subtitle">Aucune inscription requise</p>
            
            <div class="exercises-grid">
                <?php 
                // Icônes pour chaque catégorie
                $categoryIcons = [
                    'Étirements' => '🧘',
                    'Pré-game' => '🔥',
                    'Genoux' => '🦵',
                    'Chevilles' => '👟',
                    'Poignets' => '✋',
                    'Épaules' => '💪'
                ];
                
                foreach ($categories as $category): 
                    $icon = $categoryIcons[$category['nom']] ?? '🏀';
                ?>
                    <div class="exercise-card" onclick="loadExercises('<?php echo $category['nom']; ?>')">
                        <h3><?php echo $icon; ?> <?php echo cleanOutput($category['nom']); ?></h3>
                        <p><?php echo cleanOutput($category['description']); ?></p>
                        <button class="btn btn-outline">Voir les exercices</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Modal pour afficher les exercices -->
    <div id="exerciseModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Exercices</h2>
            <div id="exerciseList" class="exercise-list">
                <!-- Les exercices seront chargés ici via JavaScript -->
            </div>
        </div>
    </div>
    
    <!-- Section avantages -->
    <section class="features-section">
        <div class="container">
            <h2>Pourquoi nous rejoindre ?</h2>
            
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <h3>Programmes personnalisés</h3>
                    <p>Entraînements adaptés à votre poste de jeu (meneur, arrière, ailier, etc.)</p>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">💪</div>
                    <h3>Prévention blessures</h3>
                    <p>Exercices spécifiques pour renforcer genoux, chevilles, poignets et épaules</p>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">📊</div>
                    <h3>Suivi progression</h3>
                    <p>Enregistrez vos performances et suivez votre évolution</p>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">🚀</div>
                    <h3>Accès gratuit</h3>
                    <p>Inscription gratuite et accès immédiat à tous les programmes</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2026 Basketball Training. Tous droits réservés.</p>
        </div>
    </footer>
    
    <script src="js/main.js"></script>
</body>
</html>