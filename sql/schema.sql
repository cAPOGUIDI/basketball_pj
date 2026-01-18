-- ================================================
-- SCHÉMA DE BASE DE DONNÉES POSTGRESQL
-- Site Web Basketball Training
-- ================================================

-- Table des utilisateurs
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
);

-- Table des profils utilisateurs
CREATE TABLE profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
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

-- Table des exercices
CREATE TABLE exercices (
    id SERIAL PRIMARY KEY,
    category_id INTEGER REFERENCES categories(id) ON DELETE CASCADE,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    duree INTEGER, -- Durée en secondes
    repetitions INTEGER,
    video_url VARCHAR(500),
    image_url VARCHAR(500),
    difficulte VARCHAR(20) CHECK (difficulte IN ('facile', 'moyen', 'difficile')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exemples d'exercices publics
INSERT INTO exercices (category_id, titre, description, duree, difficulte) VALUES
(1, 'Étirement quadriceps', 'Debout, plier une jambe vers l''arrière et tenir la cheville', 30, 'facile'),
(1, 'Étirement ischio-jambiers', 'Assis, jambes tendues, toucher les pieds', 30, 'facile'),
(2, 'Jogging léger', 'Course à faible intensité autour du terrain', 300, 'facile'),
(2, 'Montées de genoux', 'Course avec genoux hauts', 60, 'moyen'),
(3, 'Renforcement VMO', 'Contractions du quadriceps jambe tendue', 45, 'moyen'),
(4, 'Rotation cheville', 'Rotations douces dans les deux sens', 30, 'facile'),
(5, 'Flexions poignet', 'Flexions et extensions avec poids léger', 60, 'facile'),
(6, 'Rotations épaules', 'Cercles avec les bras', 45, 'facile');

-- Exemples d'exercices par poste
INSERT INTO exercices (category_id, titre, description, duree, repetitions, difficulte) VALUES
-- Meneur (1)
(7, 'Dribble en slalom', 'Dribble entre cônes à vitesse progressive', 180, 10, 'moyen'),
(7, 'Tirs en course', 'Layups alternés des deux côtés', 300, 20, 'moyen'),
-- Arrière (2)
(8, 'Tirs en suspension', 'Jump shots depuis différentes positions', 300, 15, 'moyen'),
(8, 'Sprint défensif', 'Sprints latéraux et retours', 120, 8, 'difficile'),
-- Ailier (3)
(9, 'Tirs à 3 points', 'Tirs depuis les 5 positions clés', 300, 25, 'moyen'),
(9, 'Pénétrations', 'Drives vers le panier avec finition', 240, 15, 'difficile'),
-- Ailier fort (4)
(10, 'Post moves', 'Mouvements au poste bas', 300, 12, 'difficile'),
(10, 'Rebonds', 'Exercices de timing et positionnement', 180, 20, 'moyen'),
-- Pivot (5)
(11, 'Hook shot', 'Tir en crochet des deux mains', 240, 20, 'difficile'),
(11, 'Défense poste', 'Positionnement et déplacements', 300, 15, 'moyen');

-- Table de liaison entre profils et exercices favoris (optionnel)
CREATE TABLE favoris (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    exercice_id INTEGER REFERENCES exercices(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, exercice_id)
);

-- Table pour suivre les progrès (optionnel, pour évolution future)
CREATE TABLE progres (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    exercice_id INTEGER REFERENCES exercices(id) ON DELETE CASCADE,
    date_exercice DATE DEFAULT CURRENT_DATE,
    completed BOOLEAN DEFAULT FALSE,
    notes TEXT
);

-- Index pour optimiser les performances
CREATE INDEX idx_profiles_user_id ON profiles(user_id);
CREATE INDEX idx_profiles_poste ON profiles(poste);
CREATE INDEX idx_exercices_category ON exercices(category_id);
CREATE INDEX idx_favoris_user ON favoris(user_id);
CREATE INDEX idx_progres_user ON progres(user_id);

-- Vue pour obtenir les informations complètes d'un utilisateur
CREATE VIEW user_full_info AS
SELECT 
    u.id,
    u.email,
    u.created_at,
    u.last_login,
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