<?php
session_start();
require_once "../../../includes/db.php";

$id = (int)$_POST['id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        UPDATE posts 
        SET title = ?, content = ?
        WHERE PostID = ? AND author_id = ?
    ");
    $stmt->bind_param("ssii", $title, $content, $id, $_SESSION['user_id']);
    $stmt->execute();

    $conn->commit();
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Błąd edycji posta";
}
