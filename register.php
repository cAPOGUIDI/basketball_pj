<?php
require_once 'includes/auth.php';

startSession();

// Si déjà connecté, rediriger vers le dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Vérifier que les mots de passe correspondent
    if ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        $result = registerUser($email, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            // Connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_email'] = $email;
            
            // Rediriger vers la page de profil après 2 secondes
            header('Refresh: 2; URL=profile.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>🏀 Basketball Training</h1>
            <h2>Créer un compte</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo cleanOutput($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo cleanOutput($success); ?>
                    <br>Redirection vers votre profil...
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="votre@email.com"
                        value="<?php echo isset($email) ? cleanOutput($email) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="8"
                        placeholder="Minimum 8 caractères"
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        minlength="8"
                        placeholder="Retapez votre mot de passe"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
            
            <p class="auth-link">
                Vous avez déjà un compte ? <a href="login.php">Se connecter</a>
            </p>
            
            <p class="auth-link">
                <a href="index.php">← Retour à l'accueil</a>
            </p>
        </div>
    </div>
</body>
</html>