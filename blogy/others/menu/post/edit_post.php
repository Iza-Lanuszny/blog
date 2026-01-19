<?php
session_start();
require_once "../../../includes/db.php";

$id = (int)$_GET['id'];

$stmt = $conn->prepare("
    SELECT * FROM posts WHERE PostID = ? AND author_id = ?
");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die("Brak dostępu do edycji");
}
?>

<form method="POST" action="edit_post_save.php">
    <input type="hidden" name="id" value="<?= $post['PostID'] ?>">

    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"><br>
    <textarea name="content"><?= htmlspecialchars($post['content']) ?></textarea><br>

    <button type="submit">Zapisz zmiany</button>
</form>