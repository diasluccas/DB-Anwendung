<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
include 'db.php';
?>

<h4>Hast du bereits ein Konto? Dann melde dich hier unten an.</h4>

<form method="POST">
    <label>Rennveranstalter Name:</label><br>
    <input type="text" name="rvname_login" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort_login" required><br><br>

    <input type="submit" name="login_rv" value="Login">
</form>

<?php

if (isset($_POST['login_rv'])) {

    $login = $_POST['rvname_login'];
    $kennwort = $_POST['kennwort_login'];

    $sql = "SELECT * FROM Rennveranstalter WHERE RVName = '$login'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if ($kennwort == $user['Kennwort']) {

            $_SESSION['login_rv'] = $user['RVName'];

            header("Location: index.php?seite=rennen");
            exit;

        } else {
            echo "Falsches Passwort!";
        }

    } else {
        echo "User existiert nicht!";
    }
}
?>