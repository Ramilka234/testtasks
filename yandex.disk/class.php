<?php

use Bitrix\Main\Loader;
use Arhitector\Yandex\Disk;
use Arhitector\Yandex\Client;

class YandexDiskComponent extends CBitrixComponent
{
    private $diskClient;
    private $token;

    public function onPrepareComponentParams($arParams)
    {
        // Проверка на наличие OAuth токена в сессии
        if (isset($_SESSION['yandex_token'])) {
            $this->token = $_SESSION['yandex_token'];
        } else {
            // Перенаправляем на авторизацию через Яндекс OAuth
            $this->redirectToYandexAuth();
        }

        // Инициализация клиента Яндекс Диска, если токен существует
        if ($this->token) {
            $client = new Client\OAuth($this->token); // Инициализируем клиента OAuth
            $this->diskClient = new Disk($client); // Передаем клиент в Disk
        }

        return $arParams;
    }

    public function executeComponent()
    {
        if (!$this->diskClient) {
            return;
        }

        // Действия для загрузки, удаления или изменения файла
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
            $action = $_POST['action'];

            switch ($action) {
                case 'upload':
                    $this->uploadFile();
                    break;
                case 'delete':
                    $this->deleteFile();
                    break;
                case 'update':
                    $this->renameFile();
                    break;
            }
        }

        // Получение списка файлов
        $this->arResult['FILES'] = $this->listFiles();
        $this->includeComponentTemplate();
    }

    private function listFiles()
    {
    $files = [];
    try {
        $resource = $this->diskClient->getResource('/'); 
	
        foreach ($resource->items as $item) {
            $files[] = $item->toArray(['name', 'path', 'file']);
                    }
    } catch (\Exception $e) {
        ShowError("Ошибка при получении списка файлов: " . $e->getMessage());
    }
    return $files;

    }   
    private function uploadFile()
    {
        if (!empty($_FILES['file']['tmp_name'])) {
            try {
		$resource = $this->diskClient->getResource($_FILES['file']['name']);
                $resource->toArray();
            } catch (Arhitector\Yandex\Client\Exception\NotFoundException $exc) {
                $resource->upload($_FILES['file']['tmp_name'],$_FILES['file']['name']);
            }
        }
    }

    private function deleteFile()
    {
        if (!empty($_POST['filePath'])) {
            try {
                $this->diskClient->getResource($_POST['filePath'])->delete();
            } catch (\Exception $e) {
                ShowError("Ошибка при удалении файла: " . $e->getMessage());
            }
        }
    }

    private function renameFile()
    {
        if (!empty($_POST['oldPath']) && !empty($_POST['newName'])) {
            try {
                $this->diskClient->getResource($_POST['oldPath'])->move('/' . $_POST['newName']);
            } catch (\Exception $e) {
                ShowError("Ошибка при изменении имени файла: " . $e->getMessage());
            }
        }
    }
    private function redirectToYandexAuth()
    {
        // Перенаправление на страницу авторизации Яндекс OAuth
        $clientId = '453456182df644e9b11dcd86a15b6c7a';
        $redirectUri = 'https://192.168.1.6/yandex-oauth-callback.php';
        $authUrl = 'https://oauth.yandex.ru/authorize?response_type=token&client_id=' . $clientId . '&redirect_uri=' . urlencode($redirectUri);
        LocalRedirect($authUrl);
        exit;
    }
}
