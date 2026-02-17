<?php
session_start();

require_once '../../../includes/db.php';


header('Content-Type: application/json');

try {

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['moves']) && isset($data['size'])) {
        $moves = (int)$data['moves'];
        $size = (int)$data['size'];
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
       
        $guestName = (!empty($data['nickname'])) ? htmlspecialchars(trim($data['nickname'])) : null;

   
        if ($userId !== null || $guestName !== null) {
            
            $sql = "INSERT INTO memory_scores (UserID, GuestName, Moves, BoardSize, CreatedAt) 
                    VALUES (:uid, :gn, :moves, :size, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid'   => $userId,
                ':gn'    => $guestName,
                ':moves' => $moves,
                ':size'  => $size
            ]);

  
            $_SESSION['last_score_id'] = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Wynik został zapisany pomyślnie.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Musisz być zalogowany lub podać nick, aby zapisać wynik.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Błędne dane wejściowe.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Błąd bazy danych: ' . $e->getMessage()
    ]);
}