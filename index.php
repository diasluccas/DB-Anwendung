<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei -->
 
<?php
session_start();

include_once 'db.php';
include_once 'funktionen/helper.php';

if (isset($_GET['seite']) && $_GET['seite'] == 'logout') {
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
}

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