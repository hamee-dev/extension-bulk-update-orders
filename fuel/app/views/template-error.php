<!DOCTYPE html>
<html>
<head>
    <?php // Google Tag Manager ?>
    <?= $this->fast_render('gtm/_head') ?>

    <meta charset="utf-8">
    <title><?= $title ?></title>

    <?= \Asset::css(array(
        'bootstrap.css',
        'common.css',
        'error.css',
    )) ?>

</head>
<body>
    <?php // Google Tag Manager (noscript) ?>
    <?= $this->fast_render('gtm/_body') ?>

    <div id="container">
        <div id="error-content">
            <div class="alert alert-danger" role="alert">
                <div id="error-title"><span class="glyphicon glyphicon-remove"></span><?= $title ?></div>
                <?php if (isset($error_messages)) { ?>
                    <div id="error-description">
                        <?php foreach ($error_messages as $error_message) { ?>
                            <div><?= $error_message ?></div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div id="error-description">
                        <div><?= __em('common_error_description') ?></div>
                    </div>
                <?php } ?>
            </div>
        <div>
    </div>
</body>
</html>
