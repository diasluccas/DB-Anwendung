<!-- Felix Weber
 Zweck:  Login für Rennveranstalter. Prüft RVName und Kennwort gegen die Datenbank.
 Bei Erfolg wird der RVName in der Session gespeichert und weitergeleitet. -->

<!-- Login-Formular für Rennveranstalter -->
<h4>Hast du bereits ein Konto? Dann melde dich hier unten an.</h4>

<form method="POST">
    <label>Rennveranstalter Name:</label><br>
    <input type="text" name="rvname_login" maxlength="50" required><br>

    <label>Passwort:</label><br>
    <input type="password" name="kennwort_login" maxlength="100" required><br><br>

    <input type="submit" name="login_rv" value="Login">
</form>

<?php

// Formular wurde abgeschickt → Login verarbeiten
if (isset($_POST['login_rv'])) {

    // Eingaben auslesen und Leerzeichen entfernen
    $login    = trim($_POST['rvname_login']);
    $kennwort = trim($_POST['kennwort_login']);

    // Rennveranstalter anhand des RVName in der Datenbank suchen
    $sql = "
        SELECT RVName, Kennwort
        FROM Rennveranstalter
        WHERE RVName = ?
        LIMIT 1
    ";

    // Prepared Statement gegen SQL-Injection
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Prüfen ob der RVName existiert
    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Passwort gegen den gespeicherten Hashwert prüfen
        if (password_verify($kennwort, $user['Kennwort'])) {

            session_regenerate_id(true);

            // Login erfolgreich → RVName in Session speichern und weiterleiten
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