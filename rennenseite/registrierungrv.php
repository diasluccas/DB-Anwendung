<!-- Felix Weber -->

<?php
/**
 * Autor:  Felix Weber
 * Datei:  rennenseite/registrierungrv.php
 * Zweck:  Registrierung eines neuen Rennveranstalters.
 *         Prüfung ob RVName bereits existiert erfolgt über die Stored Procedure sp_rv_registrieren.
 *         Bei Fehler wird die Meldung direkt aus der SP ausgelesen und angezeigt.
 */

function neuerRennveranstalterEintragen($connection, $rvname, $kennwort) {

    // SP übernimmt sowohl Prüfung als auch INSERT
    $sql = "CALL sp_rv_registrieren(?, ?)";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return ["erfolg" => false, "meldung" => "Fehler bei der Vorbereitung!"];
    }

    mysqli_stmt_bind_param($stmt, "ss", $rvname, $kennwort);

    $erfolg = mysqli_stmt_execute($stmt);

    if ($erfolg) {
        return ["erfolg" => true, "meldung" => "Registrierung erfolgreich!"];
    } else {
        // Fehlermeldung direkt aus der SP auslesen
        return ["erfolg" => false, "meldung" => mysqli_stmt_error($stmt)];
    }
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
    } else {
        // Funktion aufrufen und Rückmeldung ausgeben
        $result = neuerRennveranstalterEintragen($connection, $rvname, $kennwort);
        echo $result['meldung'];
    }
}
?>