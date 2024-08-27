<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link rel="stylesheet" href="<?= $templateFolder ?>/css/common.css">
</head>
<body>
<div id="barba-wrapper">
    <div class="article-list">
        <?php foreach ($arResult["ITEMS"] as $arItem): ?>
            <a class="article-item article-list__item" href="<?= $arItem["DETAIL_PAGE_URL"] ?>" data-anim="anim-3">
                <div class="article-item__background">
                    <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= htmlspecialchars($arItem["NAME"]) ?>">
                </div>
                <div class="article-item__wrapper">
                    <div class="article-item__title"><?= htmlspecialchars($arItem["NAME"]) ?></div>
                    <div class="article-item__content"><?= htmlspecialchars($arItem["PREVIEW_TEXT"]) ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>