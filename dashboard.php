<?php
//проверка и вывод ошибок если они сушествуют
error_reporting(E_ALL);
// ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

include 'SessionManager.php';

// Создаем объект SessionManager
$sessionManager = new SessionManager();

// Получаем значение переменной из сессии
$userID = $sessionManager->getSessionVariable('user_id');
$username = $sessionManager->getSessionVariable('username');

// Проверяем, авторизован ли пользователь
if (!$userID || !$username) {
    // Если не авторизован, перенаправляем на страницу входа
    header("Location: signin.php");
    exit();
}


// Открытие соединения после использования
include 'connect.php';

// Подключение шапки
include 'header.php';

// Подключение подвала
include('footer.php');

// Закрытие соединения после использования
$connection->close();
?>

<center class="mt-5"><h4>List of your links and the number of clicks they have received</h4></center>
    <div class="table-responsive p-5 mt-3">
                        <table id="example" class="table table-bordered table-hover table-striped" style="width:100%">
                            <thead>
                                <th>#</th>
                                <th>link</th>
                                <th>token</th>
                                <th>click count</th>
                            </thead>
                            <tbody>
                                <?php 
                                    include_once('connect.php');
            
                                    $database = new Connection();
                                    $db = $database->open();
                                    try {
                                    $sql = 'SELECT @a:= @a+1 as `num`, `links`.* FROM `links` JOIN (SELECT @a:= 0 FROM DUAL) o WHERE `user_id` = :userID';
                                    
                                    $stmt = $db->prepare($sql);
                                    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                                    $stmt->execute();
                                
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['num']; ?></td>
                                            <td><?php echo $row['link']; ?></td>
                                            <td><?php echo $row['token']; ?></td>
                                            <td><?php echo $row['click_count']; ?></td>
                                        </tr>
                                            <?php
                                        }
                                    } catch (PDOException $e) {
                                        echo "Проверьте соединение с Базой Данных, ошибка при подключении: " . $e->getMessage();
                                    }
        
                                //close connection
                                $database->close();
        
                                ?>
                            </tbody>
                        </table>
    </div>
