<?php

class FahrerAuswertung {

    private $connection;
    private $mitarbeiterID;
    private $tcLoginName;
    private $ziel;
    private $von;
    private $bis;

    private $daten = [];

    public function __construct($connection, $mitarbeiterID, $tcLoginName) {
        $this->connection = $connection;
        $this->mitarbeiterID = $mitarbeiterID;
        $this->tcLoginName = $tcLoginName;
        $this->ziel = "alle";
        $this->von = null;
        $this->bis = null;
    }

    public function setFahrer($mitarbeiterID, $tcLoginName) {
        $this->mitarbeiterID = $mitarbeiterID;
        $this->tcLoginName = $tcLoginName;
    }

    public function setZeitraum($von, $bis) {
        $this->von = $von;
        $this->bis = $bis;
    }

    public function setZiel($ziel) {
        $this->ziel = $ziel;
    }

    public function getDaten() {
        return $this->daten;
    }

    public function getMonat($monat) {
        return $this->daten[$monat] ?? null;
    }

    public function berechne() {

        $this->daten = [];

        $sql = "
            SELECT DATE_FORMAT(Datum, '%Y-%m') AS monat, Km
            FROM Training
            WHERE MitarbeiterID = ?
            AND TCLoginName = ?
        ";

        $typen = "ss";
        $werte = [$this->mitarbeiterID, $this->tcLoginName];

        if (!empty($this->von)) {
            $sql .= " AND Datum >= ?";
            $typen .= "s";
            $werte[] = $this->von;
        }

        if (!empty($this->bis)) {
            $sql .= " AND Datum <= ?";
            $typen .= "s";
            $werte[] = $this->bis;
        }

        if (!empty($this->ziel) && $this->ziel != "alle") {
            $sql .= " AND Ziel = ?";
            $typen .= "s";
            $werte[] = $this->ziel;
        }

        $sql .= " ORDER BY monat, Km";

        $stmt = mysqli_prepare($this->connection, $sql);

        if (!$stmt) {
            return;
        }

        mysqli_stmt_bind_param($stmt, $typen, ...$werte);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $temp = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $monat = $row['monat'];
            $km = floatval($row['Km']);

            if (!isset($temp[$monat])) {
                $temp[$monat] = [];
            }

            $temp[$monat][] = $km;
        }

        foreach ($temp as $monat => $werteMonat) {

            sort($werteMonat);

            $anzahl = count($werteMonat);
            $summe = array_sum($werteMonat);
            $durchschnitt = $summe / $anzahl;
            $minimum = min($werteMonat);
            $maximum = max($werteMonat);
            $median = $this->berechneMedian($werteMonat);
            $quantil25 = $this->berechneQuantil($werteMonat, 0.25);
            $quantil75 = $this->berechneQuantil($werteMonat, 0.75);
            $standardabweichung = $this->berechneStandardabweichung($werteMonat, $durchschnitt);

            $this->daten[$monat] = [
                "anzahl" => $anzahl,
                "summe" => $summe,
                "durchschnitt" => $durchschnitt,
                "minimum" => $minimum,
                "maximum" => $maximum,
                "median" => $median,
                "quantil25" => $quantil25,
                "quantil75" => $quantil75,
                "standardabweichung" => $standardabweichung
            ];
        }
    }

    private function berechneMedian($werte) {

        $anzahl = count($werte);

        if ($anzahl == 0) {
            return 0;
        }

        sort($werte);

        $mitte = floor($anzahl / 2);

        if ($anzahl % 2 == 0) {
            return ($werte[$mitte - 1] + $werte[$mitte]) / 2;
        } else {
            return $werte[$mitte];
        }
    }

    private function berechneQuantil($werte, $q) {

        $anzahl = count($werte);

        if ($anzahl == 0) {
            return 0;
        }

        sort($werte);

        $position = ($anzahl - 1) * $q;
        $unten = floor($position);
        $oben = ceil($position);

        if ($unten == $oben) {
            return $werte[$unten];
        }

        $anteil = $position - $unten;

        return $werte[$unten] * (1 - $anteil) + $werte[$oben] * $anteil;
    }

    private function berechneStandardabweichung($werte, $durchschnitt) {

        $anzahl = count($werte);

        if ($anzahl == 0) {
            return 0;
        }

        $summeAbweichung = 0;

        foreach ($werte as $wert) {
            $summeAbweichung += pow($wert - $durchschnitt, 2);
        }

        return sqrt($summeAbweichung / $anzahl);
    }
}
?>