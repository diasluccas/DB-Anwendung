<?php

// Funktion zur Erstellung einer eindeutigen, zufälligen RennID
function generiereZufallsID($connection) {
    do {
        // Erzeugt eine Zufallszahl (z.B. zwischen 1000 und 9999)
        $id = rand(1000, 9999);
        
        // Prüfen, ob die ID schon in der Datenbank existiert
        $check = mysqli_query($connection, "SELECT RennID FROM Rennen WHERE RennID = '$id'");
    } while (mysqli_num_rows($check) > 0); // Wenn ID existiert, neue generieren
    
    return $id;
}
?>