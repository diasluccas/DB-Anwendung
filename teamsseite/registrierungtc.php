<!-- Deniz -->

<?php

function neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwortHash, $teamname) {

    // Stored Procedure übernimmt Prüfung und INSERT
    // Das Kennwort wird bereits vorher mit password_hash() gehasht
    $sql = "CALL sp_team_registrieren(?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return [
            "erfolg" => false,
            "meldung" => "Fehler bei der Vorbereitung der Registrierung."
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $login,
        $vorname,
        $nachname,
        $kennwortHash,
        $teamname
    );

    try {
        mysqli_stmt_execute($stmt);

        return [
            "erfolg" => true,
            "meldung" => "Teamchef und Team erfolgreich erstellt!"
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
    <label>Login Name:</label><br>
    <input type="text" name="loginname" maxlength="50" required><br>

    <label>Vorname:</label><br>
    <input type="text" name="vorname" maxlength="50" required><br>

    <label>Nachname:</label><br>
    <input type="text" name="nachname" maxlength="50" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort" maxlength="100" required><br>

    <label>Team Name:</label><br>
    <input type="text" name="teamname" maxlength="50" required><br><br>

    <input type="submit" name="submit_all" value="Registrieren">
</form>

<hr>

<?php
if (isset($_POST['submit_all'])) {

    $login = trim($_POST['loginname']);
    $vorname = trim($_POST['vorname']);
    $nachname = trim($_POST['nachname']);
    $kennwort = trim($_POST['kennwort']);
    $teamname = trim($_POST['teamname']);

    if ($login == "" || $vorname == "" || $nachname == "" || $kennwort == "" || $teamname == "") {
        echo "Bitte alle Pflichtfelder ausfüllen!";
    } else {

        // Passwort wird nicht im Klartext gespeichert, sondern als Hashwert
        $kennwortHash = password_hash($kennwort, PASSWORD_DEFAULT);

        // Funktion aufrufen und Rückmeldung ausgeben
        $result = neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwortHash, $teamname);
        echo h($result['meldung']);
    }
}
?>