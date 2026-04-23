<!-- LUCCAS DIAS - 2024-06-17 -->

<?php
include 'db.php';
?>
        <h4>Noch kein Konto? Registriere dich hier unten.</h4>
        <form method="POST">
            <label>Login Name:</label><br>
            <input type="text" name="loginname" required><br>

            <label>Vorname:</label><br>
            <input type="text" name="vorname"><br>

            <label>Nachname:</label><br>
            <input type="text" name="nachname"><br>

            <label>Passwort:</label><br>
            <input type="password" name="kennwort" required><br>

            <label>Team Name:</label><br>
            <input type="text" name="teamname" required><br><br>

            <input type="submit" name="submit_all" value="Registrieren">
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
if (isset($_POST['submit_all'])) {

    $login = $_POST['loginname'];
    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $kennwort = $_POST['kennwort'];
    $teamname = $_POST['teamname'];

    if (exists($connection, "TeamChef", "LoginName", $login)) {
        echo "LoginName existiert schon!";
        exit;
    }

    if (exists($connection, "Team", "TeamName", $teamname)) {
        echo "TeamName existiert schon!";
        exit;
    }

    mysqli_begin_transaction($connection);

    try {
        $sql1 = "INSERT INTO TeamChef (LoginName, Vorname, Nachname, Kennwort)
                 VALUES ('$login', '$vorname', '$nachname', '$kennwort')";
        mysqli_query($connection, $sql1);

        $sql2 = "INSERT INTO Team (TeamName, TCLoginName)
                 VALUES ('$teamname', '$login')";
        mysqli_query($connection, $sql2);

        mysqli_commit($connection);
        echo "Registrierung erfolgreich!";

    } catch (Exception $e) {
        mysqli_rollback($connection);
        echo "Fehler!";
    }
}
?>