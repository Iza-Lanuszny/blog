<?php
session_start();

$values = [
    '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10,
    'J' => 2, 'Q' => 3, 'K' => 4, 'A' => 11
];

function calculateScore($hand, $values) {
    $score = 0;
    foreach ($hand as $card) {
        $score += $values[$card];
    }
    return $score;
}

if (!isset($_SESSION['deck']) || isset($_POST['reset'])) {
    $faces = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
    $deck = array_merge($faces, $faces, $faces, $faces); 
    shuffle($deck);

    $_SESSION['deck'] = $deck;
    $_SESSION['player_hand'] = [array_pop($_SESSION['deck']), array_pop($_SESSION['deck'])];
    $_SESSION['dealer_hand'] = [array_pop($_SESSION['deck']), array_pop($_SESSION['deck'])];
    $_SESSION['game_over'] = false;
    $_SESSION['message'] = "";
}

if ($_SESSION['game_over'] === false) {
    if (isset($_POST['hit'])) {
        $_SESSION['player_hand'][] = array_pop($_SESSION['deck']);
        if (calculateScore($_SESSION['player_hand'], $values) > 21) {
            $_SESSION['game_over'] = true;
            $_SESSION['message'] = "Fura! Przekroczyłeś 21 punktów. Przegrałeś.";
        }
    }

    if (isset($_POST['stand'])) {
        $_SESSION['game_over'] = true;
        while (calculateScore($_SESSION['dealer_hand'], $values) < 17) {
            $_SESSION['dealer_hand'][] = array_pop($_SESSION['deck']);
        }

        $pScore = calculateScore($_SESSION['player_hand'], $values);
        $dScore = calculateScore($_SESSION['dealer_hand'], $values);

        if ($dScore > 21 || $pScore > $dScore) {
            $_SESSION['message'] = "Wygrałeś! Masz $pScore pkt, a Krupier $dScore pkt.";
        } elseif ($pScore < $dScore) {
            $_SESSION['message'] = "Przegrałeś. Krupier ma $dScore pkt.";
        } else {
            $_SESSION['message'] = "Remis!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Gra w Oczko</title>
    <link rel="stylesheet" href="../../../CSS/style.css">
</head>
<body>

<div class="blackjack-app">
    <h1>Gra w Oczko (21)</h1>
    <div class="menu-nav">
    <a href="../../../index.php" class="btn-back">← Powrót do Menu</a>
</div>

    <div class="hand">
        <h3>Krupier: 
            <?php 
            echo ($_SESSION['game_over']) ? calculateScore($_SESSION['dealer_hand'], $values) : "??"; 
            ?> pkt</h3>
        <div class="cards-wrapper">
            <?php foreach ($_SESSION['dealer_hand'] as $index => $card): ?>
                <div class="card <?php echo ($index != 0 && !$_SESSION['game_over']) ? 'card-back' : ''; ?>">
                    <?php echo ($index == 0 || $_SESSION['game_over']) ? $card : "?"; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="hand">
        <h3>Twoje karty: <?php echo calculateScore($_SESSION['player_hand'], $values); ?> pkt</h3>
        <div class="cards-wrapper">
            <?php foreach ($_SESSION['player_hand'] as $card): ?>
                <div class="card"><b><?php echo $card; ?></b></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="controls">
        <?php if ($_SESSION['game_over']): ?>
            <h2 class="result-msg"><?php echo $_SESSION['message']; ?></h2>
            <form method="post">
                <button type="submit" name="reset" class="btn">Zagraj jeszcze raz</button>
            </form>
        <?php else: ?>
            <form method="post">
                <button type="submit" name="hit" class="btn hit-btn">Dobierz kartę</button>
                <button type="submit" name="stand" class="btn stand-btn">Pasuję</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>