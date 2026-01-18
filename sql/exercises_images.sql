-- ================================================
-- AJOUT D'IMAGES AUX EXERCICES
-- Utilise des URLs Unsplash pour des images de qualité
-- ================================================

-- Mettre à jour les exercices d'étirement
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=500' 
WHERE titre = 'Étirement quadriceps';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=500' 
WHERE titre = 'Étirement ischio-jambiers';

-- Exercices pré-game
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=500' 
WHERE titre = 'Jogging léger';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500' 
WHERE titre = 'Montées de genoux';

-- Exercices spécifiques
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1546483875-ad9014c88eba?w=500' 
WHERE titre = 'Renforcement VMO';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1606889464198-fcb18894cf50?w=500' 
WHERE titre = 'Rotation cheville';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1598971639058-fab3c3109a00?w=500' 
WHERE titre = 'Flexions poignet';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=500' 
WHERE titre = 'Rotations épaules';

-- Exercices Meneur
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=500' 
WHERE titre = 'Dribble en slalom';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1608245449230-4ac19066d2d0?w=500' 
WHERE titre = 'Tirs en course';

-- Exercices Arrière
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1519766304817-4f37bda74a26?w=500' 
WHERE titre = 'Tirs en suspension';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1574623452334-1e0ac2b3ccb4?w=500' 
WHERE titre = 'Sprint défensif';

-- Exercices Ailier
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=500' 
WHERE titre = 'Tirs à 3 points';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1559692048-79a3f837883d?w=500' 
WHERE titre = 'Pénétrations';

-- Exercices Ailier Fort
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=500' 
WHERE titre = 'Post moves';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1515523110800-9415d13b84a8?w=500' 
WHERE titre = 'Rebonds';

-- Exercices Pivot
UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1577223625816-7546f7d5b65c?w=500' 
WHERE titre = 'Hook shot';

UPDATE exercices SET image_url = 'https://images.unsplash.com/photo-1608245449230-4ac19066d2d0?w=500' 
WHERE titre = 'Défense poste';

-- Vérifier les mises à jour
SELECT titre, image_url FROM exercices WHERE image_url IS NOT NULL;