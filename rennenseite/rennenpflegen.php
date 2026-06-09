 <!-- Felix Weber
 Datei:  rennenseite/rennenpflegen.php
 Zweck:  Neues Rennen anlegen mit Validierung und Datenbankzugriff.
 RennID wird über generiereZufallsID() vergeben.-->

<?php

// Hilfsfunktion zur Generierung einer eindeutigen Zufalls-RennID einbinden
include_once 'funktionen/zufallrennid.php';

// Formular wurde abgeschickt → Rennen speichern
if (isset($_POST['Rennen_erstellen'])) {

    // Eingaben aus dem Formular auslesen und Leerzeichen entfernen
    $datum    = trim($_POST['datum']);
    $ort      = trim($_POST['startort']);
    $km       = trim($_POST['km']);
    $hoehe    = trim($_POST['hoehenmeter']);
    $steigung = trim($_POST['steigung']);
    $rvname   = $_SESSION['login_rv']; // Eingeloggter Rennveranstalter aus der Session

    $heute = date("Y-m-d"); // Heutiges Datum für Vergleich

    // Validierung: Pflichtfelder prüfen
    if ($datum == "" || $ort == "" || $km == "" || $hoehe == "" || $steigung == "") {
        echo "Bitte alle Pflichtfelder ausfüllen!";

    // Validierung: Datum darf nicht in der Vergangenheit liegen
    } elseif ($datum < $heute) {
        echo "Datum darf nicht in der Vergangenheit liegen!";

    // Validierung: Kilometer müssen positiv sein
    } elseif ($km <= 0) {
        echo "Die Kilometeranzahl muss größer als 0 sein!";

    // Validierung: Höhenmeter dürfen nicht negativ sein
    } elseif ($hoehe < 0) {
        echo "Die Höhenmeter dürfen nicht negativ sein!";

    // Validierung: Steigung muss zwischen 0 und 100% liegen
    } elseif ($steigung < 0 || $steigung > 100) {
        echo "Die Steigung muss zwischen 0 und 100 Prozent liegen!";

    // Validierung: Höhenmeter dürfen nicht größer als die Gesamtstrecke in Metern sein
    } elseif ($hoehe > ($km * 1000)) {
        echo "Höhenmeter können nicht größer als die Gesamtstrecke in Metern sein!";

    } else {

        // Eindeutige RennID per Zufallsfunktion generieren
        $neue_id = generiereZufallsID($connection);

        // SQL-Statement vorbereiten (Prepared Statement gegen SQL-Injection)
        $sql = "
            INSERT INTO Rennen 
                (RennID, Datum, StartOrt, AnzahlKm, HoehenMeter, MaxSteigung, RVName)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare($connection, $sql);

        if ($stmt) {
            // Parameter binden: i=Integer, s=String, d=Double
            mysqli_stmt_bind_param(
                $stmt,
                "issdiis",
                $neue_id,
                $datum,
                $ort,
                $km,
                $hoehe,
                $steigung,
                $rvname
            );

            // Statement ausführen und Rückmeldung ausgeben
            if (mysqli_stmt_execute($stmt)) {
                echo "Rennen erfolgreich gespeichert! Vergebene Renn-ID: " . h($neue_id);
            } else {
                echo "Fehler beim Speichern des Rennens!";
            }
        } else {
            echo "Fehler bei der Vorbereitung der Datenbankabfrage!";
        }
    }
}
?>

<hr>

<!-- Formular zur Erfassung eines neuen Rennens -->
<h4>Neues Rennen anlegen</h4>

<form method="POST">
    <label>Datum des Rennens:</label><br>
    <input type="date" name="datum" required><br>

    <label>Startort:</label><br>
    <input type="text" name="startort" required><br>

    <label>Anzahl Kilometer:</label><br>
    <input type="number" name="km" step="0.01" required><br>

    <label>Höhenmeter:</label><br>
    <input type="number" name="hoehenmeter" required><br>

    <label>Maximale Steigung in Prozent:</label><br>
    <input type="number" name="steigung" step="0.1" required><br><br>

    <input type="submit" name="Rennen_erstellen" value="Rennen speichern">
</form>

<hr>