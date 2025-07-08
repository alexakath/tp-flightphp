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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (type_pret_id) REFERENCES types_pret(id) ON DELETE RESTRICT
);

-- Table pour les échéances de remboursement
CREATE TABLE echeances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pret_id INT NOT NULL,
    numero_echeance INT NOT NULL, -- 1, 2, 3, etc.
    date_echeance DATE NOT NULL,
    montant_capital DECIMAL(10, 2) NOT NULL, -- Part du capital à rembourser
    montant_interet DECIMAL(10, 2) NOT NULL, -- Part des intérêts
    montant_total DECIMAL(10, 2) NOT NULL, -- Total de l'échéance
    date_paiement DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE,
    INDEX idx_pret_echeance (pret_id, numero_echeance),
    INDEX idx_date_echeance (date_echeance)
);

-- Table pour les paiements d'échéances
CREATE TABLE paiements_echeances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    echeance_id INT NOT NULL,
    montant_paye DECIMAL(10, 2) NOT NULL,
    date_paiement DATE NOT NULL,
    mode_paiement ENUM('especes', 'virement', 'cheque', 'autre') DEFAULT 'especes',
    reference_paiement VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (echeance_id) REFERENCES echeances(id) ON DELETE CASCADE,
    INDEX idx_date_paiement (date_paiement)
);

-- Table pour calculer et stocker les intérêts gagnés par mois
CREATE TABLE interets_mensuels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annee INT NOT NULL,
    mois INT NOT NULL,
    total_interets_dus DECIMAL(10, 2) NOT NULL DEFAULT 0, -- Intérêts dus ce mois-ci
    total_interets_payes DECIMAL(10, 2) NOT NULL DEFAULT 0, -- Intérêts réellement payés
    total_interets_en_retard DECIMAL(10, 2) NOT NULL DEFAULT 0, -- Intérêts en retard
    nombre_echeances_dues INT NOT NULL DEFAULT 0,
    nombre_echeances_payees INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_mois_annee (annee, mois),
    INDEX idx_annee_mois (annee, mois)
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
