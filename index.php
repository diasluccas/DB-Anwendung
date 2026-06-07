<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei
    Startseite mit Navigation, Sessionsteuerung und Einbindung der Teilseiten
-->

<?php
session_start();

include_once 'db.php';
include_once 'funktionen/helper.php';

// Logout durchführen und Session beenden
if (isset($_GET['seite']) && $_GET['seite'] == 'logout') {
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
}

// Gewünschte Seite aus der URL lesen, Standardseite ist Teams
$seite = $_GET['seite'] ?? 'teams';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>RennHub</title>
</head>
<body>

<h1>RennHub</h1>

<hr>

<!-- Navigation abhängig vom Loginstatus anzeigen -->
<?php if (!isset($_SESSION['login_tc']) && !isset($_SESSION['login_rv'])): ?>

    <a href="index.php?seite=teams">Teams</a> |
    <a href="index.php?seite=rennen">Rennen</a>

<?php elseif (isset($_SESSION['login_tc'])): ?>

    <a href="index.php?seite=teams">Teams</a>

<?php elseif (isset($_SESSION['login_rv'])): ?>

    <a href="index.php?seite=rennen">Rennen</a>

<?php endif; ?>

<?php if (isset($_SESSION['login_tc']) || isset($_SESSION['login_rv'])): ?>
    | <a href="index.php?seite=logout">Logout</a>
<?php endif; ?>

<hr>

<?php

// Passende Teilseite laden und Zugriff je Rolle prüfen
switch ($seite) {

    case 'teams':

        if (isset($_SESSION['login_rv'])) {
            echo "Kein Zugriff!";
            break;
        }

        include 'teams.php';
        break;

    case 'rennen':

        if (isset($_SESSION['login_tc'])) {
            echo "Kein Zugriff!";
            break;
        }

        include 'rennen.php';
        break;

    default:
        include 'teams.php';
        break;
}

?>

</body>
</html>