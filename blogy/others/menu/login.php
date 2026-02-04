<?php
session_start();
require_once '../../includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE U_Name = ?");
    $stmt->execute([$user]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    


if ($userData && password_verify($pass, $userData['U_password'])) {
    $_SESSION['user_id'] = $userData['UserID']; 
    $_SESSION['username'] = $userData['U_Name'];
    $_SESSION['role'] = $userData['U_role'];
    if ($userData['U_status'] !== 'active') {
        $error = "Twoje konto oczekuje na aktywację przez administratora.";
    } else {
    
    header("Location: ../../index.php");
    exit;
}
    } else {
        $error = "Nieprawidłowy login lub hasło.";
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif;">
        <h2>Logowanie</h2>
        <?php if ($error): ?>
            <p style="color: red; background: #fee; padding: 10px;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Login:</label><br>
            <input type="text" name="username" required style="width: 100%; margin-bottom: 15px;"><br>
            <label>Hasło:</label><br>
            <input type="password" name="password" required style="width: 100%; margin-bottom: 15px;"><br>
            <button type="submit" style="width: 100%; padding: 10px; cursor: pointer;">Zaloguj się</button>
        </form>
        <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
    </div>
</body>
</html>