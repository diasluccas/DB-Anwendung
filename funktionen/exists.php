<?php

function exists($connection, $table, $column, $value) {

    $allowedTables = [
        "TeamChef",
        "Team",
        "Rennveranstalter",
        "Fahrer",
        "Rennen",
        "Training",
        "Teilnahme"
    ];

    $allowedColumns = [
        "LoginName",
        "TeamName",
        "RVName",
        "MitarbeiterID",
        "RennID",
        "Datum"
    ];

    if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
        return false;
    }

    $sql = "SELECT 1 FROM $table WHERE $column = ? LIMIT 1";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $value);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result) > 0;
}
?>