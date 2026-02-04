<?php
session_start();
require_once '../../includes/db.php';

$results_per_page = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start_from = ($page - 1) * $results_per_page;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $results_per_page);

    $stmt = $pdo->prepare("SELECT * FROM comments ORDER BY C_date DESC LIMIT ?, ?");
    $stmt->bindValue(1, $start_from, PDO::PARAM_INT);
    $stmt->bindValue(2, $results_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $total_pages = 0;
    $comments = [];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Komentarze - Strona <?php echo $page; ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/feedback.css">
</head>
<body>

<div class="feedback-app">
    <h3>Opinie o blogu</h3>
    
    <div class="nav-links">
        <a href="comments_blog.php" class="btn-link">+ Dodaj swój komentarz</a>
        <a href="../../index.php" class="btn-link">Powrót do strony głównej</a>
    </div>

    <div class="divider"></div>

    <?php if (empty($comments)): ?>
        <p class="empty-msg">Brak komentarzy do wyświetlenia.</p>
    <?php else: ?>
        <?php foreach ($comments as $c): ?>
            <div class="comment-card">
                <div class="comment-header">
                    <span class="user-name"><?php echo htmlspecialchars($c['Nick'] ?? $c['C_Nick']); ?></span>
                    <span class="comment-date"><?php echo $c['C_date']; ?></span>
                </div>
                <p class="comment-body">
                    <?php echo nl2br(htmlspecialchars($c['Content'] ?? $c['C_text'])); ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="comments_list.php?page=<?php echo $page - 1; ?>">&laquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="comments_list.php?page=<?php echo $i; ?>" 
               class="<?php echo ($page == $i) ? 'active' : ''; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="comments_list.php?page=<?php echo $page + 1; ?>">&raquo;</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>