<?php
session_start();
require_once '../../../includes/db.php';
error_reporting(E_ALL & ~E_NOTICE);

$all_icons = ['🍎', '🍌', '🍇', '🍓', '🍒', '🍍', '🥝', '🍋', '🍏', '🍐', '🍑', '🍈', '🥥', '🥦', '🥑', '⭐', '🌈', '🍕', '🐱', '🐶'];

$is_reset = isset($_GET['reset']);
$is_size_change = isset($_POST['size']);

if ($is_size_change || !isset($_SESSION['board']) || $is_reset) {
    $size = $is_size_change ? (int)$_POST['size'] : ($_SESSION['size'] ?? 16);
    $_SESSION['size'] = $size;
    
    $temp_icons = $all_icons;
    shuffle($temp_icons); 
    $num_pairs = $size / 2;
    $selected_icons = array_slice($temp_icons, 0, $num_pairs);
    
    $board = array_merge($selected_icons, $selected_icons);
    for ($i = 0; $i < 5; $i++) { shuffle($board); }
    
    $_SESSION['board'] = $board;

    if ($is_size_change || $is_reset) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$board_json = json_encode($_SESSION['board']);
$current_size = $_SESSION['size'] ?? 16;
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Master</title>
    <link rel="stylesheet" href="../../../CSS/style.css">
</head>
<body>

<div class="memory-app">

    <div class="top-nav">
        <a href="../../../index.php" class="nav-btn">Menu Główne</a>
        <a href="list.php" class="nav-btn">Wszystkie Gry</a>
    </div>

    <div class="header-box">
        <h1>Gra Memory</h1>
        
        <?php if ($is_logged_in): ?>
            <p>Witaj, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['u_name']); ?></strong>!</p>
        <?php else: ?>
            <div class="guest-section">
                <label>Grasz jako gość. Wpisz nick, by zapisać wynik:</label><br>
                <input type="text" id="guest-nick" placeholder="Twój pseudonim..." maxlength="20">
            </div>
        <?php endif; ?>

        <form method="post" class="game-settings">
            <select name="size" onchange="this.form.submit()">
                <option value="8" <?php echo $current_size == 8 ? 'selected' : ''; ?>>8 kart (2x4)</option>
                <option value="16" <?php echo $current_size == 16 ? 'selected' : ''; ?>>16 kart (4x4)</option>
                <option value="20" <?php echo $current_size == 20 ? 'selected' : ''; ?>>20 kart (4x5)</option>
                <option value="32" <?php echo $current_size == 32 ? 'selected' : ''; ?>>32 karty (4x8)</option>
            </select>
            <button type="button" class="reset-btn" onclick="window.location.href='?reset=1'">Restart Gry</button>
        </form>
    </div>

    <div class="stats">
        Ruchy: <span id="moves">0</span> | <span id="win-msg"></span>
    </div>

    <div class="grid" id="game-grid" style="max-width: <?php echo ($current_size > 20) ? '800px' : '500px'; ?>;"></div>

    <div class="ranking-section">
        <h3>Ranking Top 10 (Plansza <?php echo $current_size; ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Miejsce</th>
                    <th>Gracz</th>
                    <th>Ruchy</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT ms.*, u.U_Name FROM memory_scores ms LEFT JOIN users u ON ms.UserID = u.UserID WHERE ms.BoardSize = ? ORDER BY ms.Moves ASC, ms.CreatedAt ASC LIMIT 10");
                $stmt->execute([$current_size]);
                $rank = 1;
                $has_results = false;
                while ($row = $stmt->fetch()):
                    $has_results = true;
                    $name = $row['U_Name'] ? htmlspecialchars($row['U_Name']) : htmlspecialchars($row['GuestName']) . ' <small>(Gość)</small>';
                    $is_new = (isset($_SESSION['last_score_id']) && $_SESSION['last_score_id'] == $row['ScoreID']) ? 'class="new-score"' : '';
                ?>
                    <tr <?php echo $is_new; ?>>
                        <td>#<?php echo $rank++; ?></td>
                        <td><?php echo $name; ?></td>
                        <td class="moves-count"><?php echo $row['Moves']; ?></td>
                        <td><?php echo date('d.m H:i', strtotime($row['CreatedAt'])); ?></td>
                    </tr>
                <?php endwhile; 
                if (!$has_results): ?>
                    <tr><td colspan="4" class="no-results">Brak wyników.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> <script>
    const boardData = <?php echo $board_json; ?>;
    const currentSize = <?php echo $current_size; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    let flippedCards = [];
    let matchedCount = 0;
    let moves = 0;
    let lockBoard = false;

    const grid = document.getElementById('game-grid');
    boardData.forEach(icon => {
        const card = document.createElement('div');
        card.classList.add('card');
        card.dataset.icon = icon;
        card.innerHTML = `<div class="face back">?</div><div class="face front">${icon}</div>`;
        card.addEventListener('click', flipCard);
        grid.appendChild(card);
    });

    function flipCard() {
        if (lockBoard || this.classList.contains('flipped') || this.classList.contains('matched')) return;
        this.classList.add('flipped');
        flippedCards.push(this);
        if (flippedCards.length === 2) {
            moves++;
            document.getElementById('moves').innerText = moves;
            checkMatch();
        }
    }

    function checkMatch() {
        lockBoard = true;
        const [c1, c2] = flippedCards;
        if (c1.dataset.icon === c2.dataset.icon) {
            c1.classList.add('matched');
            c2.classList.add('matched');
            matchedCount += 2;
            if (matchedCount === boardData.length) handleWin();
            resetTurn();
        } else {
            setTimeout(() => {
                c1.classList.remove('flipped');
                c2.classList.remove('flipped');
                resetTurn();
            }, 800);
        }
    }

    function handleWin() {
        document.getElementById('win-msg').innerText = "WYGRANA!";
        const nickname = !isLoggedIn ? document.getElementById('guest-nick').value : null;
        if (isLoggedIn || (nickname && nickname.trim() !== "")) {
            fetch('save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ moves: moves, size: currentSize, nickname: nickname })
            }).then(() => { setTimeout(() => location.reload(), 1500); });
        }
    }
    function resetTurn() { [flippedCards, lockBoard] = [[], false]; }
</script>

</body>
</html>