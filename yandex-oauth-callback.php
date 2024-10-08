<?php
session_start();

// Проверяем, есть ли токен в URL
if (isset($_GET['token'])) {
    // Сохраняем токен в сессии
    $_SESSION['yandex_token'] = $_GET['token'];

    // Перенаправляем пользователя обратно на страницу компонента
    header('Location: /novaya-stranitsa.php');
    exit();
}

echo "
<!DOCTYPE html>
<html>
<head>
    <title>Авторизация Яндекс</title>
    <script>
        // Получаем токен из фрагмента URL
        const hash = window.location.hash.substring(1);
        const params = new URLSearchParams(hash);
        const accessToken = params.get('access_token');

        // Если токен существует, перенаправляем на обработчик
        if (accessToken) {
            window.location.href = '?token=' + accessToken;
        } else {
            document.body.innerHTML = '<p>Ошибка авторизации. Токен не получен.</p>';
        }
    </script>
</head>
<body>
</body>
</html>
";
