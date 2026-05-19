<?php

function generiereZufallsID($connection) {

    do {
        $id = rand(1000, 9999);

        $sql = "
            SELECT RennID
            FROM Rennen
            WHERE RennID = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

    } while (mysqli_num_rows($result) > 0);

    return $id;
}
?>