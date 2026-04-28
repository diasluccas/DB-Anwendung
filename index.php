<!-- LUCCAS DIAS - 2026-04-28 -->

<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
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

$seite = $_GET['seite'] ?? 'teams';

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

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;

    default:
        include 'teams.php';
}
?>

</body>
</html>