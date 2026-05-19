<?php
include_once 'klassen/fahrerauswertung.php';

if (!isset($_SESSION['login_tc'])) {
    echo "Bitte zuerst als Teamchef einloggen.";
    exit;
}

$login = $_SESSION['login_tc'];

$zielFilter = $_POST['ziel'] ?? "alle";
$von = $_POST['von'] ?? "";
$bis = $_POST['bis'] ?? "";

?>

<h4>Darstellung Kennzahlen</h4>

<form method="POST">

    <label>Trainingsziel:</label><br>
    <select name="ziel">
        <option value="alle" <?= ($zielFilter == "alle") ? "selected" : "" ?>>
            Alle Ziele
        </option>

        <?php
        $sqlZiele = "
            SELECT Ziel
            FROM Trainingsziel
            ORDER BY Ziel
        ";

        $resultZiele = mysqli_query($connection, $sqlZiele);

        while ($ziel = mysqli_fetch_assoc($resultZiele)):
        ?>
            <option value="<?= h($ziel['Ziel']) ?>"
                <?= ($zielFilter == $ziel['Ziel']) ? "selected" : "" ?>>
                <?= h($ziel['Ziel']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label>Zeitraum von:</label><br>
    <input type="date" name="von" value="<?= h($von) ?>"><br>

    <label>Zeitraum bis:</label><br>
    <input type="date" name="bis" value="<?= h($bis) ?>"><br><br>

    <input type="submit" name="auswertung_anzeigen" value="Auswertung anzeigen">

</form>

<hr>

<?php if (isset($_POST['auswertung_anzeigen'])): ?>

    <?php
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
    ?>

    <h5>Auswertung</h5>

    <?php if (mysqli_num_rows($resultFahrer) == 0): ?>

        <p>Es sind noch keine Fahrer vorhanden.</p>

    <?php else: ?>

        <?php $hatDaten = false; ?>

        <table border="1" cellpadding="5">
            <tr>
                <th>Fahrer</th>
                <th>Monat</th>
                <th>Anzahl Trainings</th>
                <th>Summe km</th>
                <th>Durchschnitt km</th>
                <th>Minimum km</th>
                <th>Maximum km</th>
                <th>Median km</th>
                <th>25%-Quantil</th>
                <th>75%-Quantil</th>
                <th>Standardabweichung</th>
            </tr>

            <?php while ($fahrer = mysqli_fetch_assoc($resultFahrer)): ?>

                <?php
                $auswertung = new FahrerAuswertung(
                    $connection,
                    $fahrer['MitarbeiterID'],
                    $login
                );

                $auswertung->setZiel($zielFilter);

                if ($von != "" || $bis != "") {
                    $auswertung->setZeitraum($von, $bis);
                }

                $auswertung->berechne();
                $daten = $auswertung->getDaten();
                ?>

                <?php foreach ($daten as $monat => $werte): ?>
                    <?php $hatDaten = true; ?>

                    <tr>
                        <td>
                            <?= h($fahrer['MitarbeiterID']) ?> -
                            <?= h($fahrer['Nachname']) ?>,
                            <?= h($fahrer['Vorname']) ?>
                        </td>
                        <td><?= h($monat) ?></td>
                        <td><?= h($werte['anzahl']) ?></td>
                        <td><?= number_format($werte['summe'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['durchschnitt'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['minimum'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['maximum'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['median'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['quantil25'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['quantil75'], 2, ',', '.') ?></td>
                        <td><?= number_format($werte['standardabweichung'], 2, ',', '.') ?></td>
                    </tr>

                <?php endforeach; ?>

            <?php endwhile; ?>

        </table>

        <?php if (!$hatDaten): ?>
            <p>Für die ausgewählten Filter wurden keine Trainingsdaten gefunden.</p>
        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>

<hr>