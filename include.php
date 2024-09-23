<?

/**
 * РђРІС‚РѕР·Р°РіСЂСѓР·РєР° РєР»Р°СЃСЃРѕРІ РёР· РїР°РїРєРё lib/
 * PSR-0
 * @param $className
 */
function dev_site_autoload($className)
{
    $sModuleId = basename(dirname(__FILE__));
    $className = ltrim($className, '\\');
    $arParts = explode('\\', $className);

    $sModuleCheck = strtolower($arParts[0] . '.' . $arParts[1]);

    if ($sModuleCheck != $sModuleId)
        return;

    $arParts = array_splice($arParts, 2);
    if (!empty($arParts)) {
        $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arParts) . '.php';
        if (file_exists($fileName))
            require_once $fileName;
    }
}

spl_autoload_register('dev_site_autoload');


if (\Bitrix\Main\Loader::includeModule('dev.site') && \Bitrix\Main\Loader::includeModule('iblock')) {

    // Проверим, существует ли агент с таким именем
    $agentName = "\\Only\\Site\\Agents\\Iblock::clearOldLogs();";
    $res = \CAgent::GetList([], ['NAME' => $agentName]);

    // Если агент не зарегистрирован, то добавим его
    if (!$res->Fetch()) {
        \CAgent::AddAgent(
            $agentName,
            "dev.site",
            "N",
            3600,   // Интервал запуска 1 час
            "",
            "Y",
            "",
            false
        );
    }
}