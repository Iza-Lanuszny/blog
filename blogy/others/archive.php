<?php
$months = [
    "2025-01" => "Styczeń 2025",
    "2024-12" => "Grudzień 2024",
    "2024-11" => "Listopad 2024",
];

echo '<div class="widget archive">';
echo '<h3>Archiwum</h3>';
echo '<ul>';

foreach ($months as $key => $value) {
    echo "<li><a href='?archive=$key'>$value</a></li>";
}

echo '</ul>';
echo '</div>';
?>