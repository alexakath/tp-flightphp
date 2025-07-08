-- Données de test pour les rapports d'intérêts mensuels
-- À exécuter après avoir créé la base de données avec script.sql

-- Vider les tables existantes (optionnel)
-- DELETE FROM echeances;
-- DELETE FROM pret;
-- DELETE FROM types_pret;
-- DELETE FROM fonds;

-- 1. Ajouter des fonds initiaux
INSERT INTO fonds (montant, date_ajout, description, solde_total) VALUES
(500000.00, '2024-01-01', 'Capital initial', 500000.00),
(100000.00, '2024-02-15', 'Apport supplémentaire', 600000.00),
(50000.00, '2024-03-10', 'Bénéfices réinvestis', 650000.00);

-- 2. Créer différents types de prêts
INSERT INTO types_pret (nom, taux_annuel, duree_max_mois, montant_min, montant_max) VALUES
('Prêt Personnel', 8.50, 60, 1000.00, 50000.00),
('Prêt Auto', 6.75, 84, 5000.00, 80000.00),
('Prêt Immobilier', 4.25, 360, 50000.00, 500000.00),
('Prêt Entreprise', 7.90, 120, 10000.00, 200000.00),
('Crédit Consommation', 9.75, 48, 500.00, 25000.00),
('Prêt Étudiant', 3.50, 144, 1000.00, 40000.00);

-- 3. Créer des prêts approuvés sur différentes périodes
-- Prêts commencés en 2024
INSERT INTO pret (client_id, type_pret_id, montant, date_debut, duree, statut) VALUES
-- Janvier 2024
(1, 1, 15000.00, '2024-01-15', 36, 'approuve'),
(2, 2, 25000.00, '2024-01-20', 60, 'approuve'),
(3, 3, 150000.00, '2024-01-25', 240, 'approuve'),

-- Février 2024
(4, 4, 75000.00, '2024-02-05', 84, 'approuve'),
(5, 5, 8000.00, '2024-02-12', 24, 'approuve'),
(6, 1, 12000.00, '2024-02-18', 48, 'approuve'),

-- Mars 2024
(7, 2, 35000.00, '2024-03-08', 72, 'approuve'),
(8, 6, 20000.00, '2024-03-15', 96, 'approuve'),
(9, 1, 18000.00, '2024-03-22', 42, 'approuve'),

-- Avril 2024
(10, 3, 200000.00, '2024-04-10', 300, 'approuve'),
(1, 5, 5000.00, '2024-04-15', 18, 'approuve'),
(2, 4, 45000.00, '2024-04-20', 60, 'approuve'),

-- Mai 2024
(3, 1, 22000.00, '2024-05-05', 48, 'approuve'),
(4, 2, 28000.00, '2024-05-12', 66, 'approuve'),
(5, 6, 15000.00, '2024-05-18', 84, 'approuve'),

-- Juin 2024
(6, 3, 180000.00, '2024-06-03', 360, 'approuve'),
(7, 1, 16000.00, '2024-06-10', 36, 'approuve'),
(8, 5, 7500.00, '2024-06-15', 30, 'approuve'),

-- Juillet 2024
(9, 4, 60000.00, '2024-07-02', 72, 'approuve'),
(10, 2, 32000.00, '2024-07-08', 60, 'approuve'),
(1, 6, 25000.00, '2024-07-15', 120, 'approuve'),

-- Août 2024
(2, 1, 14000.00, '2024-08-05', 36, 'approuve'),
(3, 5, 9000.00, '2024-08-12', 24, 'approuve'),
(4, 3, 220000.00, '2024-08-18', 300, 'approuve'),

-- Septembre 2024
(5, 2, 40000.00, '2024-09-03', 72, 'approuve'),
(6, 4, 55000.00, '2024-09-10', 84, 'approuve'),
(7, 1, 19000.00, '2024-09-15', 42, 'approuve'),

-- Octobre 2024
(8, 6, 30000.00, '2024-10-02', 108, 'approuve'),
(9, 3, 175000.00, '2024-10-08', 240, 'approuve'),
(10, 5, 6500.00, '2024-10-15', 20, 'approuve'),

-- Novembre 2024
(1, 2, 38000.00, '2024-11-05', 72, 'approuve'),
(2, 4, 80000.00, '2024-11-12', 96, 'approuve'),
(3, 1, 17000.00, '2024-11-18', 36, 'approuve'),

-- Décembre 2024
(4, 6, 22000.00, '2024-12-03', 84, 'approuve'),
(5, 3, 190000.00, '2024-12-10', 360, 'approuve'),
(6, 5, 8500.00, '2024-12-15', 24, 'approuve'),

-- Prêts 2025 pour tester la continuité
(7, 1, 20000.00, '2025-01-05', 48, 'approuve'),
(8, 2, 45000.00, '2025-01-12', 84, 'approuve'),
(9, 4, 70000.00, '2025-01-18', 72, 'approuve'),

-- Quelques prêts à court terme qui se terminent rapidement
(10, 5, 3000.00, '2024-06-01', 6, 'approuve'),  -- Se termine en décembre 2024
(1, 5, 4000.00, '2024-07-01', 8, 'approuve'),   -- Se termine en mars 2025
(2, 5, 2500.00, '2024-08-01', 4, 'approuve'),   -- Se termine en décembre 2024
(3, 5, 3500.00, '2024-09-01', 6, 'approuve'),   -- Se termine en mars 2025
(4, 5, 5500.00, '2024-10-01', 12, 'approuve'),  -- Se termine en octobre 2025

-- Quelques prêts en attente ou rejetés (ne doivent pas apparaître dans les rapports)
(5, 1, 10000.00, '2024-12-20', 24, 'en_attente'),
(6, 2, 15000.00, '2024-12-22', 36, 'en_attente'),
(7, 3, 100000.00, '2024-12-25', 180, 'rejete'),
(8, 4, 30000.00, '2024-12-28', 48, 'rejete');

-- 4. Mettre à jour le solde des fonds en déduisant les prêts approuvés
-- Calculer le montant total des prêts approuvés
SET @total_prets_approuves = (
    SELECT SUM(montant) 
    FROM pret 
    WHERE statut = 'approuve'
);

-- Ajouter une entrée pour ajuster le solde
INSERT INTO fonds (montant, date_ajout, description, solde_total) 
VALUES (
    -@total_prets_approuves, 
    '2025-01-20', 
    'Déduction totale des prêts approuvés', 
    650000.00 - @total_prets_approuves
);

-- 5. Afficher un résumé des données créées
SELECT 
    'Résumé des données de test' as Information,
    COUNT(*) as Nombre_Prets_Approuves,
    SUM(montant) as Montant_Total_Prets,
    MIN(date_debut) as Premier_Pret,
    MAX(date_debut) as Dernier_Pret
FROM pret 
WHERE statut = 'approuve';

-- Afficher la répartition par type de prêt
SELECT 
    tp.nom as Type_Pret,
    COUNT(*) as Nombre_Prets,
    SUM(p.montant) as Montant_Total,
    AVG(p.montant) as Montant_Moyen,
    tp.taux_annuel as Taux_Annuel
FROM pret p
JOIN types_pret tp ON p.type_pret_id = tp.id
WHERE p.statut = 'approuve'
GROUP BY tp.id, tp.nom, tp.taux_annuel
ORDER BY Montant_Total DESC;

-- Afficher la répartition par mois de début
SELECT 
    YEAR(date_debut) as Annee,
    MONTH(date_debut) as Mois,
    MONTHNAME(date_debut) as Nom_Mois,
    COUNT(*) as Nombre_Prets,
    SUM(montant) as Montant_Total
FROM pret 
WHERE statut = 'approuve'
GROUP BY YEAR(date_debut), MONTH(date_debut)
ORDER BY Annee, Mois;

-- 6. Requête pour tester manuellement le calcul d'intérêts pour un mois donné
-- Exemple pour janvier 2024
SELECT 
    '=== EXEMPLE DE CALCUL POUR JANVIER 2024 ===' as Information;

SELECT 
    p.id,
    c.nom as Client,
    tp.nom as Type_Pret,
    p.montant as Montant_Pret,
    p.date_debut as Date_Debut,
    p.duree as Duree_Mois,
    DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) as Date_Fin_Prevue,
    tp.taux_annuel as Taux_Annuel,
    ROUND(p.montant * tp.taux_annuel / 12 / 100, 2) as Interet_Mensuel_Theorique
FROM pret p
JOIN client c ON p.client_id = c.id
JOIN types_pret tp ON p.type_pret_id = tp.id
WHERE p.statut = 'approuve'
AND p.date_debut <= '2024-01-31'
AND DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) >= '2024-01-01'
ORDER BY p.date_debut;

-- Calcul total pour janvier 2024
SELECT 
    'Total intérêts théoriques pour janvier 2024' as Information,
    SUM(ROUND(p.montant * tp.taux_annuel / 12 / 100, 2)) as Total_Interets_Janvier_2024
FROM pret p
JOIN types_pret tp ON p.type_pret_id = tp.id
WHERE p.statut = 'approuve'
AND p.date_debut <= '2024-01-31'
AND DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) >= '2024-01-01';