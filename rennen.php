<?php
include 'db.php';
?>

<h3>Rennen</h3>

<h3>Registrierung neuer Rennveranstalter</h3>

<form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="rv_name" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="rv_kennwort" required><br><br>

    <input type="submit" name="registrierung_rv" value="Erstellen">
</form>

<?php
function exists($connection, $table, $column, $value) {
    $sql = "SELECT * FROM $table WHERE $column = '$value' LIMIT 1";
    $result = mysqli_query($connection, $sql);

    return mysqli_num_rows($result) > 0;
}
?>

<?php
if (isset($_POST['registrierung_rv'])) {

    $rvname = $_POST['rv_name'];
    $kennwort = $_POST['rv_kennwort'];

    if (exists($connection, "Rennveranstalter", "RVName", $rvname)) {
        echo "RVName existiert schon!";
        exit;
    }
try {
    $sql = "INSERT INTO Rennveranstalter (RVName, Kennwort)
            VALUES ('$rvname', '$kennwort')";

    mysqli_query($connection, $sql);

    echo "Erfolgreich erstellt!";
} catch (Exception $e) {
    echo "Fehler!";
}
}
?>

<?php
$result = mysqli_query($connection,"SELECT * FROM Rennveranstalter");

while ($row = mysqli_fetch_assoc($result)) {
    print ($row['RVName']);
    print ($row['Kennwort']);
}

mysqli_close($connection);

?>
