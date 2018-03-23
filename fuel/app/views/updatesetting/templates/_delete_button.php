<?php
/**
 * 削除ボタン
 */
$is_hidden = false;
if (isset($setting->bulk_update_columns)) {
    // １つしか設定してない場合は削除ボタンを非表示にする
    $count = 0;
    foreach ($setting->bulk_update_columns as $bulk_update_column) {
        // 発送方法区分別タイプは無視する（発送方法で1カウントとする）
        if ($bulk_update_column->receive_order_column->column_type->is_master() &&
            Utility_Master::is_forwarding_agent($bulk_update_column->receive_order_column->master_name)) {
            continue;
        }
        $count++;
    }
    if ($count <= 1){
        $is_hidden = true;
    }
}
?>
<td class="setting-list-button">
    <a href="javascript:void(0);" class="btn btn-danger"<?php if ($is_hidden) {?> style="visibility: hidden" <?php } ?>>
        <span class="glyphicon glyphicon-trash"></span><?= __c('button.delete') ?>
    </a>
</td>