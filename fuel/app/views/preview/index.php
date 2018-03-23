<?php //高度なオプションの警告表示 ?>
<?php if (!empty($warning_messages)) { ?>
    <div class="alert alert-warning" role="alert">
        <?php foreach ($warning_messages as $warning_message) { ?>
            <div><?= $warning_message ?></div>
        <?php } ?>
    </div>
<?php } ?>

<!-- タブ・メニュー -->
<ul class="nav nav-tabs">
    <li class="active" id='column-list-tab'><a href="#column-list" data-toggle="tab">項目ごと</a></li>
    <li id='order-list-tab'><a href="#order-list" data-toggle="tab">伝票ごと</a></li>
</ul>

<?= Form::hidden(BULK_UPDATE_SETTING_ID, ${BULK_UPDATE_SETTING_ID}) ?>
<?= Form::hidden(TRANSITION_PATH, ${TRANSITION_PATH}) ?>

<!-- タブ内容 -->
<div class="tab-content">
    <?php // 項目ごと ?>
    <div class="tab-pane active" id="column-list">
        <div class='description_exclude'><?= __p('description.exclude.common', ['icon' => '<span class="glyphicon glyphicon-remove-circle"></span>']) ?></div>
        <div class='description_exclude_each_column'><?= __p('description.exclude.each_column', ['icon' => '<span class="glyphicon glyphicon-remove-circle"></span>']) ?></div>
        <?= $this->fast_render('preview/_column_data') ?>
    </div>

    <?php // 伝票ごと ?>
    <div class="tab-pane" id="order-list">
        <div class='description_exclude'><?= __p('description.exclude.common', ['icon' => '<span class="glyphicon glyphicon-remove-circle"></span>']) ?></div>
        <?= $this->fast_render('preview/_order_data') ?>
    </div>
</div>

<script>
    // 名前空間の設定
    var EXT_BUO = EXT_BUO || {};
    EXT_BUO.PREVIEW = EXT_BUO.PREVIEW || {};

    // js側で「さらに表示」を行う際に必要な情報を変数として持つ
    EXT_BUO.PREVIEW.display_values_json = <?= json_encode($display_values_other) ?>;
    EXT_BUO.PREVIEW.receive_order_columns_json = <?= json_encode($receive_order_columns) ?>;
</script>