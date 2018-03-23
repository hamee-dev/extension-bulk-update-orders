<?php
/**
 * 配送型の項目
 */
$params = null;
$options = [];
$select_column_id = '';
$delivery_id = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column, 'is_delivery' => true];
    $select_column_id = $bulk_update_column->receive_order_column_id;
    $delivery_id = $bulk_update_column->update_value;
    if (isset($master_options_list['delivery'])) {
        // 配列のキーが変わらないように「+」で配列を結合する
        $options = ['' => '項目を選択'] + $master_options_list['delivery'];
    }
}else{
    // $bulk_update_columがある場合は、グローバル変数の$forwarding_agent_column_listを使うので何もしない
    // $bulk_update_columがない場合はテンプレートなので空配列を設定する
    $forwarding_agent_column_list = [];
}
?>
<tr class="form-group delivery setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <div class="select-update display-none">
            <?= Form::hidden('select_update[' . Model_Receiveordercolumn::COLUMN_ID_DELIVERY . ']', Model_Updatemethod::OVERWRITE) ?>
        </div>
        <div class="select-delivery">
            <label><?= __p('delivery_type') ?></label>
            <?= Form::hidden('select_column[]', Model_Receiveordercolumn::COLUMN_ID_DELIVERY, ['class' => 'select_column']) ?>
            <?= Form::select('select_master[' . Model_Receiveordercolumn::COLUMN_ID_DELIVERY . ']', $delivery_id, $options, ['class' => 'form-control']); ?>
        </div>
        <div class="forwarding-agent-list">
            <?php foreach ($forwarding_agent_column_list as $forwarding_agent_column) { ?>
                <?= $this->fast_render('updatesetting/templates/_forwarding_agent', ['bulk_update_column' => $forwarding_agent_column, 'delivery_id' => $delivery_id]); ?>
            <?php } ?>
        </div>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>