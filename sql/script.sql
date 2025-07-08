CREATE DATABASE finance CHARACTER SET utf8mb4;

USE finance;

CREATE TABLE fonds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(10, 2) NOT NULL,
    date_ajout DATE NOT NULL,
    description VARCHAR(255),
    solde_total DECIMAL (10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Table pour les types de prêts avec leurs taux
CREATE TABLE types_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux_annuel DECIMAL(5, 2) NOT NULL, -- Taux en pourcentage (ex: 5.50 pour 5,50%)
    duree_max_mois INT NOT NULL, -- Durée maximale en mois
    montant_min DECIMAL(10, 2) NOT NULL, -- Montant minimum du prêt
    montant_max DECIMAL(10, 2) NOT NULL, -- Montant maximum du prêt
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    type_pret_id INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    date_debut DATE NOT NULL,
    duree INT NOT NULL,
    statut ENUM('en_attente', 'approuve', 'rejete') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (type_pret_id) REFERENCES types_pret(id) ON DELETE RESTRICT
);
INSERT INTO client (nom) VALUES
('Alex Dupont'),
('Marie Lefèvre'),
('Jean Martin'),
('Sophie Bernard'),
('Luc Durand'),
('Claire Moreau'),
('Thomas Dubois'),
('Emma Lambert'),
('Paul Renault'),
('Julie Fournier');
