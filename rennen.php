<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
include 'db.php';
?>

<h2>Rennveranstalter</h2>

<?php

if (!isset($_SESSION['login_rv'])) {

    include 'rennenseite/registrierungrv.php';
    include 'rennenseite/loginrv.php';

} else {

    echo "<b>Eingeloggt als:</b> " . $_SESSION['login_rv'] . "<br><br>";

    include 'rennenseite/rennenpflegen.php';
    include 'rennenseite/rennenergebnisse.php';


}
?>

