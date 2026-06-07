<!-- Luccas Dias
    Klasse zur Berechnung monatlicher Trainingskennzahlen eines Fahrers
-->

<?php

class FahrerAuswertung {

    // Verbindungs- und Filterdaten der Auswertung
    private $connection;
    private $mitarbeiterID;
    private $tcLoginName;
    private $ziel;
    private $von;
    private $bis;

    // Ergebnisarray mit Kennzahlen pro Monat
    private $daten = [];

    // Konstruktor setzt Fahrer und Datenbankverbindung
    public function __construct($connection, $mitarbeiterID, $tcLoginName) {
        $this->connection = $connection;
        $this->mitarbeiterID = $mitarbeiterID;
        $this->tcLoginName = $tcLoginName;
        $this->ziel = "alle";
        $this->von = null;
        $this->bis = null;
    }

    // Getter und Setter für Fahrer- und Filterdaten
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

    // Trainingsdaten laden und Kennzahlen berechnen
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

        // Kilometerwerte nach Monat sammeln
        $temp = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $monat = $row['monat'];
            $km = floatval($row['Km']);

            if (!isset($temp[$monat])) {
                $temp[$monat] = [];
            }

            $temp[$monat][] = $km;
        }

        // Kennzahlen je Monat berechnen
        foreach ($temp as $monat => $werteMonat) {

            sort($werteMonat);

            $anzahl = count($werteMonat);
            $summe = array_sum($werteMonat);
            $durchschnitt = $summe / $anzahl;
            $minimum = min($werteMonat);
            $maximum = max($werteMonat);
            $median = $this->berechneMedian($werteMonat);
            $standardabweichung = $this->berechneStandardabweichung($werteMonat, $durchschnitt);

            $this->daten[$monat] = [
                "anzahl" => $anzahl,
                "summe" => $summe,
                "durchschnitt" => $durchschnitt,
                "minimum" => $minimum,
                "maximum" => $maximum,
                "median" => $median,
                "standardabweichung" => $standardabweichung
            ];
        }
    }

    // Median berechnen
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

    // Standardabweichung berechnen
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