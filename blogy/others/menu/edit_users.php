<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Odmowa dostępu.");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    
 
    if (!empty($_POST['new_password'])) {
        $newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET U_role = ?, U_password = ? WHERE UserID = ?");
        $stmt->execute([$role, $newPass, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET U_role = ? WHERE UserID = ?");
        $stmt->execute([$role, $id]);
    }
    header("Location: admin_users.php");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja użytkownika</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: auto;">
        <h2>Edytuj użytkownika: <?php echo $user['U_Name']; ?></h2>
        <form method="POST">
            <label>Rola:</label><br>
            <select name="role">
                <option value="user" <?php if($user['U_role'] == 'user') echo 'selected'; ?>>Autor (User)</option>
                <option value="admin" <?php if($user['U_role'] == 'admin') echo 'selected'; ?>>Administrator</option>
            </select><br><br>

            <label>Nowe hasło (zostaw puste, by nie zmieniać):</label><br>
            <input type="password" name="new_password"><br><br>

            <button type="submit">Zapisz zmiany</button>
            <a href="admin_users.php">Anuluj</a>
        </form>
    </div>
</body>
</html>