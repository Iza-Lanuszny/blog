<?php
session_start();
require_once '../../../includes/db.php';

// 1. FUNKCJA BB-CODE
function parseBBCode($text) {
    $text = htmlspecialchars($text);
    $find = ['/\[b\](.*?)\[\/b\]/is', '/\[i\](.*?)\[\/i\]/is', '/\[u\](.*?)\[\/u\]/is'];
    $replace = ['<strong>$1</strong>', '<em>$1</em>', '<u>$1</u>'];
    $text = preg_replace($find, $replace, $text);
    return nl2br($text);
}

// 2. DANE SESJI
$currentUserID = $_SESSION['user_id'] ?? null;
$userName      = $_SESSION['u_name']  ?? null;
$userEmail     = $_SESSION['u_mail']  ?? '';
$userRole      = $_SESSION['u_status'] ?? 'User';

// 3. OBSŁUGA DODAWANIA KOMENTARZA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $pID = (int)$_POST['post_id'];
    $commentText = trim($_POST['Comms_Text']);
    $nick = ($userName) ? $userName : "unknown";

    if (!empty($commentText)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comms (PostID, UserID, Comm_Nick, Comms_Email, Comms_Text, C_date) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$pID, $currentUserID, $nick, $userEmail, $commentText]);
            header("Location: post.php?status=success" . (isset($_GET['cat']) ? "&cat=".$_GET['cat'] : ""));
            exit;
        } catch (PDOException $e) {
            die("Błąd zapisu: " . $e->getMessage());
        }
    }
}

// 4. POBIERANIE KATEGORII DO MENU
$all_categories = $pdo->query("SELECT * FROM categories ORDER BY Cat_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// 5. FILTROWANIE POSTÓW
$cat_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($cat_filter > 0) {
    $stmt = $pdo->prepare("SELECT p.*, u.U_Name FROM posts p JOIN users u ON p.UserID = u.UserID WHERE p.CategoriesID = ? ORDER BY p.p_date DESC");
    $stmt->execute([$cat_filter]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $posts = $pdo->query("SELECT p.*, u.U_Name FROM posts p JOIN users u ON p.UserID = u.UserID ORDER BY p.p_date DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Blog - Posty</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 20px; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; }
        .nav-bar { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .category-menu { display: flex; flex-wrap: wrap; gap: 8px; border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px; }
        .cat-link { text-decoration: none; background: #e2e8f0; color: #475569; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; }
        .cat-link.active { background: #3b82f6; color: white; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: relative; }
        .comment { background: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 10px; border-left: 3px solid #3b82f6; }
        .btn-send { background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
        .admin-actions { float: right; }
        .admin-actions a { font-size: 0.8rem; margin-left: 10px; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="../../../index.php" style="text-decoration: none; color: #333;">← Menu główne</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="add_post.php" class="btn-send">+ Dodaj post</a>
            <?php endif; ?>
        </div>
        
        <div class="category-menu">
            <strong>Kategorie:</strong>
            <a href="post.php" class="cat-link <?= $cat_filter == 0 ? 'active' : '' ?>">Wszystkie</a>
            <?php foreach ($all_categories as $cat): ?>
                <a href="post.php?cat=<?= $cat['CategoriesID'] ?>" class="cat-link <?= $cat_filter == $cat['CategoriesID'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['Cat_name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($posts)): ?>
        <div class="card"><p>Brak postów do wyświetlenia.</p></div>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>
        <div class="card">
            <div class="admin-actions">
                <?php if (isset($_SESSION['user_id']) && ($userRole === 'Admin' || $_SESSION['user_id'] == $post['UserID'])): ?>
                    <a href="edit_post.php?id=<?= $post['PostID'] ?>" style="color: orange;">Edytuj</a>
                    <a href="delete_post.php?id=<?= $post['PostID'] ?>" style="color: red;" onclick="return confirm('Usunąć wpis?')">Usuń</a>
                <?php endif; ?>
            </div>

            <h2><?= htmlspecialchars($post['p_title']) ?></h2>
            <p><small>Autor: <?= htmlspecialchars($post['U_Name']) ?> | <?= $post['p_date'] ?></small></p>
            <hr>
            <div class="post-text"><?= parseBBCode($post['p_text']) ?></div>

            <div style="margin-top: 30px;">
                <h4>Komentarze</h4>
                <?php
                $cStmt = $pdo->prepare("SELECT c.*, u.U_Name as RegName FROM comms c LEFT JOIN users u ON c.UserID = u.UserID WHERE c.PostID = ? ORDER BY c.C_date ASC");
                $cStmt->execute([$post['PostID']]);
                $comments = $cStmt->fetchAll();

                foreach ($comments as $com): 
                    $finalNick = $com['RegName'] ?? $com['Comm_Nick'];
                ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($finalNick) ?></strong> 
                        <small style="color: #888;"><?= $com['C_date'] ?></small>
                        <p style="margin: 5px 0 0;"><?= parseBBCode($com['Comms_Text']) ?></p>
                    </div>
                <?php endforeach; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="post_id" value="<?= $post['PostID'] ?>">
                        <textarea name="Comms_Text" placeholder="Dodaj komentarz..." required style="width:100%; height:50px; padding:8px; border-radius:5px; border:1px solid #ddd;"></textarea>
                        <button type="submit" name="add_comment" class="btn-send" style="margin-top: 5px;">Wyślij</button>
                    </form>
                <?php else: ?>
                    <p><small><a href="../login.php">Zaloguj się</a>, aby dodać komentarz.</small></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>