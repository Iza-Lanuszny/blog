


<?php
session_start();
?>

<h2>Dodaj post</h2>

<form method="post" action="review_post.php">
    <label>Tytuł:</label><br>
    <input type="text" name="title"><br><br>

    <label>Treść (BB-code):</label><br>
    <textarea name="content" rows="8" cols="40"></textarea><br><br>

    <button type="submit">Podgląd</button>
</form>