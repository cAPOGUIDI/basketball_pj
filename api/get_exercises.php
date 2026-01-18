<?php
/**
 * API pour récupérer les exercices par catégorie
 * Endpoint: api/get_exercises.php?category=NomCategorie
 */

require_once __DIR__ . '/../includes/functions.php';

// Configuration pour réponse JSON
header('Content-Type: application/json');

// Récupérer le paramètre de catégorie
$category = $_GET['category'] ?? '';

if (empty($category)) {
    echo json_encode([
        'success' => false,
        'message' => 'Catégorie non spécifiée'
    ]);
    exit();
}

try {
    // Récupérer les exercices de cette catégorie
    $exercises = getPublicExercisesByCategory($category);
    
    echo json_encode([
        'success' => true,
        'category' => $category,
        'exercises' => $exercises,
        'count' => count($exercises)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des exercices'
    ]);
}
?>