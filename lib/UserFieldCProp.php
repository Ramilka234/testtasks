
/*
<?php
namespace studyphpdevorg;

class UserFieldCProp
{
    public static function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "HTML_EDITOR_CPROP",
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => "HTML редактор для пользовательских полей",
            "BASE_TYPE" => "string",
            "GetEditFormHTML" => [__CLASS__, "GetEditFormHTML"],
            "GetAdminListViewHTML" => [__CLASS__, "GetAdminListViewHTML"],
        );
    }

    public static function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return \CFileMan::AddHTMLEditorFrame(
            $arHtmlControl["NAME"],
            htmlspecialcharsbx($arHtmlControl["VALUE"]),
            "",
            "",
            array(
                'height' => 200,
                'width' => '100%'
            )
        );
    }

    public static function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        return htmlspecialcharsbx($arHtmlControl["VALUE"]);
    }
}
?>
*/