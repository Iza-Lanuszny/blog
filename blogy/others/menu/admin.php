<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('includes/db.php')) {
    require_once 'includes/db.php';
} else {
    require_once '../../includes/db.php';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    return; 
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE UserID = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['status_toggle']) && isset($_GET['id'])) {
    $newStatus = ($_GET['status_toggle'] === 'active') ? 'inactive' : 'active';
    $stmt = $pdo->prepare("UPDATE users SET U_status = ? WHERE UserID = ?");
    $stmt->execute([$newStatus, (int)$_GET['id']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['change_role'])) {
    $stmt = $pdo->prepare("UPDATE users SET U_role = ? WHERE UserID = ?");
    $stmt->execute([$_POST['role'], (int)$_POST['user_id']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['update_password'])) {
    $id = (int)$_POST['user_id'];
    $newPass = $_POST['new_pass'];
    
    if (!empty($newPass)) {
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET U_password = ? WHERE UserID = ?");
        $stmt->execute([$hashedPass, $id]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Hasło zmienione");
        exit;
    }
}

if (basename($_SERVER['PHP_SELF']) !== 'admin.php') {
    echo '<li><a href="others/menu/admin.php">Manage users</a></li>';
    return; 
}


$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Użytkownikami</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: sans-serif; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .active { color: green; font-weight: bold; }
        .inactive { color: red; font-weight: bold; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px; display: inline-block; }
        .btn-del { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container" style="padding: 20px;">
        <h1>Panel Admina - Użytkownicy</h1>
        <p><a href="../../index.php">← Powrót do strony głównej</a></p>
        
        <table class="admin-table">
            <tr>
                <th>Login</th>
                <th>Status</th>
                <th>Rola</th>
                <th>Zmiana hasla</th>
                <th>Akcje</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr>
    <td><strong><?php echo htmlspecialchars($u['U_Name']); ?></strong></td>
    <td>
        <span class="<?php echo $u['U_status']; ?>">
            <?php echo $u['U_status'] === 'active' ? 'Aktywny' : 'Oczekujący'; ?>
        </span>
    </td>
    <td>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="user_id" value="<?php echo $u['UserID']; ?>">
            <select name="role" onchange="this.form.submit()">
                <option value="user" <?php echo $u['U_role'] === 'user' ? 'selected' : ''; ?>>Autor</option>
                <option value="admin" <?php echo $u['U_role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <input type="hidden" name="change_role" value="1">
        </form>
    </td>
    <td>
        <form method="POST" style="display:flex; gap: 5px;">
            <input type="hidden" name="user_id" value="<?php echo $u['UserID']; ?>">
            <input type="password" name="new_pass" placeholder="Nowe hasło" required style="width: 100px; font-size: 11px;">
            <button type="submit" name="update_password" class="btn" style="background: #6c757d; color: white;">OK</button>
        </form>
    </td>
    <td>
        <a href="admin.php?status_toggle=<?php echo $u['U_status']; ?>&id=<?php echo $u['UserID']; ?>" class="btn" style="background: #007bff; color: white;">
            <?php echo $u['U_status'] === 'active' ? 'Dezaktywuj' : 'Zatwierdź'; ?>
        </a>
        <a href="admin.php?delete=<?php echo $u['UserID']; ?>" class="btn btn-del" onclick="return confirm('Usunąć użytkownika?')">Usuń</a>
    </td>
</tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>