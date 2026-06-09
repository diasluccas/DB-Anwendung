<!-- Deniz -->

<?php

if (!isset($_SESSION['login_tc'])) {
    echo "Bitte zuerst als Teamchef einloggen.";
    exit;
}

$login = $_SESSION['login_tc'];
$meldung = "";

$sqlTeam = "
    SELECT TeamName
    FROM Team
    WHERE TCLoginName = ?
    LIMIT 1
";

$stmtTeam = mysqli_prepare($connection, $sqlTeam);
mysqli_stmt_bind_param($stmtTeam, "s", $login);
mysqli_stmt_execute($stmtTeam);
$resultTeam = mysqli_stmt_get_result($stmtTeam);
$team = mysqli_fetch_assoc($resultTeam);

$aktuellerTeamname = $team['TeamName'] ?? "";

if (isset($_POST['alles_speichern'])) {

    $neuerTeamname = trim($_POST['teamname']);

    if ($neuerTeamname == "") {
        $meldung = "Teamname darf nicht leer sein.";
    } else {

        $sqlCheckTeam = "
            SELECT TeamName
            FROM Team
            WHERE TeamName = ?
            AND TCLoginName <> ?
        ";

        $stmtCheckTeam = mysqli_prepare($connection, $sqlCheckTeam);
        mysqli_stmt_bind_param($stmtCheckTeam, "ss", $neuerTeamname, $login);
        mysqli_stmt_execute($stmtCheckTeam);
        $resultCheckTeam = mysqli_stmt_get_result($stmtCheckTeam);

        if (mysqli_num_rows($resultCheckTeam) > 0) {
            $meldung = "Dieser Teamname existiert bereits.";
        } else {

            $sqlUpdateTeam = "
                UPDATE Team
                SET TeamName = ?
                WHERE TCLoginName = ?
            ";

            $stmtUpdateTeam = mysqli_prepare($connection, $sqlUpdateTeam);
            mysqli_stmt_bind_param($stmtUpdateTeam, "ss", $neuerTeamname, $login);
            mysqli_stmt_execute($stmtUpdateTeam);

            $aktuellerTeamname = $neuerTeamname;
        }
    }

    $neuID = trim($_POST['neu_mitarbeiter_id'] ?? "");
    $neuVorname = trim($_POST['neu_vorname'] ?? "");
    $neuNachname = trim($_POST['neu_nachname'] ?? "");
    $neuStrasse = trim($_POST['neu_strasse'] ?? "");
    $neuHausnummer = trim($_POST['neu_hausnummer'] ?? "");
    $neuPLZ = trim($_POST['neu_plz'] ?? "");
    $neuOrt = trim($_POST['neu_ort'] ?? "");
    $neuTelNr = trim($_POST['neu_telnr'] ?? "");

    if ($neuID != "" || $neuVorname != "" || $neuNachname != "") {

        if ($neuID == "" || $neuVorname == "" || $neuNachname == "") {
            $meldung = "Für einen neuen Fahrer müssen MitarbeiterID, Vorname und Nachname ausgefüllt sein.";
        } else {

            $sqlCheckFahrer = "
                SELECT MitarbeiterID
                FROM Fahrer
                WHERE MitarbeiterID = ?
                AND TCLoginName = ?
                LIMIT 1
            ";

            $stmtCheckFahrer = mysqli_prepare($connection, $sqlCheckFahrer);
            mysqli_stmt_bind_param($stmtCheckFahrer, "ss", $neuID, $login);
            mysqli_stmt_execute($stmtCheckFahrer);
            $resultCheckFahrer = mysqli_stmt_get_result($stmtCheckFahrer);

            if (mysqli_num_rows($resultCheckFahrer) > 0) {
                $meldung = "Diese MitarbeiterID existiert in deinem Team bereits.";
            } else {

                $sqlInsert = "CALL sp_fahrer_speichern(?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtInsert = mysqli_prepare($connection, $sqlInsert);
                mysqli_stmt_bind_param(
                    $stmtInsert,
                    "sssssssss",
                    $neuID,
                    $login,
                    $neuVorname,
                    $neuNachname,
                    $neuStrasse,
                    $neuHausnummer,
                    $neuPLZ,
                    $neuOrt,
                    $neuTelNr
                );

                mysqli_stmt_execute($stmtInsert);
            }
        }
    }

    if (isset($_POST['fahrer']) && is_array($_POST['fahrer'])) {

        foreach ($_POST['fahrer'] as $mitarbeiterID => $daten) {

            $mitarbeiterID = trim($mitarbeiterID);

            if (isset($daten['loeschen'])) {

                $sqlDelete = "
                    DELETE FROM Fahrer
                    WHERE MitarbeiterID = ?
                    AND TCLoginName = ?
                ";

                $stmtDelete = mysqli_prepare($connection, $sqlDelete);
                mysqli_stmt_bind_param($stmtDelete, "ss", $mitarbeiterID, $login);

                if (!mysqli_stmt_execute($stmtDelete)) {

                    if (mysqli_stmt_errno($stmtDelete) == 1644) {
                        $meldung = mysqli_stmt_error($stmtDelete);
                    } else {
                        $meldung = "Fehler beim Löschen des Fahrers.";
                    }
                }

            } else {

                $vorname = trim($daten['vorname']);
                $nachname = trim($daten['nachname']);
                $strasse = trim($daten['strasse']);
                $hausnummer = trim($daten['hausnummer']);
                $plz = trim($daten['plz']);
                $ort = trim($daten['ort']);
                $telnr = trim($daten['telnr']);

                if ($vorname != "" && $nachname != "") {

                    $sqlUpdate = "CALL sp_fahrer_speichern(?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmtUpdate = mysqli_prepare($connection, $sqlUpdate);
                    mysqli_stmt_bind_param(
                        $stmtUpdate,
                        "sssssssss",
                        $mitarbeiterID,
                        $login,
                        $vorname,
                        $nachname,
                        $strasse,
                        $hausnummer,
                        $plz,
                        $ort,
                        $telnr
                    );

                    mysqli_stmt_execute($stmtUpdate);
                }
            }
        }
    }

    if ($meldung == "") {
        $meldung = "Änderungen wurden gespeichert.";
    }
}

$sqlFahrer = "
    SELECT *
    FROM Fahrer
    WHERE TCLoginName = ?
    ORDER BY MitarbeiterID
";

$stmtFahrer = mysqli_prepare($connection, $sqlFahrer);
mysqli_stmt_bind_param($stmtFahrer, "s", $login);
mysqli_stmt_execute($stmtFahrer);
$resultFahrer = mysqli_stmt_get_result($stmtFahrer);

?>

<hr>

<h4>Mein Team pflegen</h4>

<?php if ($meldung != ""): ?>
    <p><b><?= h($meldung) ?></b></p>
<?php endif; ?>

<form method="POST">

    <label>Teamname:</label><br>
    <input type="text" name="teamname" value="<?= h($aktuellerTeamname) ?>" required>

    <h5>Meine Fahrer</h5>

    <table border="1" cellpadding="5">
        <tr>
            <th>MitarbeiterID</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Straße</th>
            <th>Hausnummer</th>
            <th>PLZ</th>
            <th>Ort</th>
            <th>Telefon</th>
            <th>Löschen</th>
        </tr>

        <tr>
            <td><input type="text" name="neu_mitarbeiter_id"></td>
            <td><input type="text" name="neu_vorname"></td>
            <td><input type="text" name="neu_nachname"></td>
            <td><input type="text" name="neu_strasse"></td>
            <td><input type="text" name="neu_hausnummer"></td>
            <td><input type="text" name="neu_plz"></td>
            <td><input type="text" name="neu_ort"></td>
            <td><input type="text" name="neu_telnr"></td>
            <td>Neue Zeile</td>
        </tr>

        <?php if (mysqli_num_rows($resultFahrer) == 0): ?>

            <tr>
                <td colspan="9">Noch keine Fahrer vorhanden.</td>
            </tr>

        <?php else: ?>

            <?php while ($fahrer = mysqli_fetch_assoc($resultFahrer)): ?>

                <tr>
                    <td>
                        <?= h($fahrer['MitarbeiterID']) ?>
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][vorname]"
                               value="<?= h($fahrer['Vorname']) ?>"
                               required>
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][nachname]"
                               value="<?= h($fahrer['Nachname']) ?>"
                               required>
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][strasse]"
                               value="<?= h($fahrer['Strasse']) ?>">
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][hausnummer]"
                               value="<?= h($fahrer['Hausnummer']) ?>">
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][plz]"
                               value="<?= h($fahrer['PLZ']) ?>">
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][ort]"
                               value="<?= h($fahrer['Ort']) ?>">
                    </td>

                    <td>
                        <input type="text"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][telnr]"
                               value="<?= h($fahrer['TelNr']) ?>">
                    </td>

                    <td>
                        <input type="checkbox"
                               name="fahrer[<?= h($fahrer['MitarbeiterID']) ?>][loeschen]"
                               value="1">
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php endif; ?>

    </table>

    <br>

    <input type="submit" name="alles_speichern" value="Speichern">

</form>

<hr>