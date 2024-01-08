<?php

require_once 'SessionManager.php';
require_once 'connect.php';

$connection = new Connection();  
$conn = $connection->open();

class LinkShortener {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateToken($min = 5, $max = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDFEGHIJKLMNOPRSTUVWXYZ0123456789';
        $new_chars = str_split($chars);

        $token = '';
        $rand_end = mt_rand($min, $max);

        for ($i = 0; $i < $rand_end; $i++) {
            $token .= $new_chars[mt_rand(0, sizeof($new_chars) - 1)];
        }

        return $token;
    }

    public function shortenLink($link, $userId) {
        $stmt = $this->conn->prepare("SELECT * FROM `links` WHERE `link` = :link");
        $stmt->bindParam(':link', $link);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['user_id'] === null && !$userId) {
                return $_SERVER['SERVER_NAME'] . '/' . $row['token'];
            } else {
                $stmt = $this->conn->prepare("SELECT * FROM `links` WHERE `link` = :link AND `user_id` = :user_id");
                $stmt->bindParam(':link', $link);
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $existingRow = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $_SERVER['SERVER_NAME'] . '/' . $existingRow['token'];
                } else {
                    $token = $this->generateToken();
                }
            }
        } else {
            $token = '';
            while (true) {
                $token = $this->generateToken();
                $stmt = $this->conn->prepare("SELECT * FROM `links` WHERE `token` = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    break;
                }
            }
        }

        $stmt = $this->conn->prepare("INSERT INTO `links` (`link`, `token`, `user_id`, `click_count`) VALUES (:link, :token, :user_id, 0)");
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $_SERVER['SERVER_NAME'] . '/' . $token;
        } else {
            return false;
        }
    }

    public function redirectToOriginalLink($token) {
        $stmt = $this->conn->prepare("SELECT * FROM `links` WHERE `token` = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $clickCount = $row['click_count'] + 1;
            $updateStmt = $this->conn->prepare("UPDATE `links` SET `click_count` = :click_count WHERE `token` = :token");
            $updateStmt->bindParam(':click_count', $clickCount);
            $updateStmt->bindParam(':token', $token);
            $updateStmt->execute();

            header("Location: " . $row['link']);
            exit();
        } else {
            die("Token error");
        }
    }
}

$sessionManager = new SessionManager();
$user_id = $sessionManager->getSessionVariable('user_id');
$linkShortener = new LinkShortener($conn);

if (isset($_GET['cut_link'])) {
    $request = trim($_GET['cut_link']);
    $request = htmlspecialchars($request);
    $shortenedLink = $linkShortener->shortenLink($request, $user_id);

    if ($shortenedLink) {
        $_GET['cut_link'] = $shortenedLink;
    } else {
        // Handle the case where the link is not added
    }
} else {
    $URI = $_SERVER['REQUEST_URI'];
    $token = substr($URI, 1);

    if (strlen($token)) {
        $linkShortener->redirectToOriginalLink($token);
    }
}

$connection->close();
?>
