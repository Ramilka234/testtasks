<?php
session_start();


if (isset($_GET['token'])) {
    $_SESSION['yandex_token'] = $_GET['token'];
    header('Location: /novaya-stranitsa.php');
    exit();
}

echo "
<!DOCTYPE html>
<html>
<head>
    <title>Àâòîðèçàöèÿ ßíäåêñ</title>
    <script>
        // Ïîëó÷àåì òîêåí èç ôðàãìåíòà URL
        const hash = window.location.hash.substring(1);
        const params = new URLSearchParams(hash);
        const accessToken = params.get('access_token');

        // Åñëè òîêåí ñóùåñòâóåò, ïåðåíàïðàâëÿåì íà îáðàáîò÷èê
        if (accessToken) {
            window.location.href = '?token=' + accessToken;
        } else {
            document.body.innerHTML = '<p>Îøèáêà àâòîðèçàöèè. Òîêåí íå ïîëó÷åí.</p>';
        }
    </script>
</head>
<body>
</body>
</html>
";
