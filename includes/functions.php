<?php
/**
 * Fonctions utilitaires pour la gestion des exercices et profils
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Sauvegarde ou met à jour le profil d'un utilisateur
 * @param int $userId
 * @param float $poids
 * @param int $taille
 * @param int $poste
 * @return bool
 */
function saveProfile($userId, $poids, $taille, $poste) {
    $db = getDBConnection();
    
    // Validation des données
    if ($poste < 1 || $poste > 5) {
        return false;
    }
    
    // Vérifier si le profil existe déjà
    $stmt = $db->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        // Mise à jour
        $stmt = $db->prepare("
            UPDATE profiles 
            SET poids = ?, taille = ?, poste = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE user_id = ?
        ");
        return $stmt->execute([$poids, $taille, $poste, $userId]);
    } else {
        // Insertion
        $stmt = $db->prepare("
            INSERT INTO profiles (user_id, poids, taille, poste) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $poids, $taille, $poste]);
    }
}

/**
 * Récupère les exercices publics par catégorie
 * @param string $categoryName - Nom de la catégorie
 * @return array
 */
function getPublicExercisesByCategory($categoryName) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("
        SELECT e.* 
        FROM exercices e
        JOIN categories c ON e.category_id = c.id
        WHERE c.nom = ? AND c.is_public = TRUE
        ORDER BY e.titre
    ");
    $stmt->execute([$categoryName]);
    
    return $stmt->fetchAll();
}

/**
 * Récupère le programme personnalisé selon le poste
 * @param int $poste - Numéro du poste (1-5)
 * @return array
 */
function getPersonalizedProgram($poste) {
    $db = getDBConnection();
    
    // Mapping des postes vers les noms de catégories
    $posteCategories = [
        1 => 'Programme Meneur',
        2 => 'Programme Arrière',
        3 => 'Programme Ailier',
        4 => 'Programme Ailier Fort',
        5 => 'Programme Pivot'
    ];
    
    $categoryName = $posteCategories[$poste] ?? null;
    
    if (!$categoryName) {
        return [];
    }
    
    $stmt = $db->prepare("
        SELECT e.* 
        FROM exercices e
        JOIN categories c ON e.category_id = c.id
        WHERE c.nom = ?
        ORDER BY e.difficulte, e.titre
    ");
    $stmt->execute([$categoryName]);
    
    return $stmt->fetchAll();
}

/**
 * Récupère toutes les catégories publiques
 * @return array
 */
function getPublicCategories() {
    $db = getDBConnection();
    
    $stmt = $db->query("
        SELECT * FROM categories 
        WHERE is_public = TRUE 
        ORDER BY nom
    ");
    
    return $stmt->fetchAll();
}

/**
 * Récupère un exercice par son ID
 * @param int $exerciceId
 * @return array|null
 */
function getExerciseById($exerciceId) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT * FROM exercices WHERE id = ?");
    $stmt->execute([$exerciceId]);
    
    return $stmt->fetch();
}

/**
 * Ajoute un exercice aux favoris
 * @param int $userId
 * @param int $exerciceId
 * @return bool
 */
function addToFavorites($userId, $exerciceId) {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("
            INSERT INTO favoris (user_id, exercice_id) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$userId, $exerciceId]);
    } catch (PDOException $e) {
        // L'exercice est peut-être déjà en favori (contrainte UNIQUE)
        return false;
    }
}

/**
 * Récupère les exercices favoris d'un utilisateur
 * @param int $userId
 * @return array
 */
function getUserFavorites($userId) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("
        SELECT e.* 
        FROM exercices e
        JOIN favoris f ON e.id = f.exercice_id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll();
}

/**
 * Nettoie et sécurise une chaîne pour l'affichage HTML
 * @param string $str
 * @return string
 */
function cleanOutput($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Convertit le numéro de poste en nom
 * @param int $poste
 * @return string
 */
function getPosteName($poste) {
    $postes = [
        1 => 'Meneur',
        2 => 'Arrière',
        3 => 'Ailier',
        4 => 'Ailier Fort',
        5 => 'Pivot'
    ];
    
    return $postes[$poste] ?? 'Non défini';
}

/**
 * Formate la durée en minutes:secondes
 * @param int $seconds
 * @return string
 */
function formatDuration($seconds) {
    if (!$seconds) return 'N/A';
    
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    
    if ($minutes > 0) {
        return sprintf("%d min %02d sec", $minutes, $secs);
    }
    
    return sprintf("%d sec", $secs);
}
?>