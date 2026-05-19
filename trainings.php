<!-- LUCCAS DIAS - 2026-05-05 -->

<h4>Trainings hinzufügen</h4>

<?php

$login = $_SESSION['login_tc'];

$sql = "
SELECT F.MitarbeiterID, F.Nachname
FROM Fahrer F
WHERE F.TCLoginName = '$login'
";

$result = mysqli_query($connection, $sql);

$sql2 = "
SELECT Ziel 
FROM Trainingsziel
";

$result2 = mysqli_query($connection, $sql2);
?>

<form method="POST">

    <label>Fahrer auswählen:</label><br>

    <select name="mitarbeiter_id" required>

        <?php
        if (mysqli_num_rows($result) == 0) {
            echo "<option value=''>Keine Fahrer vorhanden</option>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['MitarbeiterID']}'>
                        {$row['MitarbeiterID']} - {$row['Nachname']}
                      </option>";
            }
        }
        ?>

    </select><br><br>

    <label>Datum:</label><br>
    <input type="date" name="datum" required><br>

    <label>Kilometer:</label><br>
    <input type="number" name="km" required><br>

    <label>Ziel:</label><br>
    <select name="ziel">
        <?php
            while ($row = mysqli_fetch_assoc($result2)) {
                echo "<option value='{$row['Ziel']}'>
                        {$row['Ziel']}
                      </option>";
            }
        ?>
    </select><br><br>

    <input type="submit" name="add_training" value="Speichern">
    <br><br>
</form>

<?php

if (isset($_POST['add_training'])) {

    $fahrer = $_POST['mitarbeiter_id'];
    $datum = $_POST['datum'];
    $km = $_POST['km'];
    $ziel = $_POST['ziel'];

    $checkDatum = "
    SELECT * FROM Training 
    WHERE MitarbeiterID = '$fahrer'
    AND Datum = '$datum'
    ";

    $resDatum = mysqli_query($connection, $checkDatum);

    if (mysqli_num_rows($resDatum) > 0) {
        echo "Für diesen Tag existiert bereits ein Training!";
        reload();
    }

    $sqlInsert = "
    INSERT INTO Training (TCLoginName, MitarbeiterID, Datum, Km, Ziel)
    VALUES ('$login', '$fahrer', '$datum', '$km', '$ziel')
    ";

    mysqli_query($connection, $sqlInsert);

    echo "Training erfolgreich gespeichert!";
}
?>
<br><br>