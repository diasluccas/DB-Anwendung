<?php

$host = "localhost";
$user = "gruppe12";
$password = "Z$;tra..u1h+";
$db = "gruppe12";

$connection = mysqli_connect($host, $user, $password, $db);

if (!$connection) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8mb4");
?>