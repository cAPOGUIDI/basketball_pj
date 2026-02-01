<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Protéger la page
requireLogin();

$userId = getCurrentUserId();
$userProfile = getUserProfile($userId);

// Vérifier que le profil est complet
if (!hasCompletedProfile($userId)) {
    header('Location: profile.php');
    exit();
}

// Récupérer le programme personnalisé
$poste = $userProfile['poste'];
$exercises = getPersonalizedProgram($poste);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Programme - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- En-tête -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1>🏀 Basketball Training</h1>
                <nav>
                    <a href="dashboard.php" class="btn btn-outline">Tableau de bord</a>
                    <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="program-header">
            <h1>Programme pour <?php echo cleanOutput($userProfile['poste_nom']); ?></h1>
            <p class="program-subtitle">
                Programme personnalisé basé sur votre poste de jeu
            </p>
        </div>
        
        <?php if (empty($exercises)): ?>
            <div class="alert alert-info">
                Aucun exercice disponible pour le moment. Les programmes sont en cours de développement.
            </div>
        <?php else: ?>
            <!-- Résumé du programme -->
            <div class="program-summary">
                <div class="summary-stat">
                    <span class="stat-number"><?php echo count($exercises); ?></span>
                    <span class="stat-label">Exercices</span>
                </div>
                <div class="summary-stat">
                    <span class="stat-number">
                        <?php 
                        $totalDuration = array_sum(array_column($exercises, 'duree'));
                        echo floor($totalDuration / 60); 
                        ?>
                    </span>
                    <span class="stat-label">Minutes</span>
                </div>
                <div class="summary-stat">
                    <span class="stat-number">
                        <?php 
                        $difficulties = array_column($exercises, 'difficulte');
                        $avgDiff = in_array('difficile', $difficulties) ? 'Difficile' : 
                                  (in_array('moyen', $difficulties) ? 'Moyen' : 'Facile');
                        echo $avgDiff;
                        ?>
                    </span>
                    <span class="stat-label">Niveau</span>
                </div>
            </div>
            
            <!-- Liste des exercices -->
            <div class="exercises-list">
                <?php 
                $currentDifficulty = '';
                foreach ($exercises as $exercise): 
                    // Grouper par difficulté
                    if ($exercise['difficulte'] !== $currentDifficulty):
                        if ($currentDifficulty !== '') echo '</div>'; // Fermer le groupe précédent
                        $currentDifficulty = $exercise['difficulte'];
                ?>
                    <h2 class="difficulty-header">
                        <?php 
                        echo ucfirst($currentDifficulty);
                        if ($currentDifficulty === 'facile') echo ' 🟢';
                        elseif ($currentDifficulty === 'moyen') echo ' 🟡';
                        else echo ' 🔴';
                        ?>
                    </h2>
                    <div class="difficulty-group">
                <?php endif; ?>
                
                <!-- Carte d'exercice -->
                <div class="exercise-item">
                    <?php if ($exercise['video_url']): ?>
                        <!-- Vidéo en boucle -->
                        <div style="width: 100%; height: 250px; border-radius: 8px; overflow: hidden; margin-bottom: 1rem; background: #000;">
                            <video autoplay loop muted playsinline 
                                   style="width: 100%; height: 100%; object-fit: cover;">
                                <source src="<?php echo cleanOutput($exercise['video_url']); ?>" type="video/mp4">
                                Votre navigateur ne supporte pas la vidéo.
                            </video>
                        </div>
                    <?php elseif ($exercise['image_url']): ?>
                        <!-- Image de secours -->
                        <div style="width: 100%; height: 200px; border-radius: 8px; overflow: hidden; margin-bottom: 1rem;">
                            <img src="<?php echo cleanOutput($exercise['image_url']); ?>" 
                                 alt="<?php echo cleanOutput($exercise['titre']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="exercise-header">
                        <h3><?php echo cleanOutput($exercise['titre']); ?></h3>
                        <span class="badge badge-<?php echo $exercise['difficulte']; ?>">
                            <?php echo ucfirst($exercise['difficulte']); ?>
                        </span>
                    </div>
                    
                    <p class="exercise-description">
                        <?php echo cleanOutput($exercise['description']); ?>
                    </p>
                    
                    <div class="exercise-details">
                        <?php if ($exercise['duree']): ?>
                            <div class="detail">
                                <span class="detail-icon">⏱️</span>
                                <span><?php echo formatDuration($exercise['duree']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($exercise['repetitions']): ?>
                            <div class="detail">
                                <span class="detail-icon">🔄</span>
                                <span><?php echo $exercise['repetitions']; ?> répétitions</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="exercise-actions">
                        <button class="btn btn-outline btn-small" onclick="markAsComplete(<?php echo $exercise['id']; ?>)">
                            ✓ Marquer comme fait
                        </button>
                        <button class="btn btn-outline btn-small" onclick="addToFavorites(<?php echo $exercise['id']; ?>)">
                            ⭐ Favori
                        </button>
                    </div>
                </div>
                
                <?php endforeach; ?>
                <?php if ($currentDifficulty !== '') echo '</div>'; // Fermer le dernier groupe ?>
            </div>
            
            <!-- Conseils spécifiques au poste -->
            <div class="tips-section">
                <h2>💡 Conseils pour votre poste</h2>
                <div class="position-tips">
                    <?php
                    $tips = [
                        1 => "En tant que meneur, travaillez votre dribble et votre vision du jeu. Privilégiez les exercices de passes et de prises de décision rapides.",
                        2 => "L'arrière doit exceller au tir extérieur. Concentrez-vous sur la répétition des gestes et la constance au shoot.",
                        3 => "L'ailier est polyvalent. Équilibrez votre entraînement entre tir à 3 points, pénétrations et défense.",
                        4 => "L'ailier fort combine force et agilité. Travaillez au poste et les tirs à mi-distance.",
                        5 => "Le pivot domine près du panier. Renforcez votre jeu au poste bas et votre capacité au rebond."
                    ];
                    
                    echo '<p>' . $tips[$poste] . '</p>';
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2026 Basketball Training. Tous droits réservés.</p>
        </div>
    </footer>
    
    <script src="js/main.js"></script>
    <script>
        // Fonction pour marquer un exercice comme complété
        function markAsComplete(exerciseId) {
            alert('Fonctionnalité à venir : suivi des exercices complétés');
            // TODO: Implémenter l'enregistrement dans la table "progres"
        }
        
        // Fonction pour ajouter aux favoris
        function addToFavorites(exerciseId) {
            alert('Fonctionnalité à venir : gestion des favoris');
            // TODO: Appel AJAX vers un script PHP pour ajouter aux favoris
        }
    </script>
</body>
</html>