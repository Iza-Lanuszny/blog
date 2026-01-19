<?php

$strona = isset($_GET['str']) && is_numeric($_GET['str']) ? (int)$_GET['str'] : 1;
if ($strona < 1) $strona = 1;


$maxStron = 3;


$prev = $strona > 1 ? $strona - 1 : 1;
$next = $strona < $maxStron ? $strona + 1 : $maxStron;

echo "<div class='paginacja'>
        <a href='index.php?str=$prev'class='prev'>&laquo; </a>
        <span class='num'> $strona/$maxStron</span>
        <a href='index.php?str=$next'class='next'>&raquo;</a>
      </div>";
?>