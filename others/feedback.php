<?php
if (!isset($pdo)) {
    require_once 'includes/db.php';
}

$errors = [];
$v = ['nick' => '', 'email' => '', 'msg' => ''];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nick'] = trim($_POST['nick'] ?? '');
    $v['email'] = trim($_POST['email'] ?? '');
    $v['msg'] = trim($_POST['msg'] ?? '');

    if (empty($v['nick'])) $errors['nick'] = "Podaj swój nick!";

    $email_pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    if (empty($v['email'])) {
        $errors['email'] = "E-mail jest wymagany!";
    } elseif (!preg_match($email_pattern, $v['email'])) {
        $errors['email'] = "Niepoprawny format adresu e-mail.";
    }

    if (empty($v['msg'])) $errors['msg'] = "Wpisz treść komentarza!";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (C_Nick, C_Email, C_text) VALUES (?, ?, ?)");
            $stmt->execute([$v['nick'], $v['email'], $v['msg']]);
            $success = true;
            $v = ['nick' => '', 'email' => '', 'msg' => ''];
        } catch (PDOException $e) {
            $errors['db'] = "Błąd bazy danych: " . $e->getMessage();
        }
    }
}
?>
<link rel="stylesheet" href="../css/style.css">

<div class="feedback-app">
    <h3>Leave a feedback!</h3>

    <?php if ($success): ?>
        <p class="msg-success">Thank you! Your feedback has been added.</p>
    <?php endif; ?>

    <?php if (isset($errors['db'])): ?>
        <p class="msg-error"><?php echo $errors['db']; ?></p>
    <?php endif; ?>

    <form action="index.php?str=2" method="POST" class="comment-form">
        <div class="field-group">
            <label for="nick">Nick:</label>
            <input type="text" name="nick" id="nick" 
                   class="<?php echo isset($errors['nick']) ? 'input-error' : ''; ?>"
                   value="<?php echo htmlspecialchars($v['nick']); ?>">
            <?php if (isset($errors['nick'])): ?>
                <span class="error-text"><?php echo $errors['nick']; ?></span>
            <?php endif; ?>
        </div>

        <div class="field-group">
            <label for="email">E-mail:</label>
            <input type="text" name="email" id="email" 
                   class="<?php echo isset($errors['email']) ? 'input-error' : ''; ?>"
                   value="<?php echo htmlspecialchars($v['email']); ?>">
            <?php if (isset($errors['email'])): ?>
                <span class="error-text"><?php echo $errors['email']; ?></span>
            <?php endif; ?>
        </div>

        <div class="field-group">
            <label for="msg">Comment:</label>
            <textarea name="msg" id="msg" rows="4" 
                      class="<?php echo isset($errors['msg']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($v['msg']); ?></textarea>
            <?php if (isset($errors['msg'])): ?>
                <span class="error-text"><?php echo $errors['msg']; ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit">Send Feedback</button>
    </form>

    <div class="divider"></div>

    <div class="comments-list">
        <h3>Public feedbacks:</h3>
        <?php
        try {
            $stmt = $pdo->query("SELECT C_Nick, C_text, C_date FROM comments ORDER BY C_date DESC");
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($comments)) {
                echo "<p class='empty-msg'>No comments yet. Be the first!</p>";
            } else {
                foreach ($comments as $c) {
                    ?>
                    <div class="comment-card">
                        <div class="comment-header">
                            <span class="user-name"><?php echo htmlspecialchars($c['C_Nick']); ?></span>
                            <span class="comment-date"><?php echo $c['C_date']; ?></span>
                        </div>
                        <p class="comment-body">
                            <?php echo nl2br(htmlspecialchars($c['C_text'])); ?>
                        </p>
                    </div>
                    <?php
                }
            }
        } catch (PDOException $e) {
            echo "<p class='msg-error'>Could not load comments.</p>";
        }
        ?>
    </div>
</div>