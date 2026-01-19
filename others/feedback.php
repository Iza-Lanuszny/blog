<?php


$answer = 0;
$num_min = 0;
$num_max = 9;

$operacje = ['plus', 'minus', 'times', 'devided'];
$words = [0 => 'zero', 1=>'one', 2=> 'two', 3=>'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine'];


do {
$operacja = $operacje[array_rand($operacje)];
$a = rand($num_min, $num_max);
$b = rand($num_min, $num_max);

switch($operacja) {
    case 'plus':
        $answer = $a + $b;
        break;
    
    case 'minus':
        if ($a > $b) {
            $answer = $a - $b;
            break;
        } else {
            $answer = $b - $a;
            break;
        }

    case 'times':
        $answer = $a * $b;
        break;

    case 'devided':
        if ($b === 0) {
            $b = rand($num_min, $num_max);
        } if ($a & $b === 0){
            $answer = intdiv($a,$b);
        } else {
            $answer = -1;
        }
        break;
}
} while ($answer < 0 || $answer > 10 || $answer != floor($answer));

$_SESSION['captcha_answer'] = $answer;
$captcha_text = $words[$a] . ' ' . $operacja . ' ' . $words[$b]; 
?>



<?php 
$errors = $_GET['errors'] ?? [];
$old = $_GET['old'] ?? [];

?>

<form action="feedback_validate.php" method="POST" class="comment-form">

        <label 
        for="name">Name:
        <input type="text" id="name" name="name" required><br>
        </label>
        <label
        for="email">E-mail:
        
        <input type="text" id="email" name="email" required><br>
        </label>
        <label 
        for="comment">Comment
        <input type="text" id="comment" name="comment" required><br>
        </label>

        <label>CAPTCHA: Perform the action:</label><br>
        <b><?php echo $captcha_text; ?></b><br><br>
        <input type="text" name="captcha">
    </label>
        
        <button type="submit">Submit</button>
    </form>



    