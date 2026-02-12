<?php
/**
 * Fonctions d'authentification et de gestion des utilisateurs
 */

require_once __DIR__ . '/../config/database.php';
/**
 * Vérifie si l'utilisateur est administrateur
 * @return bool
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = getCurrentUserId();
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === 'A';
}

/**
 * Protège une page admin (redirige si non admin)
 * @param string $redirectUrl
 */
function requireAdmin($redirectUrl = 'dashboard.php') {
    if (!isAdmin()) {
        header("Location: $redirectUrl");
        exit();
    }
}

// Démarre la session si elle n'est pas déjà démarrée
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration sécurisée des sessions
        ini_set('session.cookie_httponly', 1); // Empêche l'accès JavaScript aux cookies
        ini_set('session.use_only_cookies', 1); // Utilise uniquement les cookies
        ini_set('session.cookie_secure', 0); // Mettre à 1 si HTTPS activé
        
        session_start();
    }
}

/**
 * Inscription d'un nouvel utilisateur
 * @param string $email - Email de l'utilisateur
 * @param string $password - Mot de passe en clair
 * @return array - ['success' => bool, 'message' => string, 'user_id' => int]
 */
function registerUser($email, $password) {
    $db = getDBConnection();
    
    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email invalide'];
    }
    
    // Validation du mot de passe (minimum 8 caractères)
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères'];
    }
    
    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
    }
    
    // Hash du mot de passe avec bcrypt (très sécurisé)
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertion du nouvel utilisateur
    $stmt = $db->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?) RETURNING id");
    $stmt->execute([$email, $passwordHash]);
    $result = $stmt->fetch();
    
    return [
        'success' => true, 
        'message' => 'Inscription réussie',
        'user_id' => $result['id']
    ];
}

/**
 * Connexion d'un utilisateur
 * @param string $email
 * @param string $password
 * @return array - ['success' => bool, 'message' => string, 'user' => array]
 */
function loginUser($email, $password) {
    $db = getDBConnection();
    
    // Récupération de l'utilisateur par email
    $stmt = $db->prepare("SELECT id, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Vérifier si l'utilisateur existe
    if (!$user) {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
    }
    
    // Vérifier le mot de passe avec password_verify
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
    }
    
    // Mettre à jour la date de dernière connexion
    $updateStmt = $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    // Démarrer la session et enregistrer l'utilisateur
    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    
    return [
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ];
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

/**
 * Récupère l'ID de l'utilisateur connecté
 * @return int|null
 */
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Déconnexion de l'utilisateur
 */
function logoutUser() {
    startSession();
    
    // Détruire toutes les variables de session
    $_SESSION = [];
    
    // Détruire le cookie de session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Détruire la session
    session_destroy();
}

/**
 * Protège une page (redirige vers login si non connecté)
 * @param string $redirectUrl - URL de redirection si non connecté
 */
function requireLogin($redirectUrl = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit();
    }
}

/**
 * Vérifie si l'utilisateur a complété son profil
 * @param int $userId
 * @return bool
 */
function hasCompletedProfile($userId) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT poste FROM profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
    // Le profil est complet si le poste est renseigné
    return $profile && $profile['poste'] !== null;
}

/**
 * Récupère le profil complet d'un utilisateur
 * @param int $userId
 * @return array|null
 */
function getUserProfile($userId) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT * FROM user_full_info WHERE id = ?");
    $stmt->execute([$userId]);
    
    return $stmt->fetch();
}
?>