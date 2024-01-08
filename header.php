
<?php
// Включаем вывод ошибок на этапе разработки
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'connect.php';  // Include only once
include_once 'SessionManager.php';

// Подключение к базе данных с использованием класса Connection
$connection = new Connection();  // This is where you use the Connection class
$pdo = $connection->open();

// Подключаем класс SessionManager
$sessionManager = new SessionManager();

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
            // Устанавливаем переменные сессии для пользователя
            $sessionManager->setSessionVariable('user_id', $user['id']);
            $sessionManager->setSessionVariable('username', $user['username']);

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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/core/logo-PhotoRoom.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="jquery.dataTables.min.css">
    <title>The Cut</title>
    <style>
        body {
            margin-bottom: 60px; /* Установка отступа для контента страницы, чтобы не перекрывать футер */
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 60px; /* Высота футера */
            background-color: #f5f5f5;
        }

        .hidden {
            /* Скрываем столбец с занчением ИД */
            display: none;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .column-left {
            /* Стили для левой колонки */
        }

        .column-right {
            /* Стили для правой колонки */
        }
        /* Сначала скроем все элементы в футере */
    </style>
</head>
<body>

<header>    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="../">
                <span class="logo">&nbsp;<img src="/img/logo.png" width="90" height="35">&nbsp;&nbsp;</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    $userId = $sessionManager->getSessionVariable('user_id');

                    if ($userId) {
                        // If the user is authenticated, display the Dashboard link
                    ?>
                        <li><a href="dashboard.php" class="nav-link active" aria-current="page" >Dashboard</a></li>
                    <?php
                    }
                    ?>
                </ul>

                <?php
                if ($userId) {
                    // If the user is authenticated, display the logout button
                ?>
                    <div>
                        <form method="post" action="../logout.php">
                            <input type="submit" name="logout" value="log out" class="btn btn-outline-primary">
                        </form>
                    </div>
                <?php
                } else {
                    // If the user is not authenticated, display the signup and signin buttons
                ?>
                    <div>
                        <form method="post" action="signup.php">
                            <input type="submit" name="signup" value="sign up" class="btn btn-outline-primary">
                        </form>
                        <input id="userId" type="hidden" value="<?php echo $userId ?>"/>
                    </div>
                    &nbsp; &nbsp;
                    &nbsp; &nbsp;
                    <div>
                        <form method="post" action="signin.php">
                            <input type="submit" name="signin" value="sign in" class="btn btn-outline-primary">
                        </form>
                        <input id="userId" type="hidden" value="<?php echo $userId ?>"/>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </nav>
</header>
<body>
