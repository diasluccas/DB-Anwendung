<!-- Autor:  Felix Weber
 Datei:  rennenseite/rennenergebnisse.php
 Zweck:  Rennergebnisse erfassen. Einmalige Erfassung, sortiert nach Startnummer.
 Zugriff nur für eingeloggte Rennveranstalter. -->

<?php

// Zugriffschutz: Nur eingeloggte Rennveranstalter dürfen diese Seite sehen
if (!isset($_SESSION['login_rv'])) {
    echo "Bitte zuerst als Rennveranstalter einloggen.";
    exit;
}

$rv = $_SESSION['login_rv']; // Eingeloggter Rennveranstalter aus der Session
$meldungErgebnisse = "";

// Ausgewählte RennID aus GET- oder POST-Parameter lesen
$auswahl_id = $_REQUEST['auswahl_id'] ?? "";

// Formular wurde abgeschickt → Ergebnisse speichern
if (isset($_POST['speichern']) && $auswahl_id != "") {

    // Sicherheitsprüfung: Gehört das Rennen wirklich zu diesem Rennveranstalter?
    $sqlCheckRennen = "
        SELECT RennID
        FROM Rennen
        WHERE RennID = ?
        AND RVName = ?
        LIMIT 1
    ";

    $stmtCheckRennen = mysqli_prepare($connection, $sqlCheckRennen);
    mysqli_stmt_bind_param($stmtCheckRennen, "is", $auswahl_id, $rv);
    mysqli_stmt_execute($stmtCheckRennen);
    $resultCheckRennen = mysqli_stmt_get_result($stmtCheckRennen);

    if (mysqli_num_rows($resultCheckRennen) == 0) {
        // Rennen gehört nicht zu diesem RV → kein Zugriff
        $meldungErgebnisse = "Kein Zugriff auf dieses Rennen.";
    } else {

        $gespeichert = 0; // Zähler für erfolgreich gespeicherte Ergebnisse

        // Alle eingesendeten Platzierungen durchlaufen
        foreach ($_POST['platz'] as $startnr => $platz) {

            $startnr = trim($startnr);
            $platz   = trim($platz);
            $zeit    = trim($_POST['zeit'][$startnr] ?? "");

            // Unvollständige Einträge überspringen
            if ($platz == "" || $zeit == "") {
                continue;
            }

            // Ergebnis nur speichern wenn noch keine Platzierung vorhanden (einmalige Erfassung)
            $sqlUpdate = "
                UPDATE Teilnahme
                SET Platzierung = ?,
                    Fahrzeit = ?
                WHERE RennID = ?
                AND Startnummer = ?
                AND Platzierung IS NULL
            ";

            // Prepared Statement: i=Integer, s=String
            $stmtUpdate = mysqli_prepare($connection, $sqlUpdate);
            mysqli_stmt_bind_param(
                $stmtUpdate,
                "isii",
                $platz,
                $zeit,
                $auswahl_id,
                $startnr
            );

            mysqli_stmt_execute($stmtUpdate);

            // Prüfen ob tatsächlich eine Zeile geändert wurde
            if (mysqli_stmt_affected_rows($stmtUpdate) > 0) {
                $gespeichert++;
            }
        }

        // Rückmeldung je nach Ergebnis
        if ($gespeichert > 0) {
            $meldungErgebnisse = $gespeichert . " Ergebnisse wurden gespeichert.";
        } else {
            $meldungErgebnisse = "Es wurden keine neuen Ergebnisse gespeichert. Möglicherweise wurden die Ergebnisse bereits erfasst.";
        }
    }
}

// Alle Rennen des eingeloggten Rennveranstalters für die Auswahlliste laden
$sqlRennen = "
    SELECT RennID, Datum, StartOrt
    FROM Rennen
    WHERE RVName = ?
    ORDER BY Datum DESC, StartOrt
";

$stmtRennen = mysqli_prepare($connection, $sqlRennen);
mysqli_stmt_bind_param($stmtRennen, "s", $rv);
mysqli_stmt_execute($stmtRennen);
$rennen_liste = mysqli_stmt_get_result($stmtRennen);

?>

<h4>Rennergebnisse erfassen</h4>

<!-- Erfolgs- oder Fehlermeldung anzeigen -->
<?php if ($meldungErgebnisse != ""): ?>
    <p><b><?= h($meldungErgebnisse) ?></b></p>
<?php endif; ?>

<!-- Dropdown zur Rennauswahl, wird bei Änderung automatisch abgeschickt -->
<form method="GET" action="index.php">
    <input type="hidden" name="seite" value="rennen">

    <select name="auswahl_id" onchange="this.form.submit()">
        <option value="">Rennen wählen</option>

        <?php while ($row = mysqli_fetch_assoc($rennen_liste)): ?>
            <option value="<?= h($row['RennID']) ?>"
                <?= ($auswahl_id == $row['RennID']) ? 'selected' : '' ?>>
                <?= h($row['RennID']) ?> -
                <?= h($row['Datum']) ?> -
                <?= h($row['StartOrt']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if ($auswahl_id != ""): ?>

    <?php
    // Teilnehmer des ausgewählten Rennens mit Fahrerdaten laden, sortiert nach Startnummer
    $sqlTeilnehmer = "
        SELECT 
            T.Startnummer,
            T.MitarbeiterID,
            T.TCLoginName,
            T.Platzierung,
            T.Fahrzeit,
            F.Vorname,
            F.Nachname
        FROM Teilnahme T
        INNER JOIN Rennen R ON T.RennID = R.RennID
        INNER JOIN Fahrer F 
            ON T.MitarbeiterID = F.MitarbeiterID
            AND T.TCLoginName = F.TCLoginName
        WHERE T.RennID = ?
        AND R.RVName = ?
        ORDER BY T.Startnummer ASC
    ";

    $stmtTeilnehmer = mysqli_prepare($connection, $sqlTeilnehmer);
    mysqli_stmt_bind_param($stmtTeilnehmer, "is", $auswahl_id, $rv);
    mysqli_stmt_execute($stmtTeilnehmer);
    $teilnehmer = mysqli_stmt_get_result($stmtTeilnehmer);
    ?>

    <?php if (mysqli_num_rows($teilnehmer) == 0): ?>

        <p>Für dieses Rennen sind noch keine Fahrer angemeldet.</p>

    <?php else: ?>

        <!-- Formular zur Ergebniserfassung -->
        <form method="POST" action="index.php?seite=rennen&auswahl_id=<?= h($auswahl_id) ?>">
            <input type="hidden" name="auswahl_id" value="<?= h($auswahl_id) ?>">

            <table border="1" cellpadding="5">
                <tr>
                    <th>Startnr.</th>
                    <th>MitarbeiterID</th>
                    <th>Fahrer</th>
                    <th>Teamchef</th>
                    <th>Platzierung</th>
                    <th>Fahrzeit</th>
                </tr>

                <?php while ($f = mysqli_fetch_assoc($teilnehmer)): ?>
                    <tr>
                        <td><?= h($f['Startnummer']) ?></td>
                        <td><?= h($f['MitarbeiterID']) ?></td>
                        <td><?= h($f['Nachname']) ?>, <?= h($f['Vorname']) ?></td>
                        <td><?= h($f['TCLoginName']) ?></td>

                        <?php if ($f['Platzierung'] === null): ?>
                            <!-- Noch kein Ergebnis → Eingabefelder anzeigen -->
                            <td>
                                <input type="number"
                                       name="platz[<?= h($f['Startnummer']) ?>]"
                                       min="1"
                                       required>
                            </td>

                            <td>
                                <input type="time"
                                       name="zeit[<?= h($f['Startnummer']) ?>]"
                                       step="1"
                                       required>
                            </td>

                        <?php else: ?>
                            <!-- Ergebnis bereits erfasst → nur anzeigen, nicht editierbar -->
                            <td><?= h($f['Platzierung']) ?></td>
                            <td><?= h($f['Fahrzeit']) ?></td>

                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </table>

            <br>

            <input type="submit" name="speichern" value="Ergebnisse speichern">
        </form>

    <?php endif; ?>

<?php endif; ?>

<hr>