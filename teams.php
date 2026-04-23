<!-- LUCCAS DIAS - 2024-06-17 -->

<?php
include 'db.php';
?>

<h2>TeamChef</h2>

<?php

if (!isset($_SESSION['login_tc'])) {

    include 'teamsseite/registrierungtc.php';
    include 'teamsseite/logintc.php';

} else {

    echo "<b>Eingeloggt als:</b> " . $_SESSION['login_tc'] . "<br><br>";

    include 'teamsseite/teampflegen.php';
    include 'teamsseite/trainings.php';
    include 'teamsseite/darstellungkz.php';
}
?>