<?php

namespace Dev\Site\Handlers;

use \Bitrix\Main\Loader;
use \CIBlockSection;
use \CIBlockElement;

class Iblock
{
    public static function addLog($arFields)
    {
         if (!Loader::includeModule('iblock')) {
            return;
        }

        $logIblockCode = 'LOG';
        $logIblockType = 'logs_type'; // Тип инфоблока для логов
        $logIblock = Iblock\IblockTable::getList([
            'filter' => ['CODE' => $logIblockCode, 'IBLOCK_TYPE_ID' => $logIblockType],
            'select' => ['ID']
        ])->fetch();

        if (!$logIblock) {
            return; // Если инфоблок логов не найден
        }

        $logIblockId = $logIblock['ID'];

        // Проверяем, не является ли измененный инфоблок логом
        if ($arFields['IBLOCK_ID'] == $logIblockId) {
            return;
        }

        // Получаем информацию о логируемом инфоблоке
        $iblockInfo = Iblock\IblockTable::getList([
            'filter' => ['ID' => $arFields['IBLOCK_ID']],
            'select' => ['NAME', 'CODE']
        ])->fetch();

        if (!$iblockInfo) {
            return;
        }

        // Ищем раздел в инфоблоке логов по коду и имени инфоблока, если нет — создаем
        $sectionId = self::findOrCreateLogSection($logIblockId, $iblockInfo['NAME'], $iblockInfo['CODE']);

        // Формируем строку описания для анонса
        $elementName = $arFields['NAME'];
        $sectionNamePath = self::getSectionNamePath($arFields['IBLOCK_SECTION_ID']);
        $announcement = $iblockInfo['NAME'] . ' -> ' . $sectionNamePath . ' -> ' . $elementName;

        // Создаем или обновляем элемент в инфоблоке логов
        $el = new \CIBlockElement;
        $logFields = [
            'IBLOCK_ID' => $logIblockId,
            'IBLOCK_SECTION_ID' => $sectionId,
            'NAME' => $arFields['ID'], // Имя — это ID логируемого элемента
            'ACTIVE_FROM' => date('d.m.Y H:i:s'), // Дата активности — дата создания/изменения
            'PREVIEW_TEXT' => $announcement // Описание для анонса
        ];

        
    }

    // Функция для поиска раздела по имени и коду, если не найден — создается новый раздел
    private static function findOrCreateLogSection($logIblockId, $sectionName, $sectionCode)
    {
        $section = \CIBlockSection::GetList(
            ['ID' => 'ASC'],
            ['IBLOCK_ID' => $logIblockId, 'NAME' => $sectionName, 'CODE' => $sectionCode]
        )->Fetch();

        if ($section) {
            return $section['ID'];
        } else {
            $sectionObject = new \CIBlockSection;
            $sectionFields = [
                'IBLOCK_ID' => $logIblockId,
                'NAME' => $sectionName,
                'CODE' => $sectionCode
            ];

            return $sectionObject->Add($sectionFields);
        }
    }

    // Рекурсивный поиск пути имен разделов от родителя к ребенку
    private static function getSectionNamePath($sectionId)
    {
        $sectionPath = [];
        while ($sectionId) {
            $section = \CIBlockSection::GetByID($sectionId)->Fetch();
            if ($section) {
                $sectionPath[] = $section['NAME'];
                $sectionId = $section['IBLOCK_SECTION_ID']; // Берем родителя
            } else {
                break;
            }
        }

        return implode(' -> ', array_reverse($sectionPath));
    }

    // Подписываемся на события добавления и изменения элементов инфоблока
    public static function registerHandlers()
    {
        \AddEventHandler('iblock', 'OnAfterIBlockElementAdd', ['\Dev\Site\Handlers\Iblock', 'addLog']);
        \AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', ['\Dev\Site\Handlers\Iblock', 'addLog']);
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;
        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));
        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];
        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }
        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality);
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Dev\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Dev\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');
            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();
                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ], false, false, ['ID'])
                                ->Fetch();
                            if ($arOtherElement) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arOtherElement['ID'], $OTHER_IBLOCK_ID, $arFiles, 'FILE');
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }
                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => \Dev\Site\Helpers\IBlock::getIblockID('PRODUCTS', 'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'),
                                    'ID' => $elementIds
                                ], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
                            while ($arElement = $rsElement->Fetch()) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arElement['ID'], $arElement['IBLOCK_ID'], $arFiles, 'FILE');
                            }
                        }
                    }
                }
            }
        }
    }

}
