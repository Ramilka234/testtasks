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
        $this->CheckingOAuthtokeninSession();
        // Инициализация клиента Яндекс Диска, если токен существует
        if ($this->token) {
            $this->InitializationYandexDiskClient($token);
    }
        return $arParams;
    }

    public function CheckingOAuthtokeninSession()
    {
        if (isset($_SESSION['yandex_token'])) {
            $this->token = $_SESSION['yandex_token'];
        } else {
            // Перенаправляем на авторизацию через Яндекс OAuth
            $this->redirectToYandexAuth();
        }
    }

    public function InitializationYandexDiskClient($token)
    {
        $client = new Client\OAuth($this->token); // Инициализируем клиента OAuth
        $this->diskClient = new Disk($client); // Передаем клиент в Disk

    }

    public function executeComponent()
    {
        if (!$this->diskClient) {
            return;
        }
        // Действия для загрузки, удаления или изменения файла
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
            $action = $_POST['action'];
            UploadDeleteUpdate($action);
        }
        // Получение списка файлов
        $this->arResult['FILES'] = $this->list();
        $this->includeComponentTemplate();
    }

    public function UploadDeleteUpdateFiles($action){
        switch ($action) {
            case 'upload':
                $this->uploadforfile();
                break;
            case 'delete':
                $this->deleteforfile();
                break;
            case 'update':
                $this->rename();
                break;
        }
    }

    public function list()
    {
        try {
             $this->listFiles();
        }catch (\Exception $e) {
             $this->logError("Ошибка при получении списка файлов: ".$e);
        }    
    }

    private function listFiles()
    {
    	$files = [];
    	$resource = $this->diskClient->getResource('/'); 
    	foreach ($resource->items as $item) {
        	$files[] = $item->toArray(['name', 'path', 'file']);
                	}
    	return $files;

    }   

    public function uploadforfile()
    {
        try {
            $resource = $this->diskClient->getResource($_FILES['file']['name']);
            $resource->toArray();
        }catch (Arhitector\Yandex\Client\Exception\NotFoundException $exc) {
             $this->uploadFile($resource);
        }    
    }

    private function uploadFile($resource)
    {
        if (!empty($_FILES['file']['tmp_name'])) {
        $resource->upload($_FILES['file']['tmp_name'],$_FILES['file']['name']);
            }
    }    
    
    public function deleteforfile()
    {
        try {
             $this->deleteFile();
        }catch (\Exception $e) {
             $this->logError("Ошибка при удалении файла: ".$e);
        }    
    }

    private function deleteFile()
    {
        if (!empty($_POST['filePath'])) {
            $this->diskClient->getResource($_POST['filePath'])->delete();
        }
    }

    public function rename()
    {
        try {
             $this->renameFile();
        }catch (\Exception $e) {
             $this->logError("Ошибка при изменении имени файла: ".$e);
        }    
    }

    private function renameFile()
    {
        if (!empty($_POST['oldPath']) && !empty($_POST['newName'])) {
                $this->diskClient->getResource($_POST['oldPath'])->move('/' . $_POST['newName']);
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

    private function logError(\Exception $e)
    {
        ShowError($e->getMessage());
    }
}
