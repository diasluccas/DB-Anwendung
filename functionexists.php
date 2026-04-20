<?php
function exists($connection, $table, $column, $value) {
    $sql = "SELECT * FROM $table WHERE $column = '$value' LIMIT 1";
    $result = mysqli_query($connection, $sql);

    return mysqli_num_rows($result) > 0;
}
?>