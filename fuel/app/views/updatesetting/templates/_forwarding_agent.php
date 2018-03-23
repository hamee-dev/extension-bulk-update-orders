<?php
/**
 * 発送方法別区分の項目
 */
$params = null;
$master = [];
$options = [];
$attributes = ['class' => 'form-control select-forwarding-agent', 'disabled' => 'disabled'];
$select_column_id = '';
$update_value = '';
$type = '';
$label_name = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $select_column_id = $bulk_update_column->receive_order_column_id;
    $type = Utility_Master::get_forwarding_agent_type($bulk_update_column->receive_order_column->master_name);
    $label_name = $bulk_update_column->receive_order_column->logical_name;
    $attributes['data-type'] = $type;
    $attributes['data-column-id'] = $select_column_id;
    $update_value = $bulk_update_column->update_value;
    if (isset($forwarding_agent_options[$bulk_update_column->receive_order_column->master_name])) {
        // マスタがあれば、disabledを解除する
        unset($attributes['disabled']);
        $options = $forwarding_agent_options[$bulk_update_column->receive_order_column->master_name];
    }
}
?>
<div class="forwarding-agent">
    <label><?= $label_name ?></label>
    <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
    <?= Form::hidden('select_column[]', $select_column_id, ['class' => 'select_column']); ?>
    <?php // disabledになっているとpost時に値が送信されないため、同じ名前のhidden要素を作成する（選択できない項目の場合は空で更新したいため） ?>
    <?= Form::hidden('select_master[' . $select_column_id . ']', ''); ?>
    <?= Form::select('select_master[' . $select_column_id . ']', $update_value, $options, $attributes); ?>
</div>