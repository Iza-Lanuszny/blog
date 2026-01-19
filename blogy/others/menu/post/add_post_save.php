<?php
session_start();
require_once "../../../includes/db.php";

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$author_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        INSERT INTO posts (title, content, author_id, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssi", $title, $content, $author_id);
    $stmt->execute();

    $conn->commit();
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Błąd zapisu posta";
}