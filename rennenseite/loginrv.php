<!-- LUCCAS DIAS - 2026-04-28 -->

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

    $login = trim($_POST['rvname_login']);
    $kennwort = trim($_POST['kennwort_login']);

    $sql = "
        SELECT RVName, Kennwort
        FROM Rennveranstalter
        WHERE RVName = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

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