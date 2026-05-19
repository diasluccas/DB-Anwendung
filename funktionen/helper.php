<!-- GRUPPE 12 - Grundstruktur / gemeinsame Datei -->

<?php

function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>