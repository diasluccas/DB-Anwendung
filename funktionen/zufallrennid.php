<!-- Felix Weber
    Datei: funktionen/zufallrennid.php
    Zweck:
    - Erzeugt eine zufällige RennID für neue Rennen
    - Prüft, ob die RennID bereits in der Tabelle Rennen existiert
    - Gibt eine eindeutige vierstellige ID zurück -->

<?php

function generiereZufallsID($connection) {

    do {
        // Zufallswert im Bereich 1000 bis 9999 erzeugen
        $id = rand(1000, 9999);

        // SQL-Anfrage zur Existenzprüfung der RennID
        $sql = "
            SELECT RennID
            FROM Rennen
            WHERE RennID = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        // Wenn ein Eintrag zurückkommt, war die RennID bereits vergeben
    } while (mysqli_num_rows($result) > 0);

    // Eindeutige RennID zurückgeben
    return $id;
}
?>