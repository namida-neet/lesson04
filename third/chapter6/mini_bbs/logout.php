<?php
session_start();

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 60 * 10, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
setcookie('email', '', time() - 3600);
setcookie('password', '', time() - 3600);

if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}

session_destroy();

header('Location: login.php');
exit();
?>