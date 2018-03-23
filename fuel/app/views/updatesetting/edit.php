<div id="setting-info">
    <h3 id="setting-info-name"><div><?= __p('setting_name') ?>:</div><div><strong><?= $setting->name ?></strong></div></h3>
    <div id="setting-info-date">
        <div>
            <span><?= __c('created_at') ?>: <?= $setting->created_at ?></span>
            <span><?= __c('created_user') ?>: <?= $setting->created_user->pic_name ?></span>
        </div>
        <div>
            <span><?= __c('updated_at') ?>: <?= $setting->updated_at ?></span>
            <span><?= __c('last_updated_user') ?>: <?= $setting->last_updated_user->pic_name ?></span>
        </div>
    </div>
</div>
<?= $this->fast_render('updatesetting/_common');