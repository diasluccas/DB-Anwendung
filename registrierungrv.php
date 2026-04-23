<!-- LUCCAS DIAS - 2024-06-17 -->

<?php
include 'db.php';
?>
        <h4>Noch kein Konto? Registriere dich hier unten.</h4>
            <form method="POST" action="">
                <label>Rennveranstalter Name:</label><br>
                <input type="text" name="rv_name" required><br>

                <label>Passwort:</label><br>
                <input type="password" name="rv_kennwort" required><br><br>

                <input type="submit" name="registrierung_rv" value="Erstellen">
            </form>

<hr />

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