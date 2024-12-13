<?php
header('Content-Type: application/json; charset=utf-8');

$db_server = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "users";

$connect = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$connect) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка соединения с базой данных']);
    exit;
}

mysqli_set_charset($connect, 'utf8mb4');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim(strip_tags($_POST['login']));
    $password = trim(strip_tags($_POST['password']));

    if (!empty($login) && !empty($password)) {
        $sql = "SELECT Password FROM users WHERE Login = ?";
        $stmt = mysqli_prepare($connect, $sql);
        mysqli_stmt_bind_param($stmt, "s", $login);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashed_password);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($hashed_password) {
            if (password_verify($password, $hashed_password)) {
                echo json_encode(['status' => 'success', 'message' => 'Успешный вход']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Неверный логин или пароль']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Пользователь не найден']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все поля']);
    }
}

mysqli_close($connect);
?>


