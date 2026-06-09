<!-- Deniz
    Trainings erfassen für Fahrer des eingeloggten Teamchefs
-->

<?php

// Zugriff nur für eingeloggte Teamchefs
if (!isset($_SESSION['login_tc'])) {
    echo "Bitte zuerst als Teamchef einloggen.";
    exit;
}

$login = $_SESSION['login_tc'];
$meldungTraining = "";

// Formulardaten auslesen und prüfen
if (isset($_POST['add_training'])) {

    $fahrer = trim($_POST['mitarbeiter_id']);
    $datum = trim($_POST['datum']);
    $km = trim($_POST['km']);
    $ziel = trim($_POST['ziel']);

    if ($fahrer == "" || $datum == "" || $km == "" || $ziel == "") {
        $meldungTraining = "Bitte alle Felder ausfüllen.";
    } elseif ($km <= 0) {
        $meldungTraining = "Die Kilometeranzahl muss größer als 0 sein.";
    } else {

        // Prüfen, ob für diesen Fahrer an diesem Datum bereits ein Training existiert
        $sqlCheck = "
            SELECT Datum
            FROM Training
            WHERE MitarbeiterID = ?
            AND TCLoginName = ?
            AND Datum = ?
            LIMIT 1
        ";

        $stmtCheck = mysqli_prepare($connection, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "sss", $fahrer, $login, $datum);
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);

        if (mysqli_num_rows($resultCheck) > 0) {
            $meldungTraining = "Für diesen Fahrer existiert an diesem Tag bereits ein Training.";
        } else {

            // Neues Training speichern
            $sqlInsert = "
                INSERT INTO Training (Datum, MitarbeiterID, TCLoginName, Km, Ziel)
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmtInsert = mysqli_prepare($connection, $sqlInsert);
            mysqli_stmt_bind_param($stmtInsert, "sssds", $datum, $fahrer, $login, $km, $ziel);

            if (mysqli_stmt_execute($stmtInsert)) {
                $meldungTraining = "Training erfolgreich gespeichert.";
            } else {
                $meldungTraining = "Fehler beim Speichern des Trainings.";
            }
        }
    }
}

// Fahrer des eingeloggten Teamchefs laden
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

$sqlZiele = "
    SELECT Ziel
    FROM Trainingsziel
    ORDER BY Ziel
";

$resultZiele = mysqli_query($connection, $sqlZiele);

?>

<h4>Trainings hinzufügen</h4>

<?php if ($meldungTraining != ""): ?>
    <p><b><?= h($meldungTraining) ?></b></p>
<?php endif; ?>

<!-- Formular zum Erfassen eines neuen Trainings -->
<form method="POST">

    <label>Fahrer auswählen:</label><br>

    <select name="mitarbeiter_id" required>
        <option value="">Fahrer auswählen</option>

        <?php if (mysqli_num_rows($resultFahrer) == 0): ?>

            <option value="">Keine Fahrer vorhanden</option>

        <?php else: ?>

            <?php while ($row = mysqli_fetch_assoc($resultFahrer)): ?>
                <option value="<?= h($row['MitarbeiterID']) ?>">
                    <?= h($row['MitarbeiterID']) ?> - <?= h($row['Nachname']) ?>, <?= h($row['Vorname']) ?>
                </option>
            <?php endwhile; ?>

        <?php endif; ?>
    </select>

    <br><br>

    <label>Datum:</label><br>
    <input type="date" name="datum" required><br>

    <label>Kilometer:</label><br>
    <input type="number" name="km" step="0.01" min="0.01" required><br>

    <label>Ziel:</label><br>
    <select name="ziel" required>
        <option value="">Ziel auswählen</option>

        <?php if (mysqli_num_rows($resultZiele) == 0): ?>

            <option value="">Keine Trainingsziele vorhanden</option>

        <?php else: ?>

            <?php while ($row = mysqli_fetch_assoc($resultZiele)): ?>
                <option value="<?= h($row['Ziel']) ?>">
                    <?= h($row['Ziel']) ?>
                </option>
            <?php endwhile; ?>

        <?php endif; ?>
    </select>

    <br><br>

    <input type="submit" name="add_training" value="Speichern">

</form>

<br>
<hr>