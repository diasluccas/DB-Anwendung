<!-- LUCCAS DIAS - 2026-04-28 -->
<?php
function exists($connection, $table, $column, $value) {
    $sql = "SELECT * FROM $table WHERE $column = '$value'";
    $result = mysqli_query($connection, $sql);

    return mysqli_num_rows($result) > 0;
}
?>