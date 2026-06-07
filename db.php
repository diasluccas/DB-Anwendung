<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei
    Stellt die Verbindung zur MySQL-Datenbank her
-->
 
<?php

$host = "localhost";
$user = "gruppe12";
$password = "Z$;tra..u1h+";
$db = "gruppe12";

// Verbindung zur Datenbank herstellen
$connection = mysqli_connect($host, $user, $password, $db);

if (!$connection) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
}

// Zeichensatz für korrekte Umlaute und sichere Ausgaben setzen
mysqli_set_charset($connection, "utf8mb4");
?>