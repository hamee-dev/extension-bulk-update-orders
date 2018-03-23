<?php
/**
 * ブール型（チェックボックス）の項目
 */
$params = null;
$value = '';
$select_column_id = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $value = $bulk_update_column->update_value;
    $select_column_id = $bulk_update_column->receive_order_column_id;
}
?>
<tr class="form-group bool setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
        <?php // 未チェックの場合は0を送信したいため、hiddenの要素を追加する ?>
        <?= Form::hidden('update_value[' . $select_column_id . ']', 0);?>
        <?= Form::checkbox('update_value[' . $select_column_id . ']', 1, $value === '1'); ?>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>