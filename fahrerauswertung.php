<?php

class FahrerAuswertung {

    private $connection;
    private $fahrerId;
    private $daten = [];

    public function __construct($connection, $fahrerId) {
        $this->connection = $connection;
        $this->fahrerId = $fahrerId;
    }

    public function setFahrer($fahrerId) {
        $this->fahrerId = $fahrerId;
    }

    public function berechne($von = null, $bis = null, $ziel = 'alle') {

        $sql = "
        SELECT DATE_FORMAT(Datum, '%Y-%m') AS monat, Km
        FROM Training
        WHERE FahrerID = '{$this->fahrerId}'
        ";

        if (!empty($von) && !empty($bis)) {
            $sql .= " AND Datum BETWEEN '$von' AND '$bis'";
        }

        if ($ziel != 'alle') {
            $sql .= " AND Ziel = '$ziel'";
        }

        $result = mysqli_query($this->connection, $sql);

        $temp = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $temp[$row['monat']][] = $row['Km'];
        }

        foreach ($temp as $monat => $werte) {

            sort($werte);
            $count = count($werte);

            $sum = array_sum($werte);
            $avg = $sum / $count;
            $min = min($werte);
            $max = max($werte);
            $median = $this->berechneMedian($werte);
            $std = $this->berechneStd($werte, $avg);

            $this->daten[$monat] = [
                'sum' => $sum,
                'avg' => $avg,
                'min' => $min,
                'max' => $max,
                'median' => $median,
                'std' => $std
            ];
        }
    }

    private function berechneStd($werte, $avg) {
        $sum = 0;
        foreach ($werte as $w) {
            $sum += pow($w - $avg, 2);
        }
        return sqrt($sum / count($werte));
    }

    private function berechneMedian($werte) {
        $count = count($werte);
        if ($count % 2 == 0) {
            return ($werte[$count/2 - 1] + $werte[$count/2]) / 2;
        } else {
            return $werte[floor($count/2)];
        }
    }

    public function getDaten() {
        return $this->daten;
    }

    public function getMonat($monat) {
        return $this->daten[$monat] ?? null;
    }
}
?>