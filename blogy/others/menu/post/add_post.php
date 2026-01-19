<?php
session_start();
require_once "../../../includes/db.php";

?>

<form method="POST" action="add_post_save.php">
    <label>Tytuł:</label><br>
    <input type="text" name="title"><br><br>

    <label>Treść:</label><br>
    <textarea name="content"></textarea><br><br>

    <button type="submit">Dodaj post</button>
</form>