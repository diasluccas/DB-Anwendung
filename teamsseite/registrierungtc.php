<!-- Deniz -->

<?php
include_once 'funktionen/exists.php';

function neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname) {

    $sql = "CALL sp_team_registrieren(?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return false;
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

    return mysqli_stmt_execute($stmt);
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
        $erfolg = neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname);

        if ($erfolg) {
            echo "Registrierung erfolgreich!";
        } else {
            echo "Fehler bei der Registrierung. LoginName oder TeamName existiert möglicherweise bereits.";
        }
    }
}
?>