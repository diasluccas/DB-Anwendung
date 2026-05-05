<?php
include 'db.php';
?>

<h4>Neues Rennen anlegen</h4>
<form method="POST" action="">
    <label>Datum des Rennens:</label><br>
    <input type="date" name="datum" required><br>

    <label>Startort:</label><br>
    <input type="text" name="startort" required><br>

    <label>Anzahl Kilometer:</label><br>
    <input type="number" name="km" required><br>

    <label>Höhenmeter:</label><br>
    <input type="number" name="hoehenmeter" required><br>

    <label>Maximale Steigung in Prozent:</label><br>
    <input type="number" name="steigung" step="0.1" required><br><br>

    <input type="submit" name="Rennen_erstellen" value="Rennen speichern">
</form>
<hr />


<?php
if (isset($_POST['Rennen_erstellen'])) {
    // Daten aus dem Formular holen
    $datum = $_POST['datum'];
    $ort = $_POST['startort'];
    $km = $_POST['km'];
    $hoehe = $_POST['hoehenmeter'];
    $steigung = $_POST['steigung'];
    $rvname = $_SESSION['login_rv']; // Name aus der Session

    // Test: Liegt das Datum in der Vergangenheit?
    $heute = date("Y-m-d");

    if ($datum < $heute) {
        echo "Fehler: Das Datum darf nicht in der Vergangenheit liegen!";
    } else {
        // SQL-Befehl mit Spaltennamen aus Screenshot: Datum, StartOrt, AnzahlKm, HoehenMeter, MaxSteigung, RVName
        $sql = "INSERT INTO Rennen (Datum, StartOrt, AnzahlKm, HoehenMeter, MaxSteigung, RVName) 
                VALUES ('$datum', '$ort', '$km', '$hoehe', '$steigung', '$rvname')";

        if (mysqli_query($connection, $sql)) {
            echo "Rennen erfolgreich gespeichert!";
        } else {
            echo "Fehler: " . mysqli_error($connection);
        }
    }
}
?>

