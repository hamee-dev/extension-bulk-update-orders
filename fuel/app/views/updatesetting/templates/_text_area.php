<?php
/**
 * テキストエリア型の項目
 */
$params = null;
$value = '';
$select_column_id = '';
$max_length = '';
if (isset($bulk_update_column)) {
    $params = ['bulk_update_column' => $bulk_update_column];
    $value = $bulk_update_column->update_value;
    $select_column_id = $bulk_update_column->receive_order_column_id;
    $max_length = $bulk_update_column->receive_order_column->input_max_length;
}
?>
<tr class="form-group textarea setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
        <?php // レンダリングされた際にテキストエリアの先頭行の改行が消えるため、回避策として固定で事前に改行コードを先頭に挿入しておく ?>
        <?= Form::textarea('update_value[' . $select_column_id . ']', "\n".$value, ['class' => 'form-control text-area maxlength', 'data-maxlength' => 'true', 'maxlength' => $max_length]); ?>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>