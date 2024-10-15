<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class AvailableCarsComponent extends CBitrixComponent
{
    protected $userId;
    protected $startTime;
    protected $endTime;

    public function onPrepareComponentParams($arParams)
    {
        $this->userId = $arParams['USER_ID'] ?: $GLOBALS['USER']->GetID();
        $this->startTime = $_GET['start_time'];
        $this->endTime = $_GET['end_time'];

        return parent::onPrepareComponentParams($arParams);
    }
    
    public function executeComponent()
    {
        if (empty($this->startTime) || empty($this->endTime)) {
            ShowError("Пожалуйста, укажите время начала и окончания поездки.");
            return;
        }

        if (!Loader::includeModule('highloadblock') || !Loader::includeModule('iblock')) {
            ShowError("Модули highloadblock или iblock не установлены.");
            return;
        }

        $comfortCategory = $this->getUserComfortCategory();
        $busyCars = $this->getBusyCars();
        $availableCars = $this->getAvailableCars($comfortCategory, $busyCars);
        $this->SetDrivers($availableCars)

        $this->arResult['CARS'] = $availableCars;
        $this->includeComponentTemplate();
    }

    public function SetDrivers(&$availableCars){
        foreach ($availableCars as &$car) {
            $car['DRIVER_INFO'] = $this->getDriverInfo($car['UF_DRIVER']);
        }        
    }


    protected function getUserComfortCategory()
    {
        // Получение категории комфорта сотрудника через highload-блок
        $hlblockId = $this->getHLBlockByCode('comfort_category');
        $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $employeePosition = $entityDataClass::getList([
            'filter' => ['UF_USER_ID' => $this->userId],
            'select' => ['UF_COMFORT_CATEGORY']
        ])->fetch();

        return $employeePosition['UF_COMFORT_CATEGORY'];
    }

    protected function getBusyCars()
    {
        // Получаем список занятых автомобилей на запрашиваемый интервал времени
        $trips = \Bitrix\Iblock\ElementTable::getList([
            'filter' => [
                '<=PROPERTY_START_TIME' => $this->endTime,
                '>=PROPERTY_END_TIME' => $this->startTime,
            ],
            'select' => ['ID', 'PROPERTY_CAR_ID']
        ]);
    
        // Возвращаем список занятых автомобилей через SetBusyCars
        return $this->SetBusyCars($trips);
    }
    
    public function SetBusyCars($trips)
    {
        $busyCars = [];
        while ($trip = $trips->fetch()) {
            $busyCars[] = $trip['PROPERTY_CAR_ID'];
        }
        return $busyCars;    
    }

    protected function getAvailableCars($comfortCategory, $busyCars)
    {

        $cars = \Bitrix\Iblock\ElementTable::getList([
            'filter' => [
                'PROPERTY_COMFORT_CATEGORY' => $comfortCategory,
                '!ID' => $busyCars  // Исключаем занятые автомобили
            ],
            'select' => ['ID', 'NAME', 'PROPERTY_COMFORT_CATEGORY', 'PROPERTY_DRIVER']
        ]);

        return $this->SetAvailableCars($cars);
    }

    public function SetAvailableCars($cars)
    {
        $availableCars = [];
        while ($car = $cars->fetch()) {
            $availableCars[] = [
                'ID' => $car['ID'],
                'UF_MODEL_NAME' => $car['NAME'],
                'UF_COMFORT_CATEGORY' => $car['PROPERTY_COMFORT_CATEGORY'],
                'UF_DRIVER' => $car['PROPERTY_DRIVER']
            ];
        }

        return $availableCars;   
    }

    protected function getDriverInfo($driverId)
    {
        // Получение информации о водителе из highload-блока drivers
        $hlblockId = $this->getHLBlockByCode('drivers');
        $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $driver = $entityDataClass::getList([
            'filter' => ['ID' => $driverId],
            'select' => ['UF_FULLNAME']
        ])->fetch();

        return $driver ? $driver['UF_FULLNAME'] : null;
    }

    protected function getHLBlockByCode($hlblockCode)
    {
        // Получаем ID хайлоад-блока по его символьному коду 
        $hlblock = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => $hlblockCode], 
            'select' => ['ID']
        ])->fetch();

        return $hlblock ? $hlblock['ID'] : null;
    }
}
