<!DOCTYPE html>
<html>
<head>
    <?php // Google Tag Manager ?>
    <?= $this->fast_render('gtm/_head') ?>

    <meta charset="utf-8">
    <title><?= $title ?></title>

    <?= \Asset::css([
        'bootstrap.min.css',
        'bootstrap-datepicker.min.css',
        'common.css',
    ]) ?>
    <?= \Asset::render('css') ?>
</head>
<body>
    <?php // Google Tag Manager (noscript) ?>
    <?= $this->fast_render('gtm/_body') ?>

    <?= Form::hidden(PARAM_EXECUTION_METHOD, $execution_method, ['id' => 'add-params-1']);?>
    <?= Form::hidden(PARAM_EXTENSION_EXECUTION_ID, !empty($extension_execution_id) ? $extension_execution_id : '', ['id' => 'add-params-2']);?>
    <?= Form::hidden($token_key, $token);?>
    <?= Form::hidden('token_name', $token_key);?>
    <div id="container">
        <div id="message">
            <?php if (isset($success_messages)) { ?>
                <div class="alert alert-success" role="alert">
                    <?php foreach ($success_messages as $success_message) { ?>
                        <div><?= $success_message ?></div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (isset($error_messages)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($error_messages as $error_message) { ?>
                        <div><?= $error_message ?></div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div id="header">
            <div id="app-icon"><?= \Asset::img($app_icon) ?></div>
            <div id="page-title"><h1><?= $title; ?></h1></div>
            <div id="page-description"><p><?= $description ?></p></div>
        </div>
        <div id="content">
            <?= $content ?>
        </div>
        <?php // ボタンクリック時に画面全体を覆って他のボタンをクリックさせないようにするためのビュー ?>
        <div id="display-block-view"></div>
    </div>
    <?php if (isset($footer_path)) { ?>
        <?php
        /**
         * フッターの表示
         */
        ?>
        <?= $this->fast_render('footer/' . $footer_path) ?>
    <?php } ?>
    <?= $this->fast_render('common/_modal') ?>
    <?= \Asset::js([
        'jquery-3.3.1.min.js',
        'bootstrap.min.js',
        'bootstrap-maxlength.js',
        'bootstrap-datepicker.min.js',
        'bootstrap-datepicker.ja.min.js',
        'common.js'
    ]); ?>
    <?= \Asset::render('js') ?>
</body>
</html>
