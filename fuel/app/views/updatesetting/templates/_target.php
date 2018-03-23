<?php
/**
 * 更新する項目のセレクトボックス
 */
$select_value = null;
$option_list = [];
if (isset($bulk_update_column)) {
    // 編集時の初期表示に使われる場合
    if (isset($is_delivery)) {
        // 発送方法関連の場合は選択している値を「発送方法関連」にする
        $select_value = Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE;
    }else{
        $select_value = $bulk_update_column->receive_order_column_id;
    }
    foreach ($target_list as $receive_order_column_id => $target) {
        // $target['is_display']がfalseで自分自身では無い場合は非表示にする
        if ((string)$receive_order_column_id !== $select_value && $target['is_display'] === false) {
            continue;
        }
        $option_list[$receive_order_column_id] = $target['name'];
    }
}else{
    // 新規作成時の初期表示、およびclone用のテンプレートの場合
    foreach ($target_list as $receive_order_column_id => $target) {
        if (isset($is_first_add) && $is_first_add && $receive_order_column_id === Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE) {
            // 新規作成時の初期表示の場合は「発送方法関連」を表示しない
            continue;
        }
        $option_list[$receive_order_column_id] = $target['name'];
    }
}
?>
<td class="setting-list-select">
    <?= Form::select('select_column[]', $select_value, $option_list, ['class' => 'form-control select_column']) ?>
</td>