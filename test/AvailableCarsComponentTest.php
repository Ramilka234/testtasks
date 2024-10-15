<?php

use PHPUnit\Framework\TestCase;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Mockery as m;

class AvailableCarsComponentTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testExecuteComponentWithoutTime()
    {
        // Создаем mock для компонента
        $component = m::mock('AvailableCarsComponent[showError, includeComponentTemplate]');
        $component->shouldReceive('showError')->once()->with('Пожалуйста, укажите время начала и окончания поездки.');

        // Устанавливаем параметры и вызываем метод executeComponent
        $component->startTime = null;
        $component->endTime = null;
        $component->executeComponent();
    }

    public function testExecuteComponentWithTimeButNoModules()
    {
        // Мокируем загрузчик модулей, чтобы симулировать неустановленные модули
        Loader::shouldReceive('includeModule')->with('highloadblock')->andReturn(false);
        Loader::shouldReceive('includeModule')->with('iblock')->andReturn(true);

        $component = m::mock('AvailableCarsComponent[showError, includeComponentTemplate]');
        $component->shouldReceive('showError')->once()->with('Модули highloadblock или iblock не установлены.');

        $component->startTime = '2023-10-01 10:00:00';
        $component->endTime = '2023-10-01 18:00:00';
        $component->executeComponent();
    }

    public function testGetBusyCars()
    {
        // Мокируем запрос к Bitrix для получения списка поездок
        $tripsMock = m::mock();
        $tripsMock->shouldReceive('fetch')
            ->andReturn(['PROPERTY_CAR_ID' => 1])
            ->once();

        $component = m::mock('AvailableCarsComponent[SetBusyCars]');
        $component->shouldReceive('SetBusyCars')->andReturn([1]);

        $busyCars = $component->getBusyCars();
        $this->assertEquals([1], $busyCars);
    }

    public function testSetDrivers()
    {
        $availableCars = [
            [
                'UF_DRIVER' => 1,
                'UF_MODEL_NAME' => 'Car Model 1',
                'UF_COMFORT_CATEGORY' => '1'
            ]
        ];

        $component = m::mock('AvailableCarsComponent[getDriverInfo]');
        $component->shouldReceive('getDriverInfo')->with(1)->andReturn('Driver Name');
        
        $component->SetDrivers($availableCars);

        $this->assertEquals('Driver Name', $availableCars[0]['DRIVER_INFO']);
    }

    public function testGetUserComfortCategory()
    {
        // Мокируем HL блок для комфорт-категории
        $hlblockMock = m::mock();
        $hlblockMock->shouldReceive('getList')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('fetch')
            ->andReturn(['UF_COMFORT_CATEGORY' => '1'])
            ->once();

        $component = m::mock('AvailableCarsComponent[getHLBlockByCode]');
        $component->shouldReceive('getHLBlockByCode')->with('comfort_category')->andReturn($hlblockMock);

        $comfortCategory = $component->getUserComfortCategory();
        $this->assertEquals('1', $comfortCategory);
    }

    public function testGetDriverInfo()
    {
        // Мокируем HL блок для получения информации о водителе
        $hlblockMock = m::mock();
        $hlblockMock->shouldReceive('getList')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('fetch')
            ->andReturn(['UF_FULLNAME' => 'John Doe'])
            ->once();

        $component = m::mock('AvailableCarsComponent[getHLBlockByCode]');
        $component->shouldReceive('getHLBlockByCode')->with('drivers')->andReturn($hlblockMock);

        $driverInfo = $component->getDriverInfo(1);
        $this->assertEquals('John Doe', $driverInfo);
    }
}
