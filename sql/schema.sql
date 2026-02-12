-- ================================================
-- SCHÉMA DE BASE DE DONNÉES POSTGRESQL
-- Site Web Basketball Training
-- Version complète avec toutes les modifications
-- ================================================

-- Table des utilisateurs
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
);

-- Table des profils utilisateurs (avec nom et prénom)
CREATE TABLE profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    poids DECIMAL(5,2), -- En kg (ex: 85.50)
    taille INTEGER, -- En cm (ex: 185)
    poste INTEGER CHECK (poste BETWEEN 1 AND 5), -- 1=meneur, 2=arrière, 3=ailier, 4=ailier fort, 5=pivot
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories d'exercices
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT TRUE -- True = accessible sans connexion
);

-- Insertion des catégories de base
INSERT INTO categories (nom, description, is_public) VALUES
('Étirements', 'Exercices d''étirement pour la flexibilité', TRUE),
('Pré-game', 'Routines d''échauffement avant match', TRUE),
('Genoux', 'Exercices de soulagement pour les genoux', TRUE),
('Chevilles', 'Renforcement et soulagement des chevilles', TRUE),
('Poignets', 'Exercices pour les poignets', TRUE),
('Épaules', 'Renforcement et soulagement des épaules', TRUE),
('Programme Meneur', 'Programme spécifique poste 1', FALSE),
('Programme Arrière', 'Programme spécifique poste 2', FALSE),
('Programme Ailier', 'Programme spécifique poste 3', FALSE),
('Programme Ailier Fort', 'Programme spécifique poste 4', FALSE),
('Programme Pivot', 'Programme spécifique poste 5', FALSE);

-- Table des exercices (avec image_url et video_url)
CREATE TABLE exercices (
    id SERIAL PRIMARY KEY,
    category_id INTEGER REFERENCES categories(id) ON DELETE CASCADE,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    duree INTEGER, -- Durée en secondes
    repetitions INTEGER,
    video_url VARCHAR(500), -- URL de la vidéo
    image_url VARCHAR(500), -- URL de l'image (fallback)
    difficulte VARCHAR(20) CHECK (difficulte IN ('facile', 'moyen', 'difficile')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exemples d'exercices publics avec images
INSERT INTO exercices (category_id, titre, description, duree, difficulte, image_url) VALUES
(1, 'Étirement quadriceps', 'Debout, plier une jambe vers l''arrière et tenir la cheville', 30, 'facile', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=500'),
(1, 'Étirement ischio-jambiers', 'Assis, jambes tendues, toucher les pieds', 30, 'facile', 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=500'),
(2, 'Jogging léger', 'Course à faible intensité autour du terrain', 300, 'facile', 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=500'),
(2, 'Montées de genoux', 'Course avec genoux hauts', 60, 'moyen', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500'),
(3, 'Renforcement VMO', 'Contractions du quadriceps jambe tendue', 45, 'moyen', 'https://images.unsplash.com/photo-1546483875-ad9014c88eba?w=500'),
(4, 'Rotation cheville', 'Rotations douces dans les deux sens', 30, 'facile', 'https://images.unsplash.com/photo-1606889464198-fcb18894cf50?w=500'),
(5, 'Flexions poignet', 'Flexions et extensions avec poids léger', 60, 'facile', 'https://images.unsplash.com/photo-1598971639058-fab3c3109a00?w=500'),
(6, 'Rotations épaules', 'Cercles avec les bras', 45, 'facile', 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=500');

-- Exemples d'exercices par poste avec images et vidéos
INSERT INTO exercices (category_id, titre, description, duree, repetitions, difficulte, image_url, video_url) VALUES
-- Meneur (1)
(7, 'Dribble en slalom', 'Dribble entre cônes à vitesse progressive', 180, 10, 'moyen', 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
(7, 'Tirs en course', 'Layups alternés des deux côtés', 300, 20, 'moyen', 'https://images.unsplash.com/photo-3551675/3551675-uhd_2560_1440_25fps.mp4', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
-- Arrière (2)
(8, 'Tirs en suspension', 'Jump shots depuis différentes positions', 300, 15, 'moyen', 'https://images.unsplash.com/photo-1519766304817-4f37bda74a26?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
(8, 'Sprint défensif', 'Sprints latéraux et retours', 120, 8, 'difficile', 'https://images.unsplash.com/photo-1574623452334-1e0ac2b3ccb4?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'),
-- Ailier (3)
(9, 'Tirs à 3 points', 'Tirs depuis les 5 positions clés', 300, 25, 'moyen', 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'),
(9, 'Pénétrations', 'Drives vers le panier avec finition', 240, 15, 'difficile', 'https://images.unsplash.com/photo-1559692048-79a3f837883d?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'),
-- Ailier fort (4)
(10, 'Post moves', 'Mouvements au poste bas', 300, 12, 'difficile', 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4'),
(10, 'Rebonds', 'Exercices de timing et positionnement', 180, 20, 'moyen', 'https://images.unsplash.com/photo-1515523110800-9415d13b84a8?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
-- Pivot (5)
(11, 'Hook shot', 'Tir en crochet des deux mains', 240, 20, 'difficile', 'https://images.unsplash.com/photo-1577223625816-7546f7d5b65c?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4'),
(11, 'Défense poste', 'Positionnement et déplacements', 300, 15, 'moyen', 'https://images.unsplash.com/photo-1608245449230-4ac19066d2d0?w=500', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4');

-- Table de liaison entre profils et exercices favoris
CREATE TABLE favoris (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    exercice_id INTEGER REFERENCES exercices(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, exercice_id)
);

-- Table pour suivre les progrès
CREATE TABLE progres (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    exercice_id INTEGER REFERENCES exercices(id) ON DELETE CASCADE,
    date_exercice DATE DEFAULT CURRENT_DATE,
    completed BOOLEAN DEFAULT FALSE,
    notes TEXT
);

-- Table pour les messages de contact
CREATE TABLE contacts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type_message VARCHAR(50) CHECK (type_message IN ('avis', 'suggestion', 'bug', 'question', 'autre')),
    statut VARCHAR(20) DEFAULT 'nouveau' CHECK (statut IN ('nouveau', 'lu', 'traite')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour optimiser les performances
CREATE INDEX idx_profiles_user_id ON profiles(user_id);
CREATE INDEX idx_profiles_poste ON profiles(poste);
CREATE INDEX idx_exercices_category ON exercices(category_id);
CREATE INDEX idx_favoris_user ON favoris(user_id);
CREATE INDEX idx_progres_user ON progres(user_id);
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_statut ON contacts(statut);
CREATE INDEX idx_contacts_created ON contacts(created_at DESC);

-- Vue pour obtenir les informations complètes d'un utilisateur
CREATE VIEW user_full_info AS
SELECT 
    u.id,
    u.email,
    u.created_at,
    u.last_login,
    p.nom,
    p.prenom,
    p.poids,
    p.taille,
    p.poste,
    CASE p.poste
        WHEN 1 THEN 'Meneur'
        WHEN 2 THEN 'Arrière'
        WHEN 3 THEN 'Ailier'
        WHEN 4 THEN 'Ailier Fort'
        WHEN 5 THEN 'Pivot'
    END as poste_nom
FROM users u
LEFT JOIN profiles p ON u.id = p.user_id;

-- Confirmation
SELECT 'Base de données créée avec succès !' as message;