<?php
include 'db.php';
?>

<h3>Teams</h3>

<?php
$result = mysqli_query($connection,"SELECT TeamName, TCLoginName FROM Team");

while ($row = mysqli_fetch_assoc($result)) {
    print ($row['TeamName']);
    print ($row['TCLoginName']);
}
mysqli_close($connection);

?>
