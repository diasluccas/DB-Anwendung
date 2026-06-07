<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei
    Rennen-Bereich: Registrierung/Login oder Rennveranstalter-Funktionen nach Anmeldung
-->

<h2>Rennveranstalter</h2>

<?php

// Ohne Login werden Registrierung und Login angezeigt
if (!isset($_SESSION['login_rv'])) {

    include 'rennenseite/registrierungrv.php';
    include 'rennenseite/loginrv.php';

} else {

    // Nach dem Login werden die Funktionen für Rennveranstalter eingebunden
    echo "<b>Eingeloggt als:</b> " . h($_SESSION['login_rv']) . "<br><br>";

    include 'rennenseite/rennenpflegen.php';
    include 'rennenseite/rennenergebnisse.php';
}

?>