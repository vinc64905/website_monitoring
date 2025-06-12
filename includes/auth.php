<?php
// Ініціалізація сесії з подовженим часом життя
ini_set('session.gc_maxlifetime', 3600); // 1 година
session_set_cookie_params(3600); // Час життя cookie сесії
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isModerator() {
    return isLoggedIn() && isset($_SESSION['role']) && ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin');
}

function login($email, $password, $conn) {
    $email = $conn->real_escape_string($email);
    $sql = "SELECT id, password_hash, role FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result === false) {
        error_log("SQL Error: " . $conn->error);
        return false;
    }

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}
?>