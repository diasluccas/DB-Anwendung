<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei
    Enthält Hilfsfunktionen für die Anwendung
-->
<?php

// Wandelt Sonderzeichen für HTML-Ausgaben um und reduziert XSS-Risiken
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>