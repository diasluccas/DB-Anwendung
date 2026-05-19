<?php

if (!isset($_SESSION['login_tc'])) {
    echo "Bitte zuerst als Teamchef einloggen.";
    exit;
}

$login = $_SESSION['login_tc'];
$meldungAnmeldung = "";

if (isset($_POST['anmeldung_speichern'])) {

    $rennID = trim($_POST['renn_id']);
    $fahrerListe = $_POST['fahrer'] ?? [];

    if ($rennID == "") {
        $meldungAnmeldung = "Bitte ein Rennen auswählen.";
    } else {

        $gespeichert = 0;

        foreach ($fahrerListe as $fahrerID) {

            $fahrerID = trim($fahrerID);

            if ($fahrerID == "") {
                continue;
            }

            $sqlCheck = "
                SELECT MitarbeiterID
                FROM Teilnahme
                WHERE RennID = ?
                AND MitarbeiterID = ?
                AND TCLoginName = ?
                LIMIT 1
            ";

            $stmtCheck = mysqli_prepare($connection, $sqlCheck);
            mysqli_stmt_bind_param($stmtCheck, "iss", $rennID, $fahrerID, $login);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);

            if (mysqli_num_rows($resultCheck) > 0) {
                continue;
            }

            $sqlInsert = "
                INSERT INTO Teilnahme (
                MitarbeiterID,
                TCLoginName,
                RennID,
                Startnummer
                )
                VALUES (?, ?, ?, 0)
            ";

            $stmtInsert = mysqli_prepare($connection, $sqlInsert);
            mysqli_stmt_bind_param($stmtInsert, "ssi", $fahrerID, $login, $rennID);

            if (mysqli_stmt_execute($stmtInsert)) {
                $gespeichert++;
            }
        }

        if ($gespeichert > 0) {
            $meldungAnmeldung = $gespeichert . " Fahrer erfolgreich angemeldet.";
        } else {
            $meldungAnmeldung = "Es wurden keine neuen Fahrer angemeldet.";
        }
    }
}

if (isset($_POST['copy_speichern'])) {

    $altesRennen = trim($_POST['altes_rennen']);
    $neuesRennen = trim($_POST['neues_rennen']);

    if ($altesRennen == "" || $neuesRennen == "") {
        $meldungAnmeldung = "Bitte Quellrennen und Zielrennen auswählen.";
    } elseif ($altesRennen == $neuesRennen) {
        $meldungAnmeldung = "Quellrennen und Zielrennen dürfen nicht identisch sein.";
    } else {

        $sqlAlteFahrer = "
            SELECT MitarbeiterID
            FROM Teilnahme
            WHERE RennID = ?
            AND TCLoginName = ?
            ORDER BY Startnummer
        ";

        $stmtAlteFahrer = mysqli_prepare($connection, $sqlAlteFahrer);
        mysqli_stmt_bind_param($stmtAlteFahrer, "is", $altesRennen, $login);
        mysqli_stmt_execute($stmtAlteFahrer);
        $resultAlteFahrer = mysqli_stmt_get_result($stmtAlteFahrer);

        $kopiert = 0;

        while ($row = mysqli_fetch_assoc($resultAlteFahrer)) {

            $fahrerID = $row['MitarbeiterID'];

            $sqlCheckCopy = "
                SELECT MitarbeiterID
                FROM Teilnahme
                WHERE RennID = ?
                AND MitarbeiterID = ?
                AND TCLoginName = ?
                LIMIT 1
            ";

            $stmtCheckCopy = mysqli_prepare($connection, $sqlCheckCopy);
            mysqli_stmt_bind_param($stmtCheckCopy, "iss", $neuesRennen, $fahrerID, $login);
            mysqli_stmt_execute($stmtCheckCopy);
            $resultCheckCopy = mysqli_stmt_get_result($stmtCheckCopy);

            if (mysqli_num_rows($resultCheckCopy) > 0) {
                continue;
            }

            $sqlInsertCopy = "
                INSERT INTO Teilnahme (
                MitarbeiterID,
                TCLoginName,
                RennID,
                Startnummer
                )
                VALUES (?, ?, ?, 0)
            ";

            $stmtInsertCopy = mysqli_prepare($connection, $sqlInsertCopy);
            mysqli_stmt_bind_param($stmtInsertCopy, "ssi", $fahrerID, $login, $neuesRennen);

            if (mysqli_stmt_execute($stmtInsertCopy)) {
                $kopiert++;
            }
        }

        if ($kopiert > 0) {
            $meldungAnmeldung = $kopiert . " Fahrer wurden erfolgreich kopiert.";
        } else {
            $meldungAnmeldung = "Es wurden keine neuen Fahrer kopiert.";
        }
    }
}

$heute = date("Y-m-d");

$sqlRennen = "
    SELECT RennID, Datum, StartOrt
    FROM Rennen
    WHERE Datum >= ?
    ORDER BY Datum, StartOrt
";

$stmtRennen = mysqli_prepare($connection, $sqlRennen);
mysqli_stmt_bind_param($stmtRennen, "s", $heute);
mysqli_stmt_execute($stmtRennen);
$resultRennen = mysqli_stmt_get_result($stmtRennen);

$stmtRennenCopy = mysqli_prepare($connection, $sqlRennen);
mysqli_stmt_bind_param($stmtRennenCopy, "s", $heute);
mysqli_stmt_execute($stmtRennenCopy);
$resultRennenCopy = mysqli_stmt_get_result($stmtRennenCopy);

$sqlAlteRennen = "
    SELECT DISTINCT R.RennID, R.Datum, R.StartOrt
    FROM Rennen R
    INNER JOIN Teilnahme T ON R.RennID = T.RennID
    WHERE T.TCLoginName = ?
    ORDER BY R.Datum DESC, R.StartOrt
";

$stmtAlteRennen = mysqli_prepare($connection, $sqlAlteRennen);
mysqli_stmt_bind_param($stmtAlteRennen, "s", $login);
mysqli_stmt_execute($stmtAlteRennen);
$resultAlteRennen = mysqli_stmt_get_result($stmtAlteRennen);

$sqlFahrer = "
    SELECT MitarbeiterID, Vorname, Nachname
    FROM Fahrer
    WHERE TCLoginName = ?
    ORDER BY Nachname, Vorname
";

$stmtFahrer = mysqli_prepare($connection, $sqlFahrer);
mysqli_stmt_bind_param($stmtFahrer, "s", $login);
mysqli_stmt_execute($stmtFahrer);
$resultFahrer = mysqli_stmt_get_result($stmtFahrer);

$fahrerArray = [];

while ($fahrer = mysqli_fetch_assoc($resultFahrer)) {
    $fahrerArray[] = $fahrer;
}

?>

<h4>Rennen anmelden</h4>

<?php if ($meldungAnmeldung != ""): ?>
    <p><b><?= h($meldungAnmeldung) ?></b></p>
<?php endif; ?>

<form method="GET" action="index.php">
    <input type="hidden" name="seite" value="teams">

    <label>Rennen auswählen:</label><br>
    <select name="renn_id" required>
        <option value="">Rennen auswählen</option>

        <?php while ($rennen = mysqli_fetch_assoc($resultRennen)): ?>
            <option value="<?= h($rennen['RennID']) ?>"
                <?= (isset($_GET['renn_id']) && $_GET['renn_id'] == $rennen['RennID']) ? 'selected' : '' ?>>
                <?= h($rennen['RennID']) ?> -
                <?= h($rennen['Datum']) ?> -
                <?= h($rennen['StartOrt']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Anzahl der Fahrer:</label><br>
    <input type="number" name="anzahl" min="1"
           value="<?= h($_GET['anzahl'] ?? '') ?>" required>

    <br><br>

    <input type="submit" value="Tabelle erzeugen">
</form>

<?php
$rennID = $_GET['renn_id'] ?? "";
$anzahl = $_GET['anzahl'] ?? "";

if ($rennID != "" && $anzahl != "" && $anzahl > 0):

    $zeilen = $anzahl;
?>

<h5>Fahrer für Rennen anmelden</h5>

<form method="POST">
    <input type="hidden" name="renn_id" value="<?= h($rennID) ?>">

    <table border="1" cellpadding="5">
        <tr>
            <th>Zeile</th>
            <th>Fahrer</th>
        </tr>

        <?php for ($i = 1; $i <= $zeilen; $i++): ?>
            <tr>
                <td><?= $i ?></td>
                <td>
                    <select name="fahrer[]">
                        <option value="">leer</option>

                        <?php foreach ($fahrerArray as $fahrer): ?>
                            <option value="<?= h($fahrer['MitarbeiterID']) ?>">
                                <?= h($fahrer['MitarbeiterID']) ?>
                                -
                                <?= h($fahrer['Nachname']) ?>,
                                <?= h($fahrer['Vorname']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </td>
            </tr>
        <?php endfor; ?>
    </table>

    <br>

    <input type="submit" name="anmeldung_speichern" value="Anmeldung speichern">
</form>

<?php endif; ?>

<h5>Anmeldungen von einem Rennen kopieren</h5>

<form method="POST">

    <label>Von Rennen kopieren:</label><br>
    <select name="altes_rennen" required>
        <option value="">Quellrennen auswählen</option>

        <?php while ($altes = mysqli_fetch_assoc($resultAlteRennen)): ?>
            <option value="<?= h($altes['RennID']) ?>">
                <?= h($altes['RennID']) ?> -
                <?= h($altes['Datum']) ?> -
                <?= h($altes['StartOrt']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>In zukünftiges Rennen kopieren:</label><br>
    <select name="neues_rennen" required>
        <option value="">Zielrennen auswählen</option>

        <?php while ($neues = mysqli_fetch_assoc($resultRennenCopy)): ?>
            <option value="<?= h($neues['RennID']) ?>">
                <?= h($neues['RennID']) ?> -
                <?= h($neues['Datum']) ?> -
                <?= h($neues['StartOrt']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <input type="submit" name="copy_speichern" value="Anmeldungen kopieren">

</form>

<br>
<hr>