-- ============================================================
-- Base de données des transactions (DEPOT, RETRAIT, TRANSFERT)
-- ============================================================

-- Suppression des tables si elles existent (pour réinitialisation)
DROP TABLE IF EXISTS t_transaction;
DROP TABLE IF EXISTS t_client;
DROP TABLE IF EXISTS t_historique_tarif;
DROP TABLE IF EXISTS t_tarif_operation;
DROP TABLE IF EXISTS t_type_operation;
DROP TABLE IF EXISTS t_prefix;
DROP TABLE IF EXISTS t_operateur;

-- ============================================================
-- Création des tables
-- ============================================================

CREATE TABLE t_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL
);

CREATE TABLE t_prefix (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INTEGER NOT NULL,
    libelle TEXT NOT NULL,   -- ex: '033', '+26133' (sans espaces)
    FOREIGN KEY (id_operateur) REFERENCES t_operateur(id)
);

CREATE TABLE t_type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE   -- en majuscules : DEPOT, RETRAIT, TRANSFERT
);

CREATE TABLE t_tarif_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INTEGER NOT NULL,
    id_type_operation INTEGER NOT NULL,
    min REAL NOT NULL,
    max REAL NOT NULL,
    prix REAL NOT NULL,
    FOREIGN KEY (id_operateur) REFERENCES t_operateur(id),
    FOREIGN KEY (id_type_operation) REFERENCES t_type_operation(id)
);

CREATE TABLE t_historique_tarif (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date_changement DATETIME DEFAULT NULL,   -- NULL = tarif actuel
    id_tarif_operation INTEGER NOT NULL,
    prix REAL NOT NULL,
    FOREIGN KEY (id_tarif_operation) REFERENCES t_tarif_operation(id)
);

CREATE TABLE t_client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT ,
    prenom TEXT ,
    id_operateur INTEGER ,
    numero TEXT NOT NULL,   -- sans espaces
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_operateur) REFERENCES t_operateur(id)
);

CREATE TABLE t_transaction (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_client_source INTEGER NOT NULL,
    id_client_cible INTEGER,   -- NULL pour DEPOT et RETRAIT (cible = opérateur ou inexistante)
    id_type_operation INTEGER NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (id_client_source) REFERENCES t_client(id),
    FOREIGN KEY (id_client_cible) REFERENCES t_client(id),
    FOREIGN KEY (id_type_operation) REFERENCES t_type_operation(id)
);

-- ============================================================
-- Insertion des données
-- ============================================================

-- 1. Opérateur (un seul : Orange)
INSERT INTO t_operateur (id, libelle) VALUES (1, 'Orange');

-- 2. Préfixes (sans espaces)
INSERT INTO t_prefix (id, id_operateur, libelle) VALUES
    (1, 1, '033'),
    (2, 1, '+26133');

-- 3. Types d'opération (uniquement les trois demandés)
INSERT INTO t_type_operation (id, code) VALUES
    (1, 'DEPOT'),
    (2, 'RETRAIT'),
    (3, 'TRANSFERT');

-- 4. Tarifs pour l'opérateur Orange (id=1)


--    RETRAIT (type 2)
INSERT INTO t_tarif_operation (id, id_operateur, id_type_operation, min, max, prix) VALUES
    (4, 1, 2, 0,     500,    50),
    (5, 1, 2, 501,   2000,   100),
    (6, 1, 2, 2001,  5000,   150),
    (7, 1, 2, 5001,  999999, 250);

--    TRANSFERT (type 3) – remplace l'ancien VIREMENT
INSERT INTO t_tarif_operation (id, id_operateur, id_type_operation, min, max, prix) VALUES
    (8,  1, 3, 0,     1000,   100),
    (9,  1, 3, 1001,  5000,   200),
    (10, 1, 3, 5001,  10000,  300),
    (11, 1, 3, 10001, 999999, 500);

-- 5. Historique des tarifs (tarifs courants → date_changement NULL)
INSERT INTO t_historique_tarif (id, date_changement, id_tarif_operation, prix)
SELECT
    id,
    NULL,
    id,
    prix
FROM t_tarif_operation;

-- (Optionnel) On peut ajouter un ancien tarif pour l'historique
-- INSERT INTO t_historique_tarif (date_changement, id_tarif_operation, prix)
-- VALUES ('2026-01-01 00:00:00', 1, 0);  -- ancien tarif du dépôt, par exemple

-- 6. Clients (numéros sans espaces)
INSERT INTO t_client (id, nom, prenom, id_operateur, numero, date_creation) VALUES
    (1, 'Dupont',   'Jean',     1, '0331234567',  '2026-01-15 08:30:00'),
    (2, 'Claire',   'Marie',    1, '+261331234568', '2026-02-20 14:15:00'),
    (3, 'Martin',   'Pierre',   1, '0339876543',  '2026-03-10 09:45:00'),
    (4, 'Durand',   'Sophie',   1, '+261339876543', '2026-04-05 11:20:00'),
    (5, 'Lefevre',  'Luc',      1, '0334567890',  '2026-05-12 16:00:00');

-- 7. Transactions
--    Les types : 1=DEPOT, 2=RETRAIT, 3=TRANSFERT
--    Pour DEPOT et RETRAIT, id_client_cible = NULL (car c'est avec l'opérateur)
--    Pour TRANSFERT, id_client_cible est renseigné
INSERT INTO t_transaction (id, id_client_source, id_client_cible, id_type_operation, date, montant, frais) VALUES
    -- Dépôt de Marie (2000 → frais 50 selon tarif 2)
    (1, 2, NULL, 1, '2026-07-19 09:00:00', 2000, 50),

    -- Dépôt de Luc (7000 → frais 100 selon tarif 3)
    (2, 5, NULL, 1, '2026-07-13 13:10:00', 7000, 100);

-- Fin du script
-- ============================================================
-- Table des commissions entre opérateurs
-- ============================================================

CREATE TABLE t_commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    id_operateur_envoi INTEGER NOT NULL,
    id_operateur_receveur INTEGER NOT NULL,

    pourcentage REAL NOT NULL,

    valable BOOLEAN DEFAULT 1,

    FOREIGN KEY (id_operateur_envoi)
        REFERENCES t_operateur(id),

    FOREIGN KEY (id_operateur_receveur)
        REFERENCES t_operateur(id),

    -- éviter une double commission entre deux opérateurs
    UNIQUE(id_operateur_envoi, id_operateur_receveur)
);



-- ============================================================
-- Historique des commissions
-- ============================================================

CREATE TABLE t_historique_commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,

    id_commission INTEGER NOT NULL,

    pourcentage REAL NOT NULL,

    FOREIGN KEY (id_commission)
        REFERENCES t_commission(id)
);



-- ============================================================
-- Ajout commission dans les transactions
-- ============================================================

ALTER TABLE t_transaction
ADD COLUMN commission REAL DEFAULT 0;



-- ============================================================
-- Exemple de commissions entre opérateurs
-- ============================================================

-- Orange -> Telma : 30%
-- Orange -> Airtel : 25%
-- Telma -> Orange : 30%
-- Telma -> Airtel : 20%
-- Airtel -> Orange : 25%
-- Airtel -> Telma : 20%

INSERT INTO t_commission
(id_operateur_envoi,id_operateur_receveur,pourcentage)
VALUES

(1,2,30),
(1,3,25),

(2,1,30),
(2,3,20),

(3,1,25),
(3,2,20);



-- ============================================================
-- Historique initial des commissions
-- ============================================================

INSERT INTO t_historique_commission
(id_commission,pourcentage)

SELECT 
id,
pourcentage

FROM t_commission;



-- ============================================================
-- Vue frais opérateur
-- ============================================================

CREATE VIEW v_frais_operateur AS

SELECT

    c.id_operateur_envoi,

    c.id_operateur_receveur,

    SUM(t.frais) AS frais,

    t.date,

    t.id_type_operation,

    top.code AS type_operation_libelle,

    SUM(
        t.frais * c.pourcentage / 100
    ) AS commission


FROM t_transaction t


JOIN t_client cl_source
ON cl_source.id = t.id_client_source


JOIN t_client cl_cible
ON cl_cible.id = t.id_client_cible


JOIN t_commission c
ON c.id_operateur_envoi = cl_source.id_operateur
AND c.id_operateur_receveur = cl_cible.id_operateur


JOIN t_type_operation top
ON top.id = t.id_type_operation


WHERE c.valable = 1


GROUP BY

    c.id_operateur_envoi,

    c.id_operateur_receveur,

    t.date,

    t.id_type_operation,

    top.code;