<?php
session_start();
require_once "../../includes/db.php";

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$captcha = $_POST['captcha'] ?? '';

$errors = [];

// WALIDACJA
if ($name === '') $errors[] = "Nick jest wymagany";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Niepoprawny e-mail";
if (strlen($password) < 5) $errors[] = "Hasło min. 5 znaków";
if ((int)$captcha !== $_SESSION['captcha']) $errors[] = "Błędna CAPTCHA";

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit;
}

// HASHOWANIE HASŁA (sól jest automatyczna!)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);



// ZAPIS DO BAZY
$stmt = $pdo->prepare("
    INSERT INTO users (U_Name, U_mail, U_password, U_role)
    VALUES (?, ?, ?, 'user')
");
$stmt->execute([$name, $email, $hashedPassword]);

echo "Rejestracja zakończona sukcesem!";

header("Location: login.php");
exit;