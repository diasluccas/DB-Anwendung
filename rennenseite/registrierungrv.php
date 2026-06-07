<!-- Deniz -->

<?php
include_once 'funktionen/exists.php';

function neuerRennveranstalterEintragen($connection, $rvname, $kennwort) {

    $sql = "
        INSERT INTO Rennveranstalter (RVName, Kennwort)
        VALUES (?, ?)
    ";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $rvname, $kennwort);

    return mysqli_stmt_execute($stmt);
}
?>

<h4>Noch kein Konto? Registriere dich hier unten.</h4>

<form method="POST">
    <label>Rennveranstalter Name:</label><br>
    <input type="text" name="rv_name" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="rv_kennwort" required><br><br>

    <input type="submit" name="registrierung_rv" value="Erstellen">
</form>

<hr>

<?php
if (isset($_POST['registrierung_rv'])) {

    $rvname = trim($_POST['rv_name']);
    $kennwort = trim($_POST['rv_kennwort']);

    if ($rvname == "" || $kennwort == "") {
        echo "Bitte alle Pflichtfelder ausfüllen!";
    } elseif (exists($connection, "Rennveranstalter", "RVName", $rvname)) {
        echo "RVName existiert schon!";
    } else {
        $erfolg = neuerRennveranstalterEintragen($connection, $rvname, $kennwort);

        if ($erfolg) {
            echo "Rennveranstalter erfolgreich erstellt!";
        } else {
            echo "Fehler bei der Registrierung!";
        }
    }
}
?>