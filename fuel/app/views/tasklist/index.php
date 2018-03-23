<?php if(empty($tasklist)){?>
<div><?= __p('empty') ?></div>
<?php }else{?>
<table id="tasklist-table" class="table-bordered table-striped">
    <tr>
        <th><?= __p('table.task_id') ?></th>
        <th><?= __p('table.name') ?></th>
        <th><?= __p('table.user_name') ?></th>
        <th><?= __p('table.created_at') ?></th>
        <th><?= __p('table.target_order_count') ?></th>
    </tr>
    <?php foreach ($tasklist as $task) { ?>
        <tr>
            <td><?= $task->request_key ?></td>
            <td><?= trim_length($task->name, Domain_Value_Receiveordercolumn::SETTING_NAME_DISPLAY_MAX_LENGTH) ?></td>
            <td><?= $task->user->pic_name ?></td>
            <td><?= $task->created_at ?></td>
            <td><?= __p('table.target_order_number', ['number' =>$task->target_order_count]) ?></td>
        </tr>
    <?php } ?>
</table>
<?php }?>

<div class="text-center">
    <a href="<?= $top_url ?>" class="btn btn-lg btn-go-to-list">
        <span class="glyphicon glyphicon-th-list icon"></span><?= __p('button.setting_list') ?>
    </a>
</div>
