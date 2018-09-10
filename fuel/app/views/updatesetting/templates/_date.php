<?php
/**
 * 日付型の項目
 */
$params = null;
$select_column_id = '';
$value = '';
// 日付の入力方法のセレクトボックスの値(デフォルト「日付を入力」)
$date_select_value = 'input';
// 日付入力のテキストボックスのattributes
$attributes = ['class' => 'form-control text calendar-select date-input'];

if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $value = $bulk_update_column->update_value;
    $select_column_id = $bulk_update_column->receive_order_column_id;

    if (Model_Receiveordercolumn::is_date_select_relative_date($value)) {
        // 「今日」「明日」「明後日」のどれかだった場合
        $date_select_value = $value;
        // 日付入力のてキストボックするを非表示にする
        $attributes['style'] = 'display: none;';
    }else if (!strptime($value, '%Y/%m/%d')) {
        // フォーマットが不正な場合、空文字にする
        $value = '';
    }
}

?>
<tr class="form-group date setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <div class="inline">
            <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
            <div>
                <?= Form::select('date_select[' . $select_column_id . ']', $date_select_value, Model_Receiveordercolumn::get_date_select_types(), ['class' => 'form-control date-select']); ?>
            </div>
            <div>
                <?= Form::input('update_value[' . $select_column_id . ']', $value, $attributes); ?>
            </div>
        </div>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>