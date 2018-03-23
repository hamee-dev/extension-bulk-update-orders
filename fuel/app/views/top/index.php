<div class="text-center">
    <?php if ($execution_method === EXECUTION_METHOD_EXTENSION) { ?>
        <a href="/updatesetting/new" class="btn btn-lg btn-ne add-params"><span class="glyphicon glyphicon-file icon"></span><?= __p('button.new_or_execute') ?></a>
    <?php }else{ ?>
        <a href="/updatesetting/new" class="btn btn-lg btn-ne add-params"><span class="glyphicon glyphicon-file icon"></span><?= __p('button.new') ?></a>
        <a href="/tasklist" class="btn btn-lg btn-go-to-list add-params"><span class="glyphicon glyphicon-list-alt icon"></span><?= __p('button.task_list') ?></a>
    <?php } ?>
</div>

<form id="top-execution-form" class="add-params" method="post" action="/top/execution">
</form>

<?php if (count($settings) > 0) { ?>
<div id="setting-list">

    <div id="setting-list-heading" class="clearfix">
        <div id="setting-list-title">
            <h2><span class="glyphicon glyphicon-list icon"></span><?= __p('setting_list') ?></h2>
        </div>

        <div id="setting-list-count">
            <h4><?= __p('setting_list_count') . " {$settings_count} / " . Model_Bulkupdatesetting::SETTING_COUNT_MAX . ' 件' ?></h4>
        </div>
    </div>

    <p id="setting-list-description"><?= __p('setting_list_escriptiond', ['icon' => '<span class="glyphicon glyphicon-pencil blue-icon"></span>']) ?></p>

    <table id="setting-list-table" class="table-bordered">
        <tbody>
        <tr>
            <th><?= __p('table.name') ?></th><th><?= __p('table.update_detail') ?></th>
        </tr>
        <?php foreach ($settings as $setting) { ?>
            <?= $this->fast_render('top/_setting', ['setting' => $setting]); ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<script>
    // 名前空間の設定
    var EXT_BUO = EXT_BUO || {};
    EXT_BUO.TOP = EXT_BUO.TOP || {};
    // 設定名の最大入力文字数
    EXT_BUO.TOP.setting_name_max_length = <?= Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH ?>
</script>
