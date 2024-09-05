<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="contact-form">
    <div class="contact-form__head">
        <div class="contact-form__head-title">Связаться</div>
        <div class="contact-form__head-text">Наши сотрудники помогут выполнить подбор услуги и расчет цены с учетом ваших требований</div>
    </div>
    <form class="contact-form__form" action="<?=POST_FORM_ACTION_URI?>" method="POST">
        <?=bitrix_sessid_post()?>
        
        <?php if (!empty($arResult["ERRORS"])): ?>
            <div class="form-errors">
                <?php foreach ($arResult["ERRORS"] as $error): ?>
                    <p><?= htmlspecialcharsbx($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="contact-form__form-inputs">
            <div class="input contact-form__input">
                <label class="input__label" for="medicine_name">
                    <div class="input__label-text">Ваше имя*</div>
                    <input class="input__input" type="text" id="medicine_name" name="form_text_1" value="<?=htmlspecialcharsbx($arResult["VALUES"]["form_text_1"])?>" required>
                    <?php if (isset($arResult["FORM_ERRORS"]["form_text_1"])): ?>
                        <div class="input__notification">Поле должно содержать не менее 3-х символов</div>
                    <?php endif; ?>
                </label>
            </div>
            <div class="input contact-form__input">
                <label class="input__label" for="medicine_company">
                    <div class="input__label-text">Компания/Должность*</div>
                    <input class="input__input" type="text" id="medicine_company" name="form_text_2" value="<?=htmlspecialcharsbx($arResult["VALUES"]["form_text_2"])?>" required>
                    <?php if (isset($arResult["FORM_ERRORS"]["form_text_2"])): ?>
                        <div class="input__notification">Поле должно содержать не менее 3-х символов</div>
                    <?php endif; ?>
                </label>
            </div>
            <div class="input contact-form__input">
                <label class="input__label" for="medicine_email">
                    <div class="input__label-text">Email*</div>
                    <input class="input__input" type="email" id="medicine_email" name="form_email_3" value="<?=htmlspecialcharsbx($arResult["VALUES"]["form_email_3"])?>" required>
                    <?php if (isset($arResult["FORM_ERRORS"]["form_email_3"])): ?>
                        <div class="input__notification">Неверный формат почты</div>
                    <?php endif; ?>
                </label>
            </div>
            <div class="input contact-form__input">
                <label class="input__label" for="medicine_phone">
                    <div class="input__label-text">Номер телефона*</div>
                    <input class="input__input" type="tel" id="medicine_phone" name="form_text_4" value="<?=htmlspecialcharsbx($arResult["VALUES"]["form_text_4"])?>" required>
                </label>
            </div>
        </div>
        
        <div class="contact-form__form-message">
            <div class="input">
                <label class="input__label" for="medicine_message">
                    <div class="input__label-text">Сообщение</div>
                    <textarea class="input__input" id="medicine_message" name="form_textarea_5"><?=htmlspecialcharsbx($arResult["VALUES"]["form_textarea_5"])?></textarea>
                </label>
            </div>
        </div>
        
        <div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы подтверждаете, что ознакомлены, полностью согласны и принимаете условия &laquo;Согласия на обработку персональных данных&raquo;.</div>
            <button class="form-button contact-form__bottom-button" type="submit" name="web_form_submit" value="Оставить заявку">
                <div class="form-button__title">Оставить заявку</div>
            </button>
        </div>
    </form>
</div>