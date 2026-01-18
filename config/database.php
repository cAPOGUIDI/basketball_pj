<?php
/**
 * Configuration de la connexion PostgreSQL
 * Ce fichier établit la connexion à la base de données
 */

// Informations de connexion - À ADAPTER selon votre configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'basketball_training');
define('DB_USER', 'basket_user');
define('DB_PASS', 'admin');

// Fonction pour obtenir une connexion PDO à PostgreSQL
function getDBConnection() {
    try {
        // DSN (Data Source Name) pour PostgreSQL
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        
        // Options PDO pour améliorer la sécurité et le comportement
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne des tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES => false, // Utilise les vraies requêtes préparées
        ];
        
        // Création de la connexion
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // En production, ne jamais afficher les détails de l'erreur
        // Logger l'erreur dans un fichier au lieu de l'afficher
        error_log("Erreur de connexion BDD: " . $e->getMessage());
        die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
    }
}

// Test de connexion (à commenter en production)
// $db = getDBConnection();
// echo "Connexion réussie !";
?>