<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link rel="stylesheet" href="<?= $templateFolder ?>/css/common.css">
</head>

<div class="article-card">
    <div class="article-card__title"><?= $arResult["NAME"] ?></div>
    <div class="article-card__date"><?= $arResult["DISPLAY_ACTIVE_FROM"] ?></div>
    <div class="article-card__content">
        <div class="article-card__image sticky">
            <img src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" alt="<?= $arResult["NAME"] ?>" />
        </div>
        <div class="article-card__text"><?= $arResult["DETAIL_TEXT"] ?></div>
    </div>
</div>