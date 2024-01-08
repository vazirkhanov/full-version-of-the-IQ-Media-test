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

// Функция для регистрации нового пользователя
function registerUser($username, $password, $email)
{
    global $pdo;

    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Подготовка запроса для вставки данных пользователя
    $insertUserQuery = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $statement = $pdo->prepare($insertUserQuery);
    $statement->execute([$username, $hashedPassword, $email]);
}

// Регистрация пользователя при отправке формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;

    if ($username && $password && $email) {
        // Регистрация нового пользователя
        registerUser($username, $password, $email);

        // После успешной регистрации, перенаправление на страницу авторизации
        header("Location: ../");
        exit(); // Важно завершить выполнение кода после перенаправления
    } else {
        $errorMessage = "Fill in the form fields login, password and email";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
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

            <label for="email">E-mail:</label>
            <input class="form-control" type="email" id="email" name="email" required><br>

            <center>
                <button type="submit" class="btn btn-outline-primary">Register in the system</button>
                    &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="../" class="btn btn-outline-primary">Go to home</a>
            </center>
        </form>
    </div>
</div>


</body>
</html>
