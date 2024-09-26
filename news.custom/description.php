<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    'NAME' => 'News Custom Component',
    'DESCRIPTION' => 'Компонент для вывода элементов инфоблоков по типу или ID',
    'PATH' => [
        'ID' => 'custom',
        'NAME' => 'Custom Components',
    ],
];

// Получение списка типов инфоблоков
$arIBlockTypes = [];
if (\Bitrix\Main\Loader::includeModule('iblock')) {
    $dbIBlockType = \CIBlockType::GetList([], ['ACTIVE' => 'Y']);
    while ($arType = $dbIBlockType->Fetch()) {
        $arIBlockTypes[$arType['ID']] = '[' . $arType['ID'] . '] ' . $arType['NAME'];
    }

    // Получение списка инфоблоков
    $arIBlocks = [];
    $dbIBlocks = \CIBlock::GetList([], ['ACTIVE' => 'Y']);
    while ($arIBlock = $dbIBlocks->Fetch()) {
        $arIBlocks[$arIBlock['ID']] = '[' . $arIBlock['ID'] . '] ' . $arIBlock['NAME'];
    }
}

$arComponentParameters = [
    'PARAMETERS' => [
        'IBLOCK_TYPE' => [
            'PARENT' => 'BASE',
            'NAME' => 'Тип инфоблока',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlockTypes,  // Добавляем список типов инфоблоков
            'REFRESH' => 'Y', // При изменении этого параметра форма будет перезагружаться
        ],
        'IBLOCK_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'ID инфоблока',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,  // Добавляем список инфоблоков
            'DEFAULT' => '',
            'ADDITIONAL_VALUES' => 'Y',  // Позволяет вручную вводить значение
        ],
        'FILTER' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Фильтр по свойству',
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'REFRESH' => 'N',
        ],
        'SET_TITLE' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Устанавливать заголовок страницы',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ],
        'SET_STATUS_404' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Устанавливать статус 404',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ],
    ],
];
?>
