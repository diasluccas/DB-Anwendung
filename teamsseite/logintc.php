<!-- Deniz -->

<h4>Hast du bereits ein Konto? Dann melde dich hier unten an.</h4>

<form method="POST">
    <label>Login Name:</label><br>
    <input type="text" name="loginname_login" maxlength="50" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort_login" maxlength="100" required><br><br>

    <input type="submit" name="login_tc" value="Login">
</form>

<?php

// Login wird verarbeitet, wenn das Formular abgeschickt wurde
if (isset($_POST['login_tc'])) {

    // Eingaben aus dem Formular holen
    $login = trim($_POST['loginname_login']);
    $kennwort = trim($_POST['kennwort_login']);

    // Teamchef nach LoginName suchen
    $sql = "
        SELECT LoginName, Kennwort
        FROM TeamChef
        WHERE LoginName = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Passwort gegen den gespeicherten Hashwert prüfen
        if (password_verify($kennwort, $user['Kennwort'])) {

            session_regenerate_id(true);

            $_SESSION['login_tc'] = $user['LoginName'];

            // Nach erfolgreichem Login zur Teamseite weiterleiten
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