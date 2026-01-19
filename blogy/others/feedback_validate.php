<?php

session_start();

if (!isset($_POST['captcha'])) {
    die("Brak odpowiedzi!");
}

if ($_POST['captcha'] == $_SESSION['captcha_result']) {
    echo "CAPTCHA poprawna ✔";
} else {
    echo "Błędna CAPTCHA ❌";
}

    $errors=[];
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    if(!empty($errors)) {
        header("Location: feedback.php?errors=".urlencode(json_encode($errors)) . "$old=" . urlencode(json_encode($_POST)));
        exit;
    }

    if (!isset($_SESSION['captcha_ok'])) {
    $errors['captcha'] = "Błąd sesji CAPTCHA!";
} else {
    if ((int)$captcha !== $_SESSION['captcha_ok']) {
        $errors['captcha'] = "Zły wynik działania!";
    }
}

if (!empty($errors)) {
    echo "<h2>Błędy w formularzu:</h2>";
    print_r($errors);
    exit;
}

    
    echo "<h2>Comment sent!</h2>";
    echo "<p>Name: $name</p>";
    echo "<p>E-mail:$email</p>";
    echo "<p>Comment: $comment</p>";
    ?>