<?php
session_start();
require_once '../../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Błąd: Musisz się zalogować.");
}

$postID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$currentUserID = $_SESSION['user_id'];

$userRole = $_SESSION['u_status'] ?? 'User'; 

if ($postID > 0) {
    try {

        $stmt = $pdo->prepare("SELECT UserID FROM posts WHERE PostID = ?");
        $stmt->execute([$postID]);
        $post = $stmt->fetch();

        if ($post) {
 
            if ($userRole === 'Admin' || $currentUserID == $post['UserID']) {
                
                $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE PostID = ?");
                $deleteStmt->execute([$postID]);
  
                header("Location: post.php?status=deleted");
                exit;
            } else {
                echo "<script>alert('Nie masz uprawnień do usunięcia tego posta!'); window.location.href='post.php';</script>";
            }
        } else {
            die("Post nie istnieje.");
        }
    } catch (PDOException $e) {
        die("Błąd krytyczny bazy danych: " . $e->getMessage());
    }
}