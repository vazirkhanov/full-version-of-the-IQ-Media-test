<?php
// Включаем вывод ошибок на этапе разработки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе данных с использованием класса Connection
include 'connect.php';
$connection = new Connection();
$pdo = $connection->open();

// Подключаем класс SessionManager
include 'SessionManager.php';

// Функция для аутентификации пользователя
function authenticateUser($username, $password)
{
    global $pdo;
    $getUserQuery = "SELECT * FROM users WHERE username = ?";
    $statement = $pdo->prepare($getUserQuery);
    $statement->execute([$username]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

// Аутентификация пользователя при отправке формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($username && $password) {
        $user = authenticateUser($username, $password);
        if ($user) {
            // Создаем объект SessionManager
            $sessionManager = new SessionManager();

            // Устанавливаем переменные сессии для пользователя
            $sessionManager->setSessionVariable('user_id', $user['id']);
            $sessionManager->setSessionVariable('username', $user['username']);

            // Другие переменные сессии, которые вам могут понадобиться

            header("Location: dashboard.php");
            exit(); // Важно завершить выполнение кода после перенаправления
        } else {
            $errorMessage = "Invalid user name or password.";
        }
    } else {
        $errorMessage = "Enter your user name and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="w-450">
            <form class="shadow p-3" method="POST" action="">
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
                <label for="username">Login:</label>
                <input class="form-control" type="text" id="username" name="username" required><br>

                <label for="password">Password:</label>
                <input class="form-control" type="password" id="password" name="password" required><br>

                <div class="d-flex justify-content-between align-items-center">
                    <center>
                        <button type="submit" class="btn btn-outline-primary">Authorize in the system</button>
                        &nbsp; &nbsp; &nbsp; &nbsp; 
                        <a href="../" class="btn btn-outline-primary">Go to home</a>
                    </center>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
