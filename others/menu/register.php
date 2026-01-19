<?php
session_start();

// prosta CAPTCHA: losowe dodawanie
$a = rand(0, 9);
$b = rand(0, 9);
$_SESSION['captcha'] = $a + $b;
?>

<form method="POST" action="register_save.php">
    <label>Nick:</label><br>
    <input type="text" name="name"><br><br>

    <label>E-mail:</label><br>
    <input type="text" name="email"><br><br>

    <label>Hasło:</label><br>
    <input type="password" name="password"><br><br>

    <label>CAPTCHA: ile to jest <?= $a ?> + <?= $b ?> ?</label><br>
    <input type="text" name="captcha"><br><br>

    <button type="submit">Zarejestruj</button>
</form>