<?php
session_start();

// ���������, ���� �� ����� � URL
if (isset($_GET['token'])) {
    // ��������� ����� � ������
    $_SESSION['yandex_token'] = $_GET['token'];

    // �������������� ������������ ������� �� �������� ����������
    header('Location: /novaya-stranitsa.php');
    exit();
}

echo "
<!DOCTYPE html>
<html>
<head>
    <title>����������� ������</title>
    <script>
        // �������� ����� �� ��������� URL
        const hash = window.location.hash.substring(1);
        const params = new URLSearchParams(hash);
        const accessToken = params.get('access_token');

        // ���� ����� ����������, �������������� �� ����������
        if (accessToken) {
            window.location.href = '?token=' + accessToken;
        } else {
            document.body.innerHTML = '<p>������ �����������. ����� �� �������.</p>';
        }
    </script>
</head>
<body>
</body>
</html>
";
