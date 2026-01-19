<?php
session_start();
require_once "../../../includes/db.php";

$id = (int)$_GET['id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        DELETE FROM posts 
        WHERE PostID = ? AND author_id = ?
    ");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();

    $conn->commit();
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Błąd usuwania posta";
}