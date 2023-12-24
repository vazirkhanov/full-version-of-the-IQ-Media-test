<?php
// Подключение к базе данных с использованием класса Connection
include 'connect.php';
$connection = new Connection();
$pdo = $connection->open();
// Проверка на успешное подключение
if (!$pdo) {
    echo "Ошибка подключения к базе данных.";
    exit();
}
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
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = authenticateUser($username, $password);
    if ($user) {
        session_start();
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
    } else {
        echo "Неверное имя пользователя или пароль.";
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
            <form class="shadow w-450 p-3" method="POST" action="">
                <label for="username">Логин:</label>
                <input class="form-control" type="text" id="username" name="username" required><br>

                <label for="password">Пароль:</label>
                <input class="form-control" type="password" id="password" name="password" required><br>

                <button type="submit" class="btn btn-outline-primary">Авторизоваться</button>
            </form>
   </div>
</body>
</html>
