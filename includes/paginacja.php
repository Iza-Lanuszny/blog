<?php

$results_per_page = 5; 
$page = isset($_GET['str']) && is_numeric($_GET['str']) ? (int)$_GET['str'] : 1;
if ($page < 1) $page = 1;

$start_from = ($page - 1) * $results_per_page;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $total_posts = $total_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $results_per_page);
} catch (PDOException $e) {
    $total_pages = 0;
}
?>


<div class="paginacja">
    <?php if ($page > 1): ?>
        <a href="index.php?str=<?php echo $page - 1; ?>">&laquo;</a>
    <?php else: ?>
        <a class="disabled">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="index.php?str=<?php echo $i; ?>" 
           class="<?php echo ($i == $page) ? 'active' : ''; ?>">
           <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="index.php?str=<?php echo $page + 1; ?>">&raquo;</a>
    <?php else: ?>
        <a class="disabled">&raquo;</a>
    <?php endif; ?>
</div>
