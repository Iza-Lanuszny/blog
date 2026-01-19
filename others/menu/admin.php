<?php
session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;

if ($isAdmin) {
    echo '<li class="admin__link"><a href="admin.php">Panel admi</a></li>';
}
?>