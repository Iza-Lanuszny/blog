<?php
session_start();
require_once "../../../includes/db.php";

$sql = "
SELECT posts.*, users.U_Name 
FROM posts 
JOIN users ON posts.UserID = users.UserID
ORDER BY p_date DESC
";

$result = $conn->query($sql);
?>

<h2>Posty na blogu</h2>

<?php while ($post = $result->fetch_assoc()): ?>
    <article>
        <h3><?= htmlspecialchars($post['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <small>
            Autor: <?= htmlspecialchars($post['U_Name']) ?> |
            <?= $post['created_at'] ?>
        </small>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['author_id']): ?>
            <div>
                <a href="edit_post.php?id=<?= $post['PostID'] ?>">Edytuj</a>
                <a href="delete_post.php?id=<?= $post['PostID'] ?>"
                   onclick="return confirm('Na pewno usunąć?')">
                   Usuń
                </a>
            </div>
        <?php endif; ?>
    </article>
<?php endwhile; ?>