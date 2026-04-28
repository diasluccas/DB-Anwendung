<h4>Meine Rennen pflegen</h4>
<?php
include 'db.php';

if (isset($_POST['rennen_speichern'])) {
    $datum = $_POST['datum'];
    $startort = $_POST['startort'];
    $km = $_POST['km'];
    $hoehenmeter = $_POST['hoehenmeter'];
    $steigung = $_POST['steigung'];

    $sql = "INSERT INTO Rennen (Datum, Startort, Kilometer, Hoehenmeter, MaxSteigung) 
            VALUES ('$datum', '$startort', '$km', '$hoehenmeter', '$steigung')";

    if (mysqli_query($connection, $sql)) {
        echo "Erfolg!";
    } else {
        echo "Fehler: " . mysqli_error($connection);
    }
}
?>

