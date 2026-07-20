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
    (2, 5, NULL, 1, '2026-07-13 13:10:00', 7000, 100),

    -- Retrait de Pierre (1500 → frais 100 selon tarif 5)
    (3, 3, NULL, 2, '2026-07-20 11:00:00', 1500, 100),

    -- Retrait de Sophie (300 → frais 50 selon tarif 4)
    (4, 4, NULL, 2, '2026-07-12 09:00:00', 300, 50),

    -- Transfert de Jean à Marie (500 → frais 100 selon tarif 8)
    (5, 1, 2, 3, '2026-07-20 10:00:00', 500, 100),

    -- Transfert de Luc à Jean (6000 → frais 300 selon tarif 10)
    (6, 5, 1, 3, '2026-07-17 16:00:00', 6000, 300),

    -- Transfert de Marie à Sophie (2500 → frais 200 selon tarif 9)
    (7, 2, 4, 3, '2026-07-14 17:20:00', 2500, 200);

-- Fin du script