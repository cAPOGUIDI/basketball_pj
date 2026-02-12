<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Protéger la page - rediriger si non connecté
requireLogin();

$userId = getCurrentUserId();
$userProfile = getUserProfile($userId);

$error = '';
$success = '';

// Traitement du formulaire de profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $poids = floatval($_POST['poids'] ?? 0);
    $taille = intval($_POST['taille'] ?? 0);
    $poste = intval($_POST['poste'] ?? 0);
    
    // Validation basique
    if (empty($nom) || empty($prenom)) {
        $error = 'Veuillez remplir votre nom et prénom';
    } elseif ($poids <= 0 || $taille <= 0 || $poste < 1 || $poste > 5) {
        $error = 'Veuillez remplir tous les champs correctement';
    } else {
        if (saveProfile($userId, $nom, $prenom, $poids, $taille, $poste)) {
            $success = 'Profil sauvegardé avec succès !';
            
            // Recharger le profil
            $userProfile = getUserProfile($userId);
            
            // Rediriger vers le programme après 2 secondes
            header('Refresh: 2; URL=program.php');
        } else {
            $error = 'Erreur lors de la sauvegarde du profil';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="profile-box">
            <h1>Mon Profil</h1>
            <p class="subtitle">Complétez vos informations pour obtenir un programme personnalisé</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo cleanOutput($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo cleanOutput($success); ?>
                    <br>Redirection vers votre programme...
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="profile-form">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input 
                        type="text" 
                        id="nom" 
                        name="nom" 
                        required
                        value="<?php echo cleanOutput($userProfile['nom'] ?? ''); ?>"
                        placeholder="Ex: Dupont"
                    >
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input 
                        type="text" 
                        id="prenom" 
                        name="prenom" 
                        required
                        value="<?php echo cleanOutput($userProfile['prenom'] ?? ''); ?>"
                        placeholder="Ex: Jean"
                    >
                </div>

                <div class="form-group">
                    <label for="poids">Poids (kg)</label>
                    <input 
                        type="number" 
                        id="poids" 
                        name="poids" 
                        step="0.1" 
                        min="30" 
                        max="200" 
                        required
                        value="<?php echo $userProfile['poids'] ?? ''; ?>"
                        placeholder="Ex: 75.5"
                    >
                </div>
                
                <div class="form-group">
                    <label for="taille">Taille (cm)</label>
                    <input 
                        type="number" 
                        id="taille" 
                        name="taille" 
                        min="150" 
                        max="250" 
                        required
                        value="<?php echo $userProfile['taille'] ?? ''; ?>"
                        placeholder="Ex: 185"
                    >
                </div>
                
                <div class="form-group">
                    <label for="poste">Poste de jeu</label>
                    <select id="poste" name="poste" required>
                        <option value="">-- Choisissez votre poste --</option>
                        <option value="1" <?php echo ($userProfile['poste'] ?? '') == 1 ? 'selected' : ''; ?>>
                            1 - Meneur (Point Guard)
                        </option>
                        <option value="2" <?php echo ($userProfile['poste'] ?? '') == 2 ? 'selected' : ''; ?>>
                            2 - Arrière (Shooting Guard)
                        </option>
                        <option value="3" <?php echo ($userProfile['poste'] ?? '') == 3 ? 'selected' : ''; ?>>
                            3 - Ailier (Small Forward)
                        </option>
                        <option value="4" <?php echo ($userProfile['poste'] ?? '') == 4 ? 'selected' : ''; ?>>
                            4 - Ailier Fort (Power Forward)
                        </option>
                        <option value="5" <?php echo ($userProfile['poste'] ?? '') == 5 ? 'selected' : ''; ?>>
                            5 - Pivot (Center)
                        </option>
                    </select>
                </div>
                
                <div class="info-box">
                    <h3>📋 À quoi sert cette information ?</h3>
                    <p>
                        Votre poste de jeu détermine le programme d'entraînement qui vous sera proposé.
                        Chaque poste a des besoins spécifiques en termes de compétences et de conditionnement physique.
                    </p>
                </div>
                
                <button type="submit" class="btn btn-primary">Sauvegarder mon profil</button>
            </form>
        </div>
    </div>
</body>
</html>