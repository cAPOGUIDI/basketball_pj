<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

startSession();

$success = '';
$error = '';
$userInfo = null;

// Récupérer les infos de l'utilisateur si connecté
if (isLoggedIn()) {
    $userId = getCurrentUserId();
    $userInfo = getUserProfile($userId);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'autre';
    
    // Validation
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } elseif (strlen($message) < 10) {
        $error = 'Le message doit contenir au moins 10 caractères';
    } else {
        try {
            $db = getDBConnection();
            
            $stmt = $db->prepare("
                INSERT INTO contacts (user_id, nom, email, sujet, message, type_message) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                isLoggedIn() ? getCurrentUserId() : null,
                $nom,
                $email,
                $sujet,
                $message,
                $type
            ]);
            
            $success = 'Votre message a été envoyé avec succès ! Nous vous répondrons rapidement.';
            
            // Réinitialiser le formulaire
            $nom = $email = $sujet = $message = '';
            $type = 'autre';
            
        } catch (Exception $e) {
            $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
            error_log("Erreur contact: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nous Contacter - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- En-tête -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1><a href="index.php" style="color: white; text-decoration: none;">🏀 Basketball Training</a></h1>
                <nav>
                    <a href="index.php" class="btn btn-outline">Accueil</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Tableau de bord</a>
                        <a href="logout.php" class="btn btn-outline">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">Connexion</a>
                        <a href="register.php" class="btn btn-primary">Inscription</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="contact-wrapper">
        <div class="container">
            <div class="contact-content">
                <!-- Section informations -->
                <div class="contact-info">
                    <h1>Contactez-nous</h1>
                    <p class="contact-intro">
                        Vous avez une question, une suggestion ou un retour à nous faire ? 
                        Nous sommes à votre écoute !
                    </p>
                    
                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-icon">💬</div>
                            <h3>Partagez vos avis</h3>
                            <p>Vos retours nous aident à améliorer la plateforme</p>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">💡</div>
                            <h3>Suggestions</h3>
                            <p>Proposez de nouvelles fonctionnalités ou exercices</p>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">🐛</div>
                            <h3>Signalez un bug</h3>
                            <p>Aidez-nous à corriger les problèmes techniques</p>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">❓</div>
                            <h3>Posez vos questions</h3>
                            <p>Notre équipe vous répond rapidement</p>
                        </div>
                    </div>
                    
                    <div class="contact-stats">
                        <div class="stat-item">
                            <span class="stat-number">< 24h</span>
                            <span class="stat-label">Temps de réponse moyen</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Messages lus</span>
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire de contact -->
                <div class="contact-form-container">
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">
                                    <span class="label-icon">👤</span>
                                    Nom complet
                                </label>
                                <input 
                                    type="text" 
                                    id="nom" 
                                    name="nom" 
                                    required 
                                    placeholder="Votre nom"
                                    value="<?php echo $userInfo['email'] ?? ($nom ?? ''); ?>"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="email">
                                    <span class="label-icon">📧</span>
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required 
                                    placeholder="votre@email.com"
                                    value="<?php echo $userInfo['email'] ?? ($email ?? ''); ?>"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">
                                <span class="label-icon">🏷️</span>
                                Type de message
                            </label>
                            <select id="type" name="type" required>
                                <option value="avis" <?php echo ($type ?? '') == 'avis' ? 'selected' : ''; ?>>
                                    💬 Avis / Témoignage
                                </option>
                                <option value="suggestion" <?php echo ($type ?? '') == 'suggestion' ? 'selected' : ''; ?>>
                                    💡 Suggestion / Idée
                                </option>
                                <option value="bug" <?php echo ($type ?? '') == 'bug' ? 'selected' : ''; ?>>
                                    🐛 Signaler un bug
                                </option>
                                <option value="question" <?php echo ($type ?? '') == 'question' ? 'selected' : ''; ?>>
                                    ❓ Question
                                </option>
                                <option value="autre" <?php echo ($type ?? '') == 'autre' ? 'selected' : ''; ?>>
                                    📝 Autre
                                </option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet">
                                <span class="label-icon">📌</span>
                                Sujet
                            </label>
                            <input 
                                type="text" 
                                id="sujet" 
                                name="sujet" 
                                required 
                                placeholder="Résumé en quelques mots"
                                value="<?php echo htmlspecialchars($sujet ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="message">
                                <span class="label-icon">✍️</span>
                                Message
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="6" 
                                required 
                                placeholder="Décrivez votre demande en détail..."
                                minlength="10"
                            ><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            <div class="char-counter">
                                <span id="charCount">0</span> caractères
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            <span>Envoyer le message</span>
                            <span class="btn-icon">📤</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2026 Basketball Training. Tous droits réservés.</p>
        </div>
    </footer>
    
    <script>
        // Compteur de caractères
        const textarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        
        // Animation du formulaire
        const form = document.getElementById('contactForm');
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            btn.innerHTML = '<span>Envoi en cours...</span> <span class="btn-icon">⏳</span>';
            btn.disabled = true;
        });
    </script>
</body>
</html>