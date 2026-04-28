<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
include 'db.php';
include 'funktionen/exists.php';
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