<?php
session_start();
require_once '../../../includes/db.php';


function parseBBCode($text) {
    $text = htmlspecialchars($text);
    $find = [
        '/\[b\](.*?)\[\/b\]/is', 
        '/\[i\](.*?)\[\/i\]/is', 
        '/\[u\](.*?)\[\/u\]/is', 
        '/\[url=(.*?)\](.*?)\[\/url\]/is'
    ];
    $replace = [
        '<strong>$1</strong>', 
        '<em>$1</em>', 
        '<u>$1</u>', 
        '<a href="$1" target="_blank">$2</a>'
    ];
    return preg_replace($find, $replace, $text);
}


if (!isset($_SESSION['user_id'])) {
    die("Błąd: Musisz być zalogowany. <a href='../login.php'>Zaloguj się</a>");
}


try {
    $catStmt = $pdo->query("SELECT CategoriesID, Cat_Name FROM categories"); 
    $allCategories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd pobierania kategorii: " . $e->getMessage());
}

$step = 'form';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['preview_btn'])) {
        $_SESSION['temp_title'] = $_POST['p_title'];
        $_SESSION['temp_text'] = $_POST['p_text'];
        $_SESSION['temp_cat'] = $_POST['categories_id'];
        $step = 'preview';
    } elseif (isset($_POST['save_btn'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (UserID, CategoriesID, p_text, p_title, p_date) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'], 
                $_SESSION['temp_cat'], 
                $_SESSION['temp_text'], 
                $_SESSION['temp_title']
            ]);
            unset($_SESSION['temp_title'], $_SESSION['temp_text'], $_SESSION['temp_cat']);
            header("Location: post.php?status=success");
            exit;
        } catch (PDOException $e) {
            die("Błąd zapisu: " . $e->getMessage());
        }
    } elseif (isset($_POST['back_btn'])) {
        $step = 'form';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj wpis</title>
    <link rel="stylesheet" href="../../../css/style.css">
</head>
<body>

<div style="max-width: 800px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; font-family: Arial;">
    
    <?php if ($step === 'form'): ?>
        <h2>Nowy wpis na blogu</h2>
        <form method="POST">
            <label>Tytuł wpisu:</label><br>
            <input type="text" name="p_title" maxlength="35" required style="width: 100%;" 
                   value="<?php echo htmlspecialchars($_SESSION['temp_title'] ?? ''); ?>"><br><br>

            <label>Wybierz kategorię:</label><br>
            <select name="categories_id" required style="width: 100%; padding: 5px; margin-bottom: 15px;">
                <?php foreach ($allCategories as $cat): ?>
                    <option value="<?php echo $cat['CategoriesID']; ?>" 
                        <?php echo (isset($_SESSION['temp_cat']) && $_SESSION['temp_cat'] == $cat['CategoriesID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['Cat_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Treść (możesz użyć [b], [i], [u]):</label><br>
            <textarea name="p_text" rows="10" required style="width: 100%;"><?php echo htmlspecialchars($_SESSION['temp_text'] ?? ''); ?></textarea><br><br>

            <button type="submit" name="preview_btn">Podejrzyj wpis</button>
        </form>

    <?php elseif ($step === 'preview'): ?>
        <h2>Podgląd przed publikacją</h2>
        <div style="background: #f4f4f4; padding: 20px; border-left: 5px solid #333; margin-bottom: 20px;">
            <p><small>Kategoria ID: <?php echo $_SESSION['temp_cat']; ?></small></p>
            <h3><?php echo htmlspecialchars($_SESSION['temp_title']); ?></h3>
            <hr>
            <div><?php echo parseBBCode($_SESSION['temp_text']); ?></div>
        </div>

        <form method="POST">
            <button type="submit" name="back_btn">Cofnij i edytuj</button>
            <button type="submit" name="save_btn" style="background: green; color: white; padding: 5px 15px;">Opublikuj wpis</button>
        </form>
    <?php endif; ?>

</div>

</body>
</html>