<?php
$tytul1 = "Pierwszy wpis na moim blogu";
$tresc1 = "Halo testy test";

$tytul2 = "Drugi wpis – Co u mnie?";
$tresc2 = "Studiuje informatyke i interesuje sie bazami danych";

$tytul3 = "Trzeci wpis – Plany na przyszłość";
$tresc3 = "Dodac kolejna gre";

$wpisy = [
    ["tytul" => $tytul1, "tresc" => $tresc1],
    ["tytul" => $tytul2, "tresc" => $tresc2],
    ["tytul" => $tytul3, "tresc" => $tresc3],
];

//PĘTLĄ
foreach ($wpisy as $wpis) {
    echo "<article class='post'>";
    echo "<h2>{$wpis['tytul']}</h2>";
    echo "<p>{$wpis['tresc']}</p>";
    echo "</article>";
}
?>