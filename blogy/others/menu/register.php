<?php
session_start();
require_once '../../includes/db.php';


if (!isset($_SESSION['captcha_string']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    $_SESSION['captcha_string'] = "$a + $b";
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $captcha = $_POST['captcha'] ?? '';


    if ($captcha != $_SESSION['captcha_answer']) {
        $error = "Błędny wynik działania!";
    } 

    elseif (empty($user) || empty($email) || strlen($pass) < 6) {
        $error = "Wypełnij wszystkie pola (hasło min. 6 znaków).";
    } 
    else {
       
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE U_Name = ? OR U_mail = ?");
        $check->execute([$user, $email]);
        
        if ($check->fetchColumn() > 0) {
            $error = "Login lub e-mail jest już zajęty.";
        } else {
            try {
               
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                
                
                $stmt = $pdo->prepare("INSERT INTO users (U_Name, U_mail, U_password, U_role) VALUES (?, ?, ?, 'user')");
                
                if ($stmt->execute([$user, $email, $hashedPass])) {
                    $success = "Konto załoźone pomyślnie! <a href='login.php'>Zaloguj się</a>";
                }
            } catch (PDOException $e) {
                $error = "Błąd bazy danych: " . $e->getMessage();
            }
        }
    }
    
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    $_SESSION['captcha_string'] = "$a + $b";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif;">
        <h2>Rejestracja</h2>

        <?php if ($error): ?>
            <p style="color: red; background: #fee; padding: 10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color: green; background: #efe; padding: 10px;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Login:</label><br>
            <input type="text" name="username" required style="width: 100%; margin-bottom: 15px;"><br>

            <label>E-mail:</label><br>
            <input type="email" name="email" required style="width: 100%; margin-bottom: 15px;"><br>

            <label>Hasło:</label><br>
            <input type="password" name="password" required style="width: 100%; margin-bottom: 15px;"><br>

            <div style="background: #f4f4f4; padding: 10px; margin-bottom: 15px;">
                <label>CAPTCHA: Ile to jest <b><?php echo $_SESSION['captcha_string']; ?></b>?</label><br>
                <input type="number" name="captcha" required>
            </div>

            <button type="submit" style="width: 100%; padding: 10px; cursor: pointer;">Zarejestruj się</button>
        </form>
    </div>
</body>
</html>