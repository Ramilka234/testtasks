<?php

namespace Only\Site\Agents;

use \Bitrix\Iblock;

class Iblock
{
    public static function clearOldLogs()
    {
        // Проверка на загрузку модуля
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return false; // Если не удалось загрузить модуль, прерываем выполнение
        }

        // Получаем ID инфоблока по его символьному коду
        $iblockCode = 'LOGS'; // Символьный код инфоблока
        $iblockType = 'logs_type'; // Тип инфоблока, если используется

        $iblock = Iblock\IblockTable::getList([
            'filter' => ['CODE' => $iblockCode, 'IBLOCK_TYPE_ID' => $iblockType],
            'select' => ['ID'],
        ])->fetch();

        if (!$iblock) {
            return false; // Если инфоблок не найден, прекращаем выполнение
        }

        $logIblockId = $iblock['ID'];

        // Получаем 10 самых новых элементов инфоблока
        $arSelect = ['ID'];
        $arFilter = [
            'IBLOCK_ID' => $logIblockId,
            'ACTIVE' => 'Y',
        ];
        $arOrder = ['ID' => 'DESC']; // Сортируем по ID от большего к меньшему
        $arNavParams = ['nTopCount' => 10]; // Ограничение на 10 записей

        $res = \CIBlockElement::GetList($arOrder, $arFilter, false, $arNavParams, $arSelect);
        $logIdsToKeep = [];
        
        while ($element = $res->Fetch()) {
            $logIdsToKeep[] = $element['ID'];
        }

        // Если есть логи, кроме последних 10, удаляем старые
        if (count($logIdsToKeep) > 0) {
            $arFilterOldLogs = [
                'IBLOCK_ID' => $logIblockId,
                '!ID' => $logIdsToKeep, // Исключаем последние 10
            ];

            $oldLogs = \CIBlockElement::GetList([], $arFilterOldLogs, false, false, ['ID']);
            while ($oldLog = $oldLogs->Fetch()) {
                \CIBlockElement::Delete($oldLog['ID']); // Удаляем старые логи
            }
        }

        return '\\Dev\\Site\\Agents\\Iblock::clearOldLogs();'; // Возвращаем для повторного запуска
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']); // Удаляем старые логи
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}