<?php
/**
 * テキスト型（テキストボックス）の項目
 */
$params = null;
$value = '';
$input_type = 'text';
$select_column_id = '';
$max_length = '';
$max = '';
$min = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $value = $bulk_update_column->update_value;
    $select_column_id = $bulk_update_column->receive_order_column_id;
    if ($bulk_update_column->receive_order_column->column_type->is_number()) {
        // 数値型の場合は数字のみ入力できるようにする
        $input_type = 'number';
        // NOTE: maxとminに小数の値を指定するとテキストボックス右の増減ボタンをクリックした時に少数第2位まで表示されてしまうため整数値とした
        $max = (int)\Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX;
        $min = (int)\Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN;
    }
    // 最大入力文字数
    $max_length = $bulk_update_column->receive_order_column->input_max_length;
}
?>
<tr class="form-group textbox setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <div class="inline">
            <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
            <div>
                <?= Form::input('update_value[' . $select_column_id . ']', $value, ['class' => 'form-control text maxlength', 'data-maxlength' => 'true', 'maxlength' => $max_length, 'type' => $input_type, 'max' => $max, 'min' => $min]); ?>
            </div>
        </div>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>