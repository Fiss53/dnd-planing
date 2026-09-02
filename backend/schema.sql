-- Schema de la base de donnees pour le backend Calendrier Lune Rousse.
-- A importer une fois via phpMyAdmin (interface OVH) ou la ligne de commande mysql.

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(8) NOT NULL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    info TEXT NULL,
    gameMaster VARCHAR(255) NULL,
    image VARCHAR(500) NULL,
    dates TEXT NULL,          -- tableau JSON de dates, ex: ["2026-05-20","2026-05-27"]
    day VARCHAR(20) NULL,     -- jour de recurrence (Lundi, Mardi, ...)
    frequency INT NULL,       -- recurrence en semaines (1 = chaque semaine, 2 = toutes les 2 semaines, ...)
    location VARCHAR(255) NULL,
    time VARCHAR(10) NULL,
    players INT NOT NULL DEFAULT 0,
    maxPlayers INT NOT NULL DEFAULT 0,
    tags TEXT NULL,           -- tableau JSON de tags, ex: ["DnD","Vampire"]
    special TINYINT(1) NOT NULL DEFAULT 0,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
