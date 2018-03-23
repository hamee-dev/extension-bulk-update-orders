<?php
/**
 * 日付型の項目
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
<tr class="form-group date setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
        <?= Form::input('update_value[' . $select_column_id . ']', $value, ['class' => 'form-control text calendar-select']); ?>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>