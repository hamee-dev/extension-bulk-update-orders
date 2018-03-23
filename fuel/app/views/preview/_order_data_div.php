<?php
    $excluded_reason = isset($display_value['excluded_reason']) ? $display_value['excluded_reason'] : '';
    if($excluded_reason === '' && $receive_order_id !== ''){
        $overlay_class_name = 'overlay_toggle display-none';
    } else {
        $overlay_class_name = '';
    }

    if($receive_order_id === ''){
        $div_id = '';
    } else {
        $div_id = 'receive_order_id_'.$receive_order_id;
    }
?>

<div class="panel panel-default update-result-list" id=<?= $div_id ?>>
    <div class="overlay <?= $overlay_class_name ?>">
        <div class='excluded_reason'><?= $excluded_reason ?></div>
    </div>
    <div class="panel-body">
        <div class='row'>
            <div class='col-md-6'><strong>伝票番号</strong></div>
            <?php if($excluded_reason === '') { ?>
            <div class="col-md-6 each_order_exclude_button_position">
                <a href="javascript:void(0);" data-receive_order_id=<?= $receive_order_id ?>>
                    <span class="glyphicon glyphicon-remove-circle exclude_button each_order_exclude_button"></span>
                </a>
            </div>
            <?php } ?>
        </div>
        <div class="lead"><strong class='receive_order_id_strong'><?= $receive_order_id ?></strong></div>
        <table class="update-result-table table-bordered">
            <thead>
            <tr>
                <th class='receive_order_column'>項目</th>
                <th class='before_value_column'>実行前</th>
                <th class='after_value_column'>実行後</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($receive_order_columns as $receive_order_column) { ?>
                <?php
                $logical_name  = $receive_order_column['logical_name'];
                $physical_name = $receive_order_column['physical_name'];
                $before_value = isset($display_value[$physical_name]['before_value']) ? $display_value[$physical_name]['before_value'] : '';
                $after_value  = isset($display_value[$physical_name]['after_value'])  ? $display_value[$physical_name]['after_value']  : '';
                ?>
                <tr>
                    <td class="preview-value column_name <?= $physical_name ?>"><?= $logical_name ?></td>
                    <td class="preview-value before_value <?= $physical_name ?>"><?= $before_value ?></td>
                    <td class="preview-value after_value <?= $physical_name ?>"><?= $after_value ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
