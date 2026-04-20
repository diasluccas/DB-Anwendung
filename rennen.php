<?php
include 'db.php';
?>

<h3>Rennen</h3>

<?php
$result = mysqli_query($connection,"SELECT * FROM Rennen");

while ($row = mysqli_fetch_assoc($result)) {
    print ($row['RennID']);
    print ($row['Datum']);
    print ($row['StartOrt']);
    print ($row['AnzahlKm']);
    print ($row['HoehenMeter']);
    print ($row['MaxSteigung']);
    print ($row['RVName']);
    print ($row['TeamChef']);
}

mysqli_close($connection);

?>
