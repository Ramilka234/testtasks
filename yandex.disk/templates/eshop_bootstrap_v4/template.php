<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<h1>Файлы на Яндекс Диске</h1>

<!-- Форма для загрузки нового файла -->
<form action="<?= POST_FORM_ACTION_URI ?>" method="POST" enctype="multipart/form-data">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="action" value="upload">
    <input type="file" name="file">
    <button type="submit">Загрузить файл</button>
</form>

<h2>Список файлов:</h2>
<ul>
    <?php foreach ($arResult['FILES'] as $file): ?>
        <li>
            <a href="<?= $file['file'] ?>" target="_blank"><?= $file['name'] ?></a>
            <!-- Форма для удаления файла -->
            <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" style="display:inline;">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="filePath" value="<?= $file['path'] ?>">
                <button type="submit">Удалить</button>
            </form>
            <!-- Форма для изменения имени файла -->
            <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" style="display:inline;">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="oldPath" value="<?= $file['path'] ?>">
                <input type="text" name="newName" value="<?= basename($file['path']) ?>">
                <button type="submit">Изменить</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>