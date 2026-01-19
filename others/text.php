<?php
$tytul1 = "Pierwszy wpis na moim blogu";
$tresc1 = "To jest przykładowa treść pierwszego wpisu. Tutaj możesz wpisać cokolwiek: opis dnia, informacje, ciekawostki itd.";

$tytul2 = "Drugi wpis – Co u mnie?";
$tresc2 = "Drugi wpis zawiera inną treść. Możesz tu dodać swoje zdjęcia, opisy, cokolwiek chcesz opublikować.";

$tytul3 = "Trzeci wpis – Plany na przyszłość";
$tresc3 = "W trzecim wpisie opisuję moje plany na kolejne miesiące. To tylko przykładowy tekst.";

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