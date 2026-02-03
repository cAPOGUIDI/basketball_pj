
<?php
require_once 'includes/auth.php'; 
require_once 'includes/functions.php'; 

startSession();

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        // Rediriger vers le dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Basketball Training</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>🏀 Basketball Training</h1>
            <h2>Connexion</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo cleanOutput($error); ?>
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
                        placeholder="Votre mot de passe"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            
            <p class="auth-link">
                Pas encore de compte ? <a href="register.php">S'inscrire</a>
            </p>
            
            <p class="auth-link">
                <a href="index.php">← Retour à l'accueil</a>
            </p>
        </div>
    </div>
</body>
</html>