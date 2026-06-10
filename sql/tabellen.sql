-- GRUPPE 12 - Grundstruktur / gemeinsame Datei


-- Tabellen --

CREATE TABLE TeamChef (
    LoginName VARCHAR(50) PRIMARY KEY,
    Vorname VARCHAR(50),
    Nachname VARCHAR(50),
    Kennwort VARCHAR(255) NOT NULL
);


CREATE TABLE Rennveranstalter (
    RVName VARCHAR(50) PRIMARY KEY,
    Kennwort VARCHAR(255) NOT NULL
);


CREATE TABLE Trainingsziel (
    Ziel VARCHAR(50) PRIMARY KEY
);


CREATE TABLE Team (
    TeamName VARCHAR(50) PRIMARY KEY,
    TCLoginName VARCHAR(50),

    UNIQUE (TCLoginName),

    CONSTRAINT fk_team_tc
    FOREIGN KEY (TCLoginName)
        REFERENCES TeamChef(LoginName)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE Rennen (
    RennID INT PRIMARY KEY,
    Datum DATE,
    StartOrt VARCHAR(50),
    AnzahlKm DECIMAL(5,2),
    HoehenMeter INT,
    MaxSteigung DECIMAL(5,2),
    RVName VARCHAR(50),

    CONSTRAINT fk_rennen_rv
    FOREIGN KEY (RVName)
        REFERENCES Rennveranstalter(RVName)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE Fahrer (
    MitarbeiterID VARCHAR(10),
    TCLoginName VARCHAR(50),
    Vorname VARCHAR(50),
    Nachname VARCHAR(50),
    Strasse VARCHAR(100),
    Hausnummer VARCHAR(10),
    PLZ VARCHAR(10),
    Ort VARCHAR(50),
    TelNr VARCHAR(20),

    PRIMARY KEY (MitarbeiterID, TCLoginName),

    CONSTRAINT fk_fahrer_tc
    FOREIGN KEY (TCLoginName)
        REFERENCES TeamChef(LoginName)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE Teilnahme (
    MitarbeiterID VARCHAR(10),
    TCLoginName VARCHAR(50),
    RennID INT,
    Startnummer INT,
    Platzierung INT,
    Fahrzeit TIME,
    TeamPraemie DECIMAL(10,2),
    RVPraemie DECIMAL(10,2),

    PRIMARY KEY (MitarbeiterID, TCLoginName, RennID),

    UNIQUE (RennID, Startnummer),

    CONSTRAINT fk_teilnahme_fahrer
    FOREIGN KEY (MitarbeiterID, TCLoginName)
        REFERENCES Fahrer(MitarbeiterID, TCLoginName)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_teilnahme_rennen
    FOREIGN KEY (RennID)
        REFERENCES Rennen(RennID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE Training (
    Datum DATE,
    MitarbeiterID VARCHAR(10),
    TCLoginName VARCHAR(50),
    Km DECIMAL(5,2),
    Ziel VARCHAR(50),

    PRIMARY KEY (Datum, MitarbeiterID, TCLoginName),

    CONSTRAINT fk_training_fahrer
    FOREIGN KEY (MitarbeiterID, TCLoginName)
        REFERENCES Fahrer(MitarbeiterID, TCLoginName)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_training_ziel
    FOREIGN KEY (Ziel)
        REFERENCES Trainingsziel(Ziel)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);


-- Indizes --

CREATE INDEX idx_fahrer_tc_name
ON Fahrer (TCLoginName, Nachname, Vorname);

CREATE INDEX idx_training_fahrer_zeitraum_ziel
ON Training (TCLoginName, MitarbeiterID, Datum, Ziel);

CREATE INDEX idx_rennen_datum
ON Rennen (Datum, StartOrt);

CREATE INDEX idx_rennen_rv_datum
ON Rennen (RVName, Datum);

CREATE INDEX idx_teilnahme_tc_rennen
ON Teilnahme (TCLoginName, RennID);