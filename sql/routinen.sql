-- GRUPPE 12 - Stored Procedures und Trigger

-- Stored Procedures --

/* 
    Deniz Persbach
    Stored Procedure: Team registrieren
    Zweck:
    - Prüft, ob LoginName bereits existiert
    - Prüft, ob TeamName bereits existiert
    - Legt TeamChef und Team gemeinsam in einer Transaktion an
 */

DELIMITER //
DROP PROCEDURE IF EXISTS sp_team_registrieren;//

CREATE PROCEDURE sp_team_registrieren(
    IN p_loginname VARCHAR(50),
    IN p_vorname VARCHAR(50),
    IN p_nachname VARCHAR(50),
    IN p_kennwort VARCHAR(50),
    IN p_teamname VARCHAR(50)
)
BEGIN
    DECLARE v_login_vorhanden INT DEFAULT 0;
    DECLARE v_team_vorhanden INT DEFAULT 0;

    -- Bei einem Fehler wird alles zurückgesetzt
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    -- Prüfen, ob LoginName oder TeamName schon vergeben sind
    SELECT COUNT(*)
    INTO v_login_vorhanden
    FROM TeamChef
    WHERE LoginName = p_loginname;

    SELECT COUNT(*)
    INTO v_team_vorhanden
    FROM Team
    WHERE TeamName = p_teamname;

    IF v_login_vorhanden > 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'LoginName existiert bereits.';

    ELSEIF v_team_vorhanden > 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'TeamName existiert bereits.';

    ELSE

        -- TeamChef und Team werden zusammen gespeichert
        START TRANSACTION;

        INSERT INTO TeamChef (
            LoginName,
            Vorname,
            Nachname,
            Kennwort
        )
        VALUES (
            p_loginname,
            p_vorname,
            p_nachname,
            p_kennwort
        );

        INSERT INTO Team (
            TeamName,
            TCLoginName
        )
        VALUES (
            p_teamname,
            p_loginname
        );

        COMMIT;

    END IF;
END//

DELIMITER ;


/* 
    Deniz Persbach
    Stored Procedure: Fahrer speichern oder ändern
    Zweck:
    - Wenn Fahrer noch nicht existiert: INSERT
    - Wenn Fahrer existiert: UPDATE
    - Die MitarbeiterID wird beim UPDATE nicht geändert
 */

DELIMITER //

DROP PROCEDURE IF EXISTS sp_fahrer_speichern;//

CREATE PROCEDURE sp_fahrer_speichern(
    IN p_mitarbeiter_id VARCHAR(10),
    IN p_tc_loginname VARCHAR(50),
    IN p_vorname VARCHAR(50),
    IN p_nachname VARCHAR(50),
    IN p_strasse VARCHAR(100),
    IN p_hausnummer VARCHAR(10),
    IN p_plz VARCHAR(10),
    IN p_ort VARCHAR(50),
    IN p_telnr VARCHAR(20)
)
BEGIN
    DECLARE v_fahrer_vorhanden INT DEFAULT 0;

    -- Existenz prüfen
    SELECT COUNT(*)
    INTO v_fahrer_vorhanden
    FROM Fahrer
    WHERE MitarbeiterID = p_mitarbeiter_id
      AND TCLoginName = p_tc_loginname;

    IF v_fahrer_vorhanden = 0 THEN

        -- Neuer Fahrer einfügen
        INSERT INTO Fahrer (
            MitarbeiterID,
            TCLoginName,
            Vorname,
            Nachname,
            Strasse,
            Hausnummer,
            PLZ,
            Ort,
            TelNr
        )
        VALUES (
            p_mitarbeiter_id,
            p_tc_loginname,
            p_vorname,
            p_nachname,
            p_strasse,
            p_hausnummer,
            p_plz,
            p_ort,
            p_telnr
        );

    ELSE

        -- Bestehenden Fahrer aktualisieren
        UPDATE Fahrer
        SET Vorname = p_vorname,
            Nachname = p_nachname,
            Strasse = p_strasse,
            Hausnummer = p_hausnummer,
            PLZ = p_plz,
            Ort = p_ort,
            TelNr = p_telnr
        WHERE MitarbeiterID = p_mitarbeiter_id
          AND TCLoginName = p_tc_loginname;

    END IF;
END//

DELIMITER ;

/* 
    Felix
    Stored Procedure: Rennveranstalter registrieren
    Zweck:
    - Prüft, ob RVName bereits existiert
    - Legt neuen Rennveranstalter an, wenn der Name frei ist
 */

DELIMITER //

DROP PROCEDURE IF EXISTS sp_rv_registrieren;//

CREATE PROCEDURE sp_rv_registrieren(
    IN p_rvname VARCHAR(50),
    IN p_kennwort VARCHAR(50)
)
BEGIN
    DECLARE v_rv_vorhanden INT DEFAULT 0;

    -- Handler bei SQL-Fehlern: Rollback und Fehler weiterreichen
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    -- Prüfen, ob RVName schon vergeben ist
    SELECT COUNT(*)
    INTO v_rv_vorhanden
    FROM Rennveranstalter
    WHERE RVName = p_rvname;

    IF v_rv_vorhanden > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'RVName existiert bereits.';
    ELSE
        INSERT INTO Rennveranstalter (RVName, Kennwort)
        VALUES (p_rvname, p_kennwort);
    END IF;
END//

DELIMITER ;


-- Triggers --

/* 
    Luccas
    Trigger: Startnummer automatisch vergeben
    Zweck:
    - Wird vor jedem INSERT in Teilnahme ausgeführt
    - Wenn Startnummer NULL oder 0 ist, wird automatisch die nächste Startnummer vergeben
    - Die Startnummer beginnt pro Rennen bei 1
*/

DELIMITER //

DROP TRIGGER IF EXISTS trg_startnummer_vergeben;//

CREATE TRIGGER trg_startnummer_vergeben
BEFORE INSERT ON Teilnahme
FOR EACH ROW
BEGIN
    DECLARE v_naechste_startnummer INT DEFAULT 1;

    IF NEW.Startnummer IS NULL OR NEW.Startnummer = 0 THEN

        -- Nächste freie Startnummer für dieses Rennen ermitteln
        SELECT COALESCE(MAX(Startnummer), 0) + 1
        INTO v_naechste_startnummer
        FROM Teilnahme
        WHERE RennID = NEW.RennID;

                -- Startnummer im neuen Teilnahme-Datensatz setzen
        SET NEW.Startnummer = v_naechste_startnummer;

    END IF;
END//

DELIMITER ;

/* 
   Luccas Dias
   Trigger: Training prüfen
   Zweck:
   - Prüft Trainingsdaten vor dem Einfügen
   - Verhindert ungültige Kilometer und zukünftige Trainingsdaten
   - Unterstützt die Datenqualität für die spätere Auswertung
*/

DELIMITER //

DROP TRIGGER IF EXISTS trg_training_pruefen;//

CREATE TRIGGER trg_training_pruefen
BEFORE INSERT ON Training
FOR EACH ROW
BEGIN
    DECLARE v_fahrer_vorhanden INT DEFAULT 0;
    DECLARE v_ziel_vorhanden INT DEFAULT 0;

    -- Prüfen, ob der Fahrer existiert
    SELECT COUNT(*)
    INTO v_fahrer_vorhanden
    FROM Fahrer
    WHERE MitarbeiterID = NEW.MitarbeiterID
      AND TCLoginName = NEW.TCLoginName;

    IF v_fahrer_vorhanden = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Der Fahrer existiert nicht.';
    END IF;

    -- Kilometer müssen positiv sein
    IF NEW.Km <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Die Kilometeranzahl muss groesser als 0 sein.';
    END IF;

    -- Trainings dürfen nicht in der Zukunft liegen
    IF NEW.Datum > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trainings duerfen nicht in der Zukunft liegen.';
    END IF;

    IF NEW.Ziel IS NOT NULL THEN

        SELECT COUNT(*)
        INTO v_ziel_vorhanden
        FROM Trainingsziel
        WHERE Ziel = NEW.Ziel;

        IF v_ziel_vorhanden = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Das Trainingsziel existiert nicht.';
        END IF;
    END IF;
END//

DELIMITER ;