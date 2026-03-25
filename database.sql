-- ============================================================
-- SCRIPT SQL - Site d'actualité dynamique
-- Étudiant 1 : Création de la base de données
-- ============================================================

-- On crée la base de données si elle n'existe pas encore
CREATE DATABASE IF NOT EXISTS site_actualite;

-- On sélectionne cette base pour travailler dedans
USE site_actualite;

-- ============================================================
-- TABLE : categories
-- On crée d'abord cette table car la table articles en dépend
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- Identifiant unique, s'incrémente tout seul
    nom VARCHAR(100) NOT NULL            -- Nom de la catégorie (ex: Sport, Politique...)
);

-- ============================================================
-- TABLE : users (utilisateurs)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,  -- UNIQUE : deux utilisateurs ne peuvent pas avoir le même login
    mot_de_passe VARCHAR(255) NOT NULL,  -- On stocke le mot de passe hashé (jamais en clair !)
    role ENUM('visiteur', 'editeur', 'administrateur') NOT NULL DEFAULT 'editeur'
    -- ENUM : le rôle ne peut être que l'une de ces 3 valeurs
);

-- ============================================================
-- TABLE : articles
-- Dépend de users (auteur) et categories
-- ============================================================
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description_courte VARCHAR(500) NOT NULL,  -- Résumé court affiché sur l'accueil
    contenu TEXT NOT NULL,                     -- Contenu complet de l'article
    image VARCHAR(255) DEFAULT NULL,           -- Nom du fichier image ou URL
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP, -- Date auto à la création
    id_categorie INT NOT NULL,
    id_auteur INT NOT NULL,

    -- Clés étrangères : lien avec les autres tables
    FOREIGN KEY (id_categorie) REFERENCES categories(id),
    FOREIGN KEY (id_auteur) REFERENCES users(id)
);

-- ============================================================
-- DONNÉES INITIALES : Catégories
-- ============================================================
INSERT INTO categories (nom) VALUES
('Technologie'),
('Sport'),
('Politique'),
('Éducation'),
('Culture');

-- ============================================================
-- DONNÉES INITIALES : Utilisateurs
-- Les mots de passe sont hashés avec password_hash() en PHP
-- Ici on simule ce hash pour "password123" 
-- (en vrai tu utilises le script PHP ci-dessous pour générer les vrais hashs)
-- ============================================================

-- Pour générer un vrai hash, exécute ce script PHP une fois :
-- <?php echo password_hash('password123', PASSWORD_DEFAULT); ?>

-- Administrateur (login: admin / mot de passe: admin123)
INSERT INTO users (nom, prenom, login, mot_de_passe, role) VALUES
('Diallo', 'Mamadou', 'admin', '$2b$12$kUHKdbDxdQ7TiMHWcD3Zi.jurG14Xgj8w3EHSRLBStL6hzNvQrGfa', 'administrateur'),
-- Éditeur (login: editeur1 / mot de passe: editeur123)
('Sow', 'Fatou', 'editeur1', '$2b$12$OiHgh6.YCNXhoUaCPcZVFOyQKIfO4s31h11hT5fpWhdZtgdpI4CTq', 'editeur');

-- Note : les hash ci-dessus sont des hash BCrypt générés avec password_hash() de PHP
-- admin    → mot de passe : admin123
-- editeur1 → mot de passe : editeur123

-- ============================================================
-- DONNÉES INITIALES : Articles de test
-- ============================================================
INSERT INTO articles (titre, description_courte, contenu, id_categorie, id_auteur) VALUES
(
    'Lancement du nouveau smartphone X15',
    'La marque TechPlus vient de présenter son dernier modèle avec des fonctionnalités révolutionnaires.',
    'La marque TechPlus a officiellement lancé son nouveau smartphone X15 lors d\'une conférence de presse tenue hier. Ce modèle dispose d\'un processeur ultra-rapide, d\'un appareil photo de 200 mégapixels et d\'une batterie de 6000 mAh. Le prix de vente est fixé à 450 000 FCFA.',
    1, -- Catégorie Technologie
    2  -- Auteur : editeur1
),
(
    'Victoire de l\'équipe nationale de football',
    'L\'équipe nationale a remporté le match amical contre le Mali sur le score de 2-1.',
    'Dans un match très disputé, l\'équipe nationale a dominé le Mali pendant toute la rencontre. Les buts ont été marqués par Keita à la 23ème minute et par Ndiaye à la 67ème minute. Ce résultat est encourageant avant la prochaine compétition continentale.',
    2, -- Catégorie Sport
    2
),
(
    'Réforme du système éducatif annoncée',
    'Le ministère de l\'Éducation a présenté un nouveau programme scolaire qui entrera en vigueur l\'an prochain.',
    'Le ministre de l\'Éducation nationale a présenté hier les grandes lignes de la réforme du système éducatif. Parmi les changements notables : l\'introduction de l\'informatique dès le primaire, la révision des programmes de mathématiques et une nouvelle approche pédagogique axée sur la pratique.',
    4, -- Catégorie Éducation
    2
);