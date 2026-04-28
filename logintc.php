<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
include 'db.php';
?>

<h4>Hast du bereits ein Konto? Dann melde dich hier unten an.</h4>

<form method="POST">
    <label>Login Name:</label><br>
    <input type="text" name="loginname_login" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort_login" required><br><br>

    <input type="submit" name="login_tc" value="Login">
</form>

<?php

if (isset($_POST['login_tc'])) {

    $login = $_POST['loginname_login'];
    $kennwort = $_POST['kennwort_login'];

    $sql = "SELECT * FROM TeamChef WHERE LoginName = '$login'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if ($kennwort == $user['Kennwort']) {

            $_SESSION['login_tc'] = $user['LoginName'];

            header("Location: index.php?seite=teams");
            exit;

        } else {
            echo "Falsches Passwort!";
        }

    } else {
        echo "User existiert nicht!";
    }
}
?>