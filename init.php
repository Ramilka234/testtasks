<?php

use Bitrix\Main\EventManager;
use Only\Site\Handlers\Iblock;

// �������� �� ������� ���������� �������� ���������
EventManager::getInstance()->addEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    [Iblock::class, 'addLog'] 
);

// �������� �� ������� ���������� �������� ���������
EventManager::getInstance()->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    [Iblock::class, 'addLog'] );