<?php
include 'db.php';
include 'functionexists.php';
?>

<h3>Teams</h3>

<h3>Registrierung neuer TeamChef</h3>

<form method="POST" action="">
    <label>Login Name:</label><br>
    <input type="text" name="loginname" required><br>

    <label>Vorname:</label><br>
    <input type="text" name="vorname"><br>

    <label>Nachname:</label><br>
    <input type="text" name="nachname"><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort" required><br><br>

    <label>Team Name:</label><br>
    <input type="text" name="teamname" required><br><br>

    <input type="submit" name="submit_all" value="Registrieren">
</form>

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
    $hashed_pw = password_hash($kennwort, PASSWORD_DEFAULT);

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
                 VALUES ('$login', '$vorname', '$nachname', '$hashed_pw')";
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

<?php
$result = mysqli_query($connection,"SELECT * FROM Team");

while ($row = mysqli_fetch_assoc($result)) {
    print ($row['TeamName']);
    print ($row['TCLoginName']);
}

mysqli_close($connection);

?>
