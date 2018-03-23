<?php
    $before_value = isset($display_value[$physical_name]['before_value']) ? $display_value[$physical_name]['before_value'] : '';
    $after_value  = isset($display_value[$physical_name]['after_value']) ? $display_value[$physical_name]['after_value'] : '';
    $excluded_reason = isset($display_value['excluded_reason']) ? $display_value['excluded_reason'] : '';
    if($excluded_reason === '' && $receive_order_id !== ''){
        $exclude_class_name = '';
    } else {
        $exclude_class_name = 'system_exclude';
    }

    if($receive_order_id === ''){
        $tr_receive_order_id_class = '';
    } else {
        $tr_receive_order_id_class = 'receive_order_id_'.$receive_order_id;
    }
?>

<tr class="receive_order_id <?= $tr_receive_order_id_class ?> <?= $exclude_class_name ?>">
    <td class='each_column_receive_order_id'>
        <div class="preview-value"><?= $receive_order_id ?></div>
    </td>
    <td class='each_column_before_value'>
        <div class="preview-value"><?= $before_value ?></div>
    </td>
    <td class='each_column_after_value'>
        <div class="preview-value"><?= $after_value ?></div>
    </td>
    <td class='each_column_exclude'>
        <a href="javascript:void(0);" data-receive_order_id="<?= $receive_order_id ?>"<?php if ($excluded_reason !== '') { ?> style="visibility: hidden" <?php } ?>>
            <span class="glyphicon glyphicon-remove-circle exclude_button each_column_exclude_button"></span>
        </a>
    </td>
    <td class="excluded_reason"<?php if ($excluded_reason === '') { ?> style="display: none" <?php } ?>><?= $excluded_reason ?></td>
</tr>
