<?

class dev_site extends CModule
{
    const MODULE_ID = 'dev.site';

    public $MODULE_ID = 'dev.site';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = '������������� ������';
    public $PARTNER_NAME = 'dev';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = [])
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();

 	\CAgent::AddAgent(
    	"\\Dev\\Site\\Agents\\Iblock::clearOldLogs();", 
    	"dev.site",
        100, 
    	"Y", 
   	    3600, 
    	"", 
    	"N", 
    	"", 
    	false
	);
	if ($agentId === false) {
    global $APPLICATION;
    $errorMessage = $APPLICATION->GetException();
    if ($errorMessage) {
        AddMessage2Log('������ ��� ���������� ������: ' . $errorMessage->GetString(), "dev.site");
    } else {
        AddMessage2Log('����������� ������ ��� ���������� ������', "dev.site");
    }
	} else {
    	AddMessage2Log('����� ������� ��������. ID ������: ' . $agentId, "dev.site");
	} 	
}

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
    }
}