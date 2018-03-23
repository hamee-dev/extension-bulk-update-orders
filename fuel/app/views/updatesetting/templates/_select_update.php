<?php
/**
 * 更新方法のセレクトボックス
 */
$column_types_update_methods = [];
$options = [];
$select_column_id = '';
$select_update_id = '';
if (isset($bulk_update_column)) {
    $select_column_id = $bulk_update_column->receive_order_column_id;
    $select_update_id = $bulk_update_column->update_method_id;
    if (isset($update_mehod_options_list[$bulk_update_column->receive_order_column_id])) {
        $options = $update_mehod_options_list[$bulk_update_column->receive_order_column_id];
    }
}
?>
<div class="select-update<?php if (count($options) === 1) { ?> display-none<?php } ?>">
    <?php // もし何も選択できなかった場合は初期値を1(上書き)とする ?>
    <input type="hidden" name="select_update[<?= $select_column_id ?>]" value="1">
    <?= Form::select('select_update[' . $select_column_id . ']', $select_update_id, $options, ['class' => 'form-control']); ?>
</div>