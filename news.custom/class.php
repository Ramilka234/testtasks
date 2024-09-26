<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

class NewsCustomComponent extends CBitrixComponent
{
    // Метод для получения всех инфоблоков по типу с фильтрацией
    protected function getIblockElementsByType($type, $filter = [])
    {
        if (!Loader::includeModule("iblock")) {
            ShowError('Модуль инфоблоков не установлен.');
            return [];
        }

        $arFilter = ['TYPE' => $type, 'ACTIVE' => 'Y'];
        $arResult = [];

        // Получаем список инфоблоков
        $rsIBlocks = CIBlock::GetList([], $arFilter);
        while ($iblock = $rsIBlocks->Fetch()) {
            // Получаем элементы каждого инфоблока с учетом фильтрации, сохраняем их в массив $arResult под ключом, соответствующим ID инфоблока
            $arResult[$iblock['ID']] = $this->getElementsByIblockId($iblock['ID'], $filter);
        }

        return $arResult;
    }

    // Метод для получения элементов по ID инфоблока с фильтрацией
    protected function getElementsByIblockId($iblockId, $filter = [])
    {
        $arFilter = array_merge([
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y'
        ], $filter); // Добавляем фильтр

        $arElements = [];

        $rsElements = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'IBLOCK_ID', 'DETAIL_PAGE_URL', 'PREVIEW_TEXT', 'PREVIEW_PICTURE']);
        while ($obElement = $rsElements->GetNextElement()) {
            $elementFields = $obElement->GetFields();
            $elementFields['PREVIEW_PICTURE_SRC'] = CFile::GetPath($elementFields['PREVIEW_PICTURE']);
            $arElements[] = $elementFields;
        }

        return $arElements;
    }

    // Метод для проверки параметров
    protected function validateParams()
    {
        if (empty($this->arParams['IBLOCK_TYPE'])) {
            ShowError('Не указан тип инфоблока.');
            return false;
        }

        return true;
    }

    // Основной метод компонента
    public function executeComponent()
    {
        if (!$this->validateParams()) {
            return;
        }

        $iblockType = $this->arParams['IBLOCK_TYPE'];
        $iblockId = $this->arParams['IBLOCK_ID'];
        $filter = $this->arParams['FILTER'] ?? []; // Получаем фильтр

        // Получаем данные по инфоблокам или по конкретному ID
        if ($iblockId) {
            $this->arResult['ITEMS'][$iblockId] = $this->getElementsByIblockId($iblockId, $filter);
        } else {
            $this->arResult['ITEMS'] = $this->getIblockElementsByType($iblockType, $filter);
        }

        // Если не удалось получить данные, показываем ошибку
        if (empty($this->arResult['ITEMS'])) {
            ShowError('Элементы не найдены.');
        } else {
            if ($this->arParams["SET_TITLE"] == "Y") {
                global $APPLICATION;
                $APPLICATION->SetTitle($this->arResult["TITLE"]);
            }

            // Включаем шаблон компонента
            $this->includeComponentTemplate();
        }

        // Устанавливаем статус 404, если указано в параметрах
        if ($this->arParams["SET_STATUS_404"] == "Y" && empty($this->arResult['ITEMS'])) {
            \Bitrix\Iblock\Component\Tools::process404(
                "",
                true,
                $this->arParams["SET_STATUS_404"] === "Y",
                $this->arParams["SHOW_404"] === "Y",
                $this->arParams["FILE_404"]
            );
        }
    }
}