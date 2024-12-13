<?php 
header('Content-Type: application/json; charset=utf-8'); 
 
$db_server = "127.0.0.1"; 
$db_user = "root"; 
$db_pass = ""; 
$db_name = "users"; 
 
$connect = mysqli_connect($db_server, $db_user, $db_pass, $db_name); 
 
if (!$connect) { 
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . mysqli_connect_error()]); 
    exit; 
} 
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $surname = trim(strip_tags($_POST['surname'])); 
    $name = trim(strip_tags($_POST['name'])); 
    $date_birthday = trim(strip_tags($_POST['birthday'])); 
    $mail = trim(strip_tags($_POST['mail'])); 
    $login = trim(strip_tags($_POST['login'])); 
    $password = trim(strip_tags($_POST['password'])); 
 
    if (!empty($surname) && !empty($name) && !empty($date_birthday) && 
        !empty($mail) && !empty($login) && !empty($password) && filter_var($mail, FILTER_VALIDATE_EMAIL)) { 
 
        $subject = "Регистрация на сайте вашего_сайта"; 
        $msg = "Ваши данные формы регистрации:\nФамилия: $surname\nИмя: $name\nДата рождения: $date_birthday"; 
        $headers = "Content-type: text/plain; charset=UTF-8\r\n"; 
        $headers .= "From: no-reply@yourdomain.com\r\n"; 
 
        mail($mail, $subject, $msg, $headers); 
 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
 
        $check_login_sql = "SELECT id FROM users WHERE login = '$login'"; 
        $result = mysqli_query($connect, $check_login_sql); 
 
        if (mysqli_num_rows($result) > 0) { 
            echo json_encode(['status' => 'error', 'message' => 'Этот логин уже занят. Пожалуйста, выберите другой.']); 
        } else { 
            $sql = "INSERT INTO users (Surname, Name, Birthday, Mail, Login, Password) 
                    VALUES ('$surname', '$name', '$date_birthday', '$mail', '$login', '$hashed_password')"; 
 
            if (mysqli_query($connect, $sql)) { 
                header("Location: autorisation.html");
                exit;
            } else { 
                echo json_encode(['status' => 'error', 'message' => 'Ошибка при сохранении данных в базе: ' . mysqli_error($connect)]); 
            } 
        } 
    } else { 
        echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все поля и укажите корректный email.']); 
    } 
} 
 
mysqli_close($connect); 
?>