<?php
/**
 * マスタ型の項目
 */
$params = null;
$master = [];
$options = [];
$select_column_id = '';
$update_value = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $select_column_id = $bulk_update_column->receive_order_column_id;
    $update_value = $bulk_update_column->update_value;
    if (isset($master_options_list[$bulk_update_column->receive_order_column->master_name])) {
        $options = $master_options_list[$bulk_update_column->receive_order_column->master_name];
    }
}
?>
<tr class="form-group master setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
        <div class="select-master">
            <?= Form::select('select_master[' . $select_column_id . ']', $update_value, $options, ['class' => 'form-control']); ?>
        </div>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>