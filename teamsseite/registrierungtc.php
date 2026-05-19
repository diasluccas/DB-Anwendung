<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
include_once 'funktionen/exists.php';

function neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname) {

    mysqli_begin_transaction($connection);

    try {
        $sql1 = "
            INSERT INTO TeamChef (LoginName, Vorname, Nachname, Kennwort)
            VALUES (?, ?, ?, ?)
        ";

        $stmt1 = mysqli_prepare($connection, $sql1);
        mysqli_stmt_bind_param($stmt1, "ssss", $login, $vorname, $nachname, $kennwort);
        mysqli_stmt_execute($stmt1);

        $sql2 = "
            INSERT INTO Team (TeamName, TCLoginName)
            VALUES (?, ?)
        ";

        $stmt2 = mysqli_prepare($connection, $sql2);
        mysqli_stmt_bind_param($stmt2, "ss", $teamname, $login);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($connection);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($connection);
        return false;
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
    } elseif (exists($connection, "TeamChef", "LoginName", $login)) {
        echo "LoginName existiert schon!";
    } elseif (exists($connection, "Team", "TeamName", $teamname)) {
        echo "TeamName existiert schon!";
    } else {
        $erfolg = neuesTeamEintragen($connection, $login, $vorname, $nachname, $kennwort, $teamname);

        if ($erfolg) {
            echo "Registrierung erfolgreich!";
        } else {
            echo "Fehler bei der Registrierung!";
        }
    }
}
?>