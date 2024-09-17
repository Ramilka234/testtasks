<?php
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumeration;

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}
\Bitrix\Main\Loader::includeModule('iblock');

$iblockId = 4; 

// ������� ��� ��������� ID �������� ������, ���� ��� ��� � ��������� �����
function getListValueId($iblockId, $propertyCode, $value) {
    $propertyEnums = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $iblockId, "CODE" => $propertyCode, "VALUE" => $value]);
    if ($enumFields = $propertyEnums->GetNext()) {
        return $enumFields['ID']; // ���������� ������������ ID
    } else {
        // ��������� ����� ��������, ���� ��� �� ����������
        $propertyEnum = new CIBlockPropertyEnum;
        $newEnumId = $propertyEnum->Add([
            'PROPERTY_ID' => getPropertyIdByCode($iblockId, $propertyCode), // ID ��������
            'VALUE' => $value, // ��������
        ]);
        return $newEnumId; // ���������� ID ������ ��������
    }
}

// ������� ��� ��������� ID �������� �� ��� ����
function getPropertyIdByCode($iblockId, $propertyCode) {
    $propertyRes = CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId, "CODE" => $propertyCode]);
    if ($propertyFields = $propertyRes->GetNext()) {
        return $propertyFields['ID'];
    }
    return false;
}

if (($handle = fopen($_SERVER["DOCUMENT_ROOT"]."/upload/vacancy.csv", "r")) !== FALSE) {
    $el = new CIBlockElement;

    fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $activityId = getListValueId($iblockId, 'ACTIVITY', $data[9]); 
        $fieldId = getListValueId($iblockId, 'FIELD', $data[11]); 
        $officeId = getListValueId($iblockId, 'OFFICE', $data[1]); 
        $locationId = getListValueId($iblockId, 'LOCATION', $data[2]); 
        $typeId = getListValueId($iblockId, 'TYPE', $data[8]);
        $scheduleId = getListValueId($iblockId, 'SCHEDULE', $data[10]); 
        $salaryTypeId = getListValueId($iblockId, 'SALARY_TYPE', ''); 

        // ��������� ������ ��� ������ �������� ���������
        $fields = [
            "IBLOCK_ID" => $iblockId,
            "NAME" => $data[3],  
            "PROPERTY_VALUES" => [
                'ACTIVITY' => $activityId, 
                'FIELD' => $fieldId, 
                'OFFICE' => $officeId, 
                'EMAIL' => $data[12], 
                'LOCATION' => $locationId,
                'TYPE' => $typeId, 
                'SALARY_TYPE' => $salaryTypeId, 
                'SALARY_VALUE' => $data[7], 
                'REQUIRE' => $data[4], 
                'DUTY' => $data[5], 
                'CONDITIONS' => $data[6], 
                'SCHEDULE' => $scheduleId, 
            ],
            "ACTIVE" => "Y",
        ];
	

        if ($elementId = $el->Add($fields)) {
            echo "������� ������� �������� � ID: " . $elementId . "<br>";
        } else {
            echo "������: " . $el->LAST_ERROR . "<br>";
        }
    }

    fclose($handle);
} else {
    echo "�� ������� ������� ���� CSV.";
}
?>
