<?php
header('Content-Type: text/html; charset=utf-8');

$db_server = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "users";

$connect = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$connect) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, 'utf8mb4');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search = trim(strip_tags($_POST['search']));

    if (!empty($search)) {
        $sql = "SELECT * FROM Vedra WHERE
                    Country LIKE ? OR
                    Carname LIKE ? OR
                    Power_HP LIKE ? OR
                    Mileage LIKE ? OR
                    Model LIKE ?";
        $stmt = mysqli_prepare($connect, $sql);
        $search_param = '%' . $search . '%';
        mysqli_stmt_bind_param($stmt, 'sssss', $search_param, $search_param, $search_param, $search_param, $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='car-card'>";
                if (isset($row['Image']) && !empty($row['Image'])) {
                    echo "<img src='" . htmlspecialchars($row['Image']) . "' alt='" . htmlspecialchars($row['Carname']) . "'>";
                } else {
                    echo "<img src='images/default_image.jpg' alt='" . htmlspecialchars($row['Carname']) . "'>";
                }
                echo "<h3>" . htmlspecialchars($row['Carname']) . "</h3>";
                echo "<p>Country: " . htmlspecialchars($row['Country']) . "</p>";
                echo "<p>Power: " . htmlspecialchars($row['Power_HP']) . " HP</p>";
                echo "<p>Mileage: " . htmlspecialchars($row['Mileage']) . " km</p>";
                echo "<p>Model: " . htmlspecialchars($row['Model']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Ничего не найдено по запросу: <strong>" . htmlspecialchars($search) . "</strong></p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Пожалуйста, введите запрос для поиска.</p>";
    }
}

mysqli_close($connect);
?>
