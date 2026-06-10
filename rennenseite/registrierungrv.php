<!-- Felix Weber 
Zweck:  Registrierung eines neuen Rennveranstalters.
        Prüfung ob RVName bereits existiert erfolgt über die Stored Procedure sp_rv_registrieren.
        Bei Fehler wird die Meldung direkt aus der SP ausgelesen und angezeigt. -->

<?php

function neuerRennveranstalterEintragen($connection, $rvname, $kennwortHash) {

    // SP übernimmt sowohl Prüfung als auch INSERT
    // Das Kennwort wird bereits vorher mit password_hash() gehasht
    $sql = "CALL sp_rv_registrieren(?, ?)";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return [
            "erfolg" => false,
            "meldung" => "Fehler bei der Vorbereitung der Registrierung."
        ];
    }

    mysqli_stmt_bind_param($stmt, "ss", $rvname, $kennwortHash);

    try {
        mysqli_stmt_execute($stmt);

        return [
            "erfolg" => true,
            "meldung" => "Rennveranstalter erfolgreich erstellt!"
        ];

    } catch (mysqli_sql_exception $e) {

        return [
            "erfolg" => false,
            "meldung" => $e->getMessage()
        ];
    }

}
?>

<h4>Noch kein Konto? Registriere dich hier unten.</h4>

<form method="POST">
    <label>Rennveranstalter Name:</label><br>
    <input type="text" name="rv_name" maxlength="50" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="rv_kennwort" maxlength="100" required><br><br>

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

        // Passwort wird nicht im Klartext gespeichert, sondern als Hashwert
        $kennwortHash = password_hash($kennwort, PASSWORD_DEFAULT);

        // Funktion aufrufen und Rückmeldung ausgeben
        $result = neuerRennveranstalterEintragen($connection, $rvname, $kennwortHash);
        echo h($result['meldung']);
    }
}
?>