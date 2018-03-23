<?php
/**
 * タグ型の項目
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
<tr class="form-group tag setting-data">
    <?= $this->fast_render('updatesetting/templates/_target', $params) ?>
    <td class="setting-list-data">
        <?= $this->fast_render('updatesetting/templates/_select_update', $params) ?>
        <?= Form::textarea('update_value[' . $select_column_id . ']', $value, ['class' => 'form-control text-area tag-text-area']); ?>
        <div class="tag-list" style="display: none">
            <?php
            /**
             * 受注分類タグで選択できるタグの要素を作成する
             */
            ?>
            <?php
            if (isset($tag_list)) {
                foreach ($tag_list as $tag) {
                    ?>
                    <a href="javascript:void(0)" class="btn tag_icon" data-tag-value="<?= $tag['name'] ?>" style="<?= $tag['style'] ?>"><?= $tag['name'] ?></a>
                <?php }
            }
            ?>
        </div>
        <?= isset($caution_template) ? $this->fast_render($caution_template) : null ?>
    </td>
    <?= $this->fast_render('updatesetting/templates/_delete_button') ?>
</tr>