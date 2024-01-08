<?php

class SessionManager {
    
    public function __construct() {
        // Check if the session is already started
        if (session_status() == PHP_SESSION_NONE) {
            // If not, start the session
            session_start();
        }
    }

    public function setSessionVariable($key, $value) {
        // Установка переменной в сессии
        $_SESSION[$key] = $value;
    }

    public function getSessionVariable($key) {
        // Получение значения переменной из сессии
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function destroySession() {
        // Уничтожение сессии
        session_destroy();
    }
}

?>
