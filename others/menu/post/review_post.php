<?php
session_start();

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

$_SESSION['title'] = $title;
$_SESSION['content'] = $content;

function bbcode($text) {
    return preg_replace(
        [
            '/\[b\](.*?)\[\/b\]/',
            '/\[i\](.*?)\[\/i\]/',
            '/\[u\](.*?)\[\/u\]/'
        ],
        [
            '<b>$1</b>',
            '<i>$1</i>',
            '<u>$1</u>'
        ],
        nl2br(htmlspecialchars($text))
    );
}
?>

<h2>Podgląd posta</h2>

<h3><?= htmlspecialchars($title) ?></h3>
<p><?= bbcode($content) ?></p>

<form action="post.php">
    <button>Cofnij</button>
</form>

<form action="add_post.php" method="post">
    <button>Zatwierdź</button>
</form>