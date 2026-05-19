<?php
include 'db.php';

$auswahl_id = $_REQUEST['auswahl_id'] ?? "";

if (isset($_POST['speichern']) && $auswahl_id != "") {
    foreach ($_POST['platz'] as $startnr => $platz) {
        $zeit = $_POST['zeit'][$startnr];
        $sql = "UPDATE Teilnahme 
                SET Platzierung = '$platz', Fahrzeit = '$zeit' 
                WHERE RennID = '$auswahl_id' AND Startnummer = '$startnr'";
        mysqli_query($connection, $sql);
    }
    echo "Ergebnisse gespeichert.";
}

$rv = $_SESSION['login_rv'];
$rennen_liste = mysqli_query($connection, "SELECT RennID, StartOrt FROM Rennen WHERE RVName = '$rv'");
?>

<h4>Rennen auswählen</h4>
<form method="GET" action="index.php">
    <input type="hidden" name="seite" value="rennen">
    <select name="auswahl_id" onchange="this.form.submit()">
        <option value="">-- Rennen wählen --</option>
        <?php while ($row = mysqli_fetch_assoc($rennen_liste)): ?>
            <option value="<?= $row['RennID'] ?>" <?= ($auswahl_id == $row['RennID']) ? 'selected' : '' ?>>
                <?= $row['RennID'] . " - " . $row['StartOrt'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>
<hr>

<?php if ($auswahl_id != ""): ?>
    <?php
    $teilnehmer = mysqli_query($connection, "SELECT * FROM Teilnahme WHERE RennID = '$auswahl_id' ORDER BY Startnummer ASC");
    ?>
    <form method="POST" action="index.php?seite=rennen&auswahl_id=<?= $auswahl_id ?>">
        <input type="hidden" name="auswahl_id" value="<?= $auswahl_id ?>">
        <table border="1">
            <tr>
                <th>Startnr.</th>
                <th>MitarbeiterID</th>
                <th>Platzierung</th>
                <th>Fahrzeit</th>
            </tr>
            <?php while ($f = mysqli_fetch_assoc($teilnehmer)): ?>
                <tr>
                    <td><?= $f['Startnummer'] ?></td>
                    <td><?= $f['MitarbeiterID'] ?></td>
                    <?php if ($f['Platzierung'] == NULL): ?>
                        <td><input type="number" name="platz[<?= $f['Startnummer'] ?>]" required></td>
                        <td><input type="text" name="zeit[<?= $f['Startnummer'] ?>]" placeholder="00:00:00" required></td>
                    <?php else: ?>
                        <td><?= $f['Platzierung'] ?></td>
                        <td><?= $f['Fahrzeit'] ?></td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <input type="submit" name="speichern" value="Ergebnisse speichern">
    </form>
<?php endif; ?>