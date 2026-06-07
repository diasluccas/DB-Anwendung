<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei
    Teams-Bereich: Registrierung/Login oder Teamfunktionen nach Anmeldung
-->

<h2>TeamChef</h2>

<?php

// Ohne Login werden Registrierung und Login angezeigt
if (!isset($_SESSION['login_tc'])) {

    include 'teamsseite/registrierungtc.php';
    include 'teamsseite/logintc.php';

} else {

    // Nach dem Login werden die Funktionen für Teamchefs eingebunden
    echo "<b>Eingeloggt als:</b> " . h($_SESSION['login_tc']) . "<br><br>";

    include 'teamsseite/teampflegen.php';
    include 'teamsseite/trainings.php';
    include 'teamsseite/rennenanmelden.php';
    include 'teamsseite/darstellungkz.php';
}

?>