<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialcharsbx($arResult["FORM_TITLE"]) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="shortcut icon" href="/images/favicon.604825ed.ico" type="image/x-icon">
    <link href="/css/common.css" rel="stylesheet">
</head>
<body>
    <div class="contact-form">
        <div class="contact-form__head">
           <div class="contact-form__head-title">Связаться</div>
        <div class="contact-form__head-text">Наши сотрудники помогут выполнить подбор услуги и расчет цены с учетом ваших требований</div>        </div>
        <form class="contact-form__form" action="<?= htmlspecialcharsbx($APPLICATION->GetCurPage()) ?>" method="POST">
            <?= $arResult["FORM_HEADER"] ?>
            <?= bitrix_sessid_post() ?>

            <?php if ($arResult["isFormErrors"] == "Y"): ?>
                <div class="form-errors">
                    <?= htmlspecialcharsbx($arResult["FORM_ERRORS_TEXT"]) ?>
                </div>
            <?php endif; ?>

            <div class="contact-form__form-inputs">
                <?php foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion): ?>
                    <?php if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] != 'hidden'): ?>
                        <div class="input contact-form__input">
                            <label class="input__label" for="<?= htmlspecialcharsbx($arQuestion["STRUCTURE"][0]["ID"]) ?>">
                                <div class="input__label-text"><?= htmlspecialcharsbx($arQuestion["CAPTION"]) ?><?= $arQuestion["REQUIRED"] == "Y" ? $arResult["REQUIRED_SIGN"] : "" ?></div>
                                <?= $arQuestion["HTML_CODE"] ?>
                            </label>
                        </div>
                    <?php else: ?>
                        <?= $arQuestion["HTML_CODE"] ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($arResult["isUseCaptcha"] == "Y"): ?>
                <div class="captcha">
                    <label>
                        <div class="captcha__label"><?= GetMessage("FORM_CAPTCHA_FIELD_TITLE") ?><?= $arResult["REQUIRED_SIGN"] ?></div>
                        <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" />
                    </label>
                    <div class="captcha__image">
                        <input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx($arResult["CAPTCHACode"]) ?>" />
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($arResult["CAPTCHACode"]) ?>" width="180" height="40" />
                    </div>
                </div>
            <?php endif; ?>

            <div class="contact-form__bottom">
                <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных данных&raquo;.</div>
                <button class="form-button contact-form__bottom-button" type="submit" name="web_form_submit" value="<?= htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]) ?>">
                    <div class="form-button__title"><?= htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]) ?></div>
                </button>
                <?php if ($arResult["F_RIGHT"] >= 15): ?>
                    <input type="hidden" name="web_form_apply" value="Y" />
                    <input type="submit" name="web_form_apply" value="<?= GetMessage("FORM_APPLY") ?>" />
                <?php endif; ?>
                <input type="reset" value="<?= GetMessage("FORM_RESET") ?>" />
            </div>
            <?= $arResult["FORM_FOOTER"] ?>
        </form>
    </div>
</body>
</html>