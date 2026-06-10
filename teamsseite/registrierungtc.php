<!-- Deniz -->

<?php

function neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname) {

    // Stored Procedure übernimmt Prüfung und INSERT
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
        $kennwort,
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
    <input type="text" name="loginname" required><br>

    <label>Vorname:</label><br>
    <input type="text" name="vorname" required><br>

    <label>Nachname:</label><br>
    <input type="text" name="nachname" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort" required><br>

    <label>Team Name:</label><br>
    <input type="text" name="teamname" required><br><br>

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

        // Funktion aufrufen und Rückmeldung ausgeben
        $result = neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname);
        echo h($result['meldung']);
    }
}
?>