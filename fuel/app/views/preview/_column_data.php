<?php //「さらに表示」の時にcloneする用のダミー要素  ?>
<table class='display-none' id='column_data_dummy'>
     <tbody>
        <?= $this->fast_render('preview/_column_data_tr', ['receive_order_id' => '', 'display_value' => [], 'physical_name' => ''], false) ?>
     </tbody>
</table>


<?php foreach ($receive_order_columns as $receive_order_column) { ?>
    <?php
        $logical_name = $receive_order_column['logical_name'];
        $physical_name = $receive_order_column['physical_name'];
    ?>
    <div class="update-result-list">
        <div><strong class="lead"><?= $logical_name ?></strong></div>
        <table class="update-result-table table-bordered <?= $physical_name ?>">
            <thead>
            <tr>
                <th class='receive_order_id_column'>伝票番号</th>
                <th class='before_value_column'>実行前</th>
                <th class='after_value_column'>実行後</th>
                <th class='exclude_column' colspan="2">除外</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($display_values as $receive_order_id => $display_value) { ?>
                <?= $this->fast_render('preview/_column_data_tr', ['receive_order_id' => $receive_order_id, 'display_value' => $display_value, 'physical_name' => $physical_name], false) ?>
            <?php } ?>

            <?php // 「さらに表示」の時に要素を追加する目印となる部分 ?>
            <tr id="<?= $physical_name ?>_last_tr" class='display-none'></tr>
            </tbody>
        </table>
    </div>

    <?php // 「さらに表示」ボタンの配置 ?>
    <?= $this->fast_render('preview/_more_display') ?>
<?php } ?>