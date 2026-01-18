/**
 * BASKETBALL TRAINING - JavaScript Principal
 * Gestion des interactions côté client
 */

// ============================================
// CHARGEMENT DES EXERCICES PUBLICS
// ============================================

/**
 * Charge et affiche les exercices d'une catégorie
 * @param {string} categoryName - Nom de la catégorie
 */
async function loadExercises(categoryName) {
    try {
        // Créer un fichier PHP séparé pour l'API (api/get_exercises.php)
        const response = await fetch(`api/get_exercises.php?category=${encodeURIComponent(categoryName)}`);
        const data = await response.json();
        
        if (data.success) {
            displayExercises(categoryName, data.exercises);
        } else {
            alert('Erreur lors du chargement des exercices');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion au serveur');
    }
}

/**
 * Affiche les exercices dans le modal
 * @param {string} categoryName - Nom de la catégorie
 * @param {Array} exercises - Liste des exercices
 */
function displayExercises(categoryName, exercises) {
    const modal = document.getElementById('exerciseModal');
    const modalTitle = document.getElementById('modalTitle');
    const exerciseList = document.getElementById('exerciseList');
    
    // Mettre à jour le titre
    modalTitle.textContent = categoryName;
    
    // Vider la liste
    exerciseList.innerHTML = '';
    
    if (exercises.length === 0) {
        exerciseList.innerHTML = '<p>Aucun exercice disponible pour cette catégorie.</p>';
    } else {
        // Créer les cartes d'exercice
        exercises.forEach(exercise => {
            const exerciseCard = createExerciseCard(exercise);
            exerciseList.appendChild(exerciseCard);
        });
    }
    
    // Afficher le modal
    modal.style.display = 'block';
}

/**
 * Crée une carte d'exercice
 * @param {Object} exercise - Données de l'exercice
 * @returns {HTMLElement} - Élément DOM de la carte
 */
function createExerciseCard(exercise) {
    const card = document.createElement('div');
    card.className = 'exercise-item';
    
    // Ajouter une image si disponible
    if (exercise.image_url) {
        const imageContainer = document.createElement('div');
        imageContainer.style.cssText = `
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
        `;
        
        const img = document.createElement('img');
        img.src = exercise.image_url;
        img.alt = exercise.titre;
        img.style.cssText = `
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        `;
        img.onmouseover = () => img.style.transform = 'scale(1.1)';
        img.onmouseout = () => img.style.transform = 'scale(1)';
        
        imageContainer.appendChild(img);
        card.appendChild(imageContainer);
    }
    
    // En-tête
    const header = document.createElement('div');
    header.className = 'exercise-header';
    
    const title = document.createElement('h3');
    title.textContent = exercise.titre;
    header.appendChild(title);
    
    if (exercise.difficulte) {
        const badge = document.createElement('span');
        badge.className = `badge badge-${exercise.difficulte}`;
        badge.textContent = exercise.difficulte.charAt(0).toUpperCase() + exercise.difficulte.slice(1);
        header.appendChild(badge);
    }
    
    card.appendChild(header);
    
    // Description
    if (exercise.description) {
        const desc = document.createElement('p');
        desc.className = 'exercise-description';
        desc.textContent = exercise.description;
        card.appendChild(desc);
    }
    
    // Détails (durée, répétitions)
    const details = document.createElement('div');
    details.className = 'exercise-details';
    
    if (exercise.duree) {
        const duration = document.createElement('div');
        duration.className = 'detail';
        duration.innerHTML = `<span class="detail-icon">⏱️</span><span>${formatDuration(exercise.duree)}</span>`;
        details.appendChild(duration);
    }
    
    if (exercise.repetitions) {
        const reps = document.createElement('div');
        reps.className = 'detail';
        reps.innerHTML = `<span class="detail-icon">🔄</span><span>${exercise.repetitions} répétitions</span>`;
        details.appendChild(reps);
    }
    
    if (details.children.length > 0) {
        card.appendChild(details);
    }
    
    return card;
}

/**
 * Formate la durée en minutes:secondes
 * @param {number} seconds - Durée en secondes
 * @returns {string} - Durée formatée
 */
function formatDuration(seconds) {
    if (!seconds) return 'N/A';
    
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    
    if (minutes > 0) {
        return `${minutes} min ${secs.toString().padStart(2, '0')} sec`;
    }
    
    return `${secs} sec`;
}

/**
 * Ferme le modal
 */
function closeModal() {
    const modal = document.getElementById('exerciseModal');
    modal.style.display = 'none';
}

// ============================================
// GESTION DU MODAL
// ============================================

// Fermer le modal si on clique en dehors
window.onclick = function(event) {
    const modal = document.getElementById('exerciseModal');
    if (event.target === modal) {
        closeModal();
    }
};

// Fermer avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// ============================================
// VALIDATION DES FORMULAIRES
// ============================================

/**
 * Valide le formulaire de profil
 */
function validateProfileForm() {
    const poids = parseFloat(document.getElementById('poids').value);
    const taille = parseInt(document.getElementById('taille').value);
    const poste = parseInt(document.getElementById('poste').value);
    
    if (poids < 30 || poids > 200) {
        alert('Le poids doit être entre 30 et 200 kg');
        return false;
    }
    
    if (taille < 150 || taille > 250) {
        alert('La taille doit être entre 150 et 250 cm');
        return false;
    }
    
    if (poste < 1 || poste > 5) {
        alert('Veuillez sélectionner un poste valide');
        return false;
    }
    
    return true;
}

// ============================================
// GESTION DES COOKIES (pour "Se souvenir de moi")
// ============================================

/**
 * Définit un cookie
 * @param {string} name - Nom du cookie
 * @param {string} value - Valeur du cookie
 * @param {number} days - Durée de vie en jours
 */
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Strict";
}

/**
 * Récupère un cookie
 * @param {string} name - Nom du cookie
 * @returns {string|null} - Valeur du cookie ou null
 */
function getCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
    
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        if (cookie.indexOf(nameEQ) === 0) {
            return cookie.substring(nameEQ.length);
        }
    }
    
    return null;
}

/**
 * Supprime un cookie
 * @param {string} name - Nom du cookie
 */
function deleteCookie(name) {
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
}

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

/**
 * Affiche un message de notification temporaire
 * @param {string} message - Message à afficher
 * @param {string} type - Type de notification (success, error, info)
 */
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Ajouter les styles inline
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '1rem 1.5rem';
    notification.style.borderRadius = '8px';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '300px';
    notification.style.animation = 'slideIn 0.3s ease';
    
    // Couleurs selon le type
    if (type === 'success') {
        notification.style.backgroundColor = '#d4edda';
        notification.style.color = '#155724';
        notification.style.border = '1px solid #c3e6cb';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#f8d7da';
        notification.style.color = '#721c24';
        notification.style.border = '1px solid #f5c6cb';
    } else {
        notification.style.backgroundColor = '#d1ecf1';
        notification.style.color = '#0c5460';
        notification.style.border = '1px solid #bee5eb';
    }
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Retirer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Ajouter les animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Basketball Training - Application chargée');
    
    // Vérifier si on est sur la page de profil
    const profileForm = document.querySelector('.profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            if (!validateProfileForm()) {
                e.preventDefault();
            }
        });
    }
});