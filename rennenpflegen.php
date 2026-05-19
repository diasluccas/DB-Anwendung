<?php
include 'db.php';
include 'funktionen/zufallrennid.php';

if (isset($_POST['Rennen_erstellen'])) {
    $datum = $_POST['datum'];
    $ort = $_POST['startort'];
    $km = $_POST['km'];
    $hoehe = $_POST['hoehenmeter'];
    $steigung = $_POST['steigung'];
    $rvname = $_SESSION['login_rv'];

    $heute = date("Y-m-d");

    if ($datum < $heute) {
        echo "Datum darf nicht in der Vergangenheit liegen";
    } elseif ($steigung > 100) {
        echo "Steigung kann nicht über 100% liegen";
    } elseif ($hoehe > ($km * 1000)) {
        echo "Höhenmeter können nicht größer als die Gesamtstrecke sein";
    } else {
        $neue_id = generiereZufallsID($connection);

        $sql = "INSERT INTO Rennen (RennID, Datum, StartOrt, AnzahlKm, HoehenMeter, MaxSteigung, RVName) 
                VALUES ('$neue_id', '$datum', '$ort', '$km', '$hoehe', '$steigung', '$rvname')";

        if (mysqli_query($connection, $sql)) {
            echo "Rennen erfolgreich gespeichert! Vergebene Zufalls-ID: $neue_id";
        } else {
            echo mysqli_error($connection);
        }
    }
}
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