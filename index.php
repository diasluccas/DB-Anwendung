<!DOCTYPE html>
<html lang="de">
<head>
    <title>Radrennen Datenbankanwendung</title>
    <meta name="authors" content="Deniz, Felix und Luccas">
    <meta name="copyright" content="DHBW">
    <meta name="Keywords" content="Datenbankanwendung, Radrennen">
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <link rel="index" href="index.html">
</head>
<body>
    <h1>RennHub</h1>
    <h2>Dein Zentrum für Radrennen.</h2>
    <p><b>WWIBE224</b> Deniz, Felix und Luccas</p>
    <p><b>Note:</b> 1.0</p>

    <hr />
    <a href="index.php?seite=teams">Teams</a> | <a href="index.php?seite=rennen">Rennen</a>
    <hr />


    <?php
    $seite = $_GET['seite'] ?? 'teams';
    $allowed = ['teams', 'rennen'];

    if (!in_array($seite, $allowed)) {
        $seite = 'teams';
    }
    include $seite . '.php';
    ?>

</body>
</html>