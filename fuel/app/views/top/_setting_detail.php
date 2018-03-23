<?php
/**
 * 一括更新内容の1行分の要素を作成する
 */

// 更新方法
$update_method = $bulk_update_column->update_method;
// 表示する更新値を取得
$value = $display_values[$bulk_update_column->id];
// 更新方法を表示するかどうか
$is_show_update_method = $show_update_methods[$bulk_update_column->id];
?>

<div class="inline">
    <div class="setting-detail-name">
        <div class="font_bold"><?= $bulk_update_column->receive_order_column->logical_name ?></div>
        <span class="glyphicon glyphicon-arrow-right"></span>
    </div>

    <div class="update-value<?= $update_method->is_calc() ? '-calc' : '' ?>">
        <?php if ($is_show_update_method) { ?>
            <div class="setting-detail-icon orange-area"><?= $update_method->name ?></div>
        <?php } ?>
        <div class="font_bold"><?= $value ?></div>
    </div>

</div>