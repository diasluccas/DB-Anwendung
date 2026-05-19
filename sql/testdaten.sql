-- GRUPPE 12 - Testdaten

DELETE FROM Training;
DELETE FROM Teilnahme;
DELETE FROM Fahrer;
DELETE FROM Rennen;
DELETE FROM Team;
DELETE FROM Trainingsziel;
DELETE FROM Rennveranstalter;
DELETE FROM TeamChef;

INSERT INTO Trainingsziel (Ziel) VALUES
('Ausdauer'),
('Sprintkraft'),
('Steigungen');

CALL sp_team_registrieren(
    'luccas',
    'Luccas',
    'Dias',
    '123',
    'Rennteam Bodensee'
);

CALL sp_team_registrieren(
    'deniz',
    'Deniz',
    'Yilmaz',
    '123',
    'Speed Riders'
);

CALL sp_team_registrieren(
    'felix',
    'Felix',
    'Schneider',
    '123',
    'Mountain Kings'
);

INSERT INTO Rennveranstalter (RVName, Kennwort) VALUES
('rv_sued', '123'),
('rv_nord', '123');

CALL sp_fahrer_speichern(
    'F001',
    'luccas',
    'Max',
    'Müller',
    'Hauptstraße',
    '10',
    '88250',
    'Weingarten',
    '075112345'
);

CALL sp_fahrer_speichern(
    'F002',
    'luccas',
    'Anna',
    'Schmidt',
    'Bahnhofstraße',
    '5',
    '88214',
    'Ravensburg',
    '075198765'
);

CALL sp_fahrer_speichern(
    'F003',
    'luccas',
    'Jonas',
    'Weber',
    'Seestraße',
    '22',
    '88045',
    'Friedrichshafen',
    '075411111'
);

CALL sp_fahrer_speichern(
    'D001',
    'deniz',
    'Lena',
    'Fischer',
    'Bergweg',
    '3',
    '88250',
    'Weingarten',
    '075122222'
);

CALL sp_fahrer_speichern(
    'D002',
    'deniz',
    'Tim',
    'Hoffmann',
    'Marktplatz',
    '7',
    '88212',
    'Ravensburg',
    '075133333'
);

CALL sp_fahrer_speichern(
    'X001',
    'felix',
    'Paul',
    'Keller',
    'Waldstraße',
    '9',
    '88069',
    'Tettnang',
    '075144444'
);

CALL sp_fahrer_speichern(
    'X002',
    'felix',
    'Mia',
    'Becker',
    'Kirchweg',
    '12',
    '88239',
    'Wangen',
    '075155555'
);

INSERT INTO Rennen (
    RennID,
    Datum,
    StartOrt,
    AnzahlKm,
    HoehenMeter,
    MaxSteigung,
    RVName
)
VALUES
(7001, '2026-05-10', 'Ravensburg', 80.00, 900, 12.50, 'rv_sued'),
(9127, '2026-05-22', 'Frankfurt', 120.00, 1400, 15.00, 'rv_sued'),
(8450, '2026-06-05', 'Stuttgart', 95.50, 1100, 10.00, 'rv_nord'),
(6188, '2026-07-12', 'München', 150.00, 2100, 18.00, 'rv_nord');

INSERT INTO Teilnahme (
    MitarbeiterID,
    TCLoginName,
    RennID,
    Startnummer
)
VALUES
('F001', 'luccas', 7001, 0),
('F002', 'luccas', 7001, 0),
('D001', 'deniz', 7001, 0),
('X001', 'felix', 7001, 0);

INSERT INTO Teilnahme (
    MitarbeiterID,
    TCLoginName,
    RennID,
    Startnummer
)
VALUES
('F001', 'luccas', 9127, 0),
('F002', 'luccas', 9127, 0),
('F003', 'luccas', 9127, 0),
('D001', 'deniz', 9127, 0),
('D002', 'deniz', 9127, 0);

INSERT INTO Teilnahme (
    MitarbeiterID,
    TCLoginName,
    RennID,
    Startnummer
)
VALUES
('X001', 'felix', 8450, 0),
('X002', 'felix', 8450, 0),
('F001', 'luccas', 8450, 0);

UPDATE Teilnahme
SET Platzierung = 1,
    Fahrzeit = '02:10:30'
WHERE RennID = 7001
  AND TCLoginName = 'luccas'
  AND MitarbeiterID = 'F001';

UPDATE Teilnahme
SET Platzierung = 2,
    Fahrzeit = '02:15:45'
WHERE RennID = 7001
  AND TCLoginName = 'deniz'
  AND MitarbeiterID = 'D001';

UPDATE Teilnahme
SET Platzierung = 3,
    Fahrzeit = '02:18:10'
WHERE RennID = 7001
  AND TCLoginName = 'felix'
  AND MitarbeiterID = 'X001';

UPDATE Teilnahme
SET Platzierung = 4,
    Fahrzeit = '02:25:00'
WHERE RennID = 7001
  AND TCLoginName = 'luccas'
  AND MitarbeiterID = 'F002';

INSERT INTO Training (
    Datum,
    MitarbeiterID,
    TCLoginName,
    Km,
    Ziel
)
VALUES
('2026-03-03', 'F001', 'luccas', 45.50, 'Ausdauer'),
('2026-03-10', 'F001', 'luccas', 52.00, 'Ausdauer'),
('2026-03-20', 'F001', 'luccas', 30.00, 'Sprintkraft'),
('2026-04-05', 'F001', 'luccas', 60.00, 'Steigungen'),
('2026-04-12', 'F001', 'luccas', 75.50, 'Ausdauer'),
('2026-05-02', 'F001', 'luccas', 80.00, 'Ausdauer'),

('2026-03-04', 'F002', 'luccas', 35.00, 'Ausdauer'),
('2026-03-14', 'F002', 'luccas', 42.00, 'Sprintkraft'),
('2026-04-03', 'F002', 'luccas', 50.00, 'Steigungen'),
('2026-04-17', 'F002', 'luccas', 55.50, 'Ausdauer'),
('2026-05-04', 'F002', 'luccas', 65.00, 'Ausdauer'),

('2026-03-06', 'F003', 'luccas', 25.00, 'Sprintkraft'),
('2026-04-08', 'F003', 'luccas', 48.00, 'Ausdauer'),
('2026-05-09', 'F003', 'luccas', 70.00, 'Steigungen'),

('2026-03-05', 'D001', 'deniz', 40.00, 'Ausdauer'),
('2026-03-18', 'D001', 'deniz', 44.00, 'Sprintkraft'),
('2026-04-10', 'D001', 'deniz', 58.00, 'Steigungen'),
('2026-05-03', 'D001', 'deniz', 62.50, 'Ausdauer'),

('2026-03-07', 'D002', 'deniz', 38.00, 'Ausdauer'),
('2026-04-11', 'D002', 'deniz', 53.00, 'Steigungen'),
('2026-05-06', 'D002', 'deniz', 67.00, 'Ausdauer'),

('2026-03-09', 'X001', 'felix', 46.00, 'Ausdauer'),
('2026-04-13', 'X001', 'felix', 61.00, 'Steigungen'),
('2026-05-08', 'X001', 'felix', 73.00, 'Ausdauer'),

('2026-03-12', 'X002', 'felix', 32.00, 'Sprintkraft'),
('2026-04-15', 'X002', 'felix', 57.00, 'Ausdauer'),
('2026-05-10', 'X002', 'felix', 69.00, 'Steigungen');