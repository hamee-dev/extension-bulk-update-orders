<?php //「さらに表示」の時にcloneする用のダミー要素  ?>
<div class='display-none' id='order_data_dummy'>
    <?= $this->fast_render('preview/_order_data_div', ['receive_order_id' => '', 'display_value' => []]) ?>
</div>

<?php foreach ($display_values as $receive_order_id => $display_value) { ?>
    <?= $this->fast_render('preview/_order_data_div', ['receive_order_id' => $receive_order_id, 'display_value' => $display_value], false) ?>
<?php } ?>
<?php // 「さらに表示」の時に要素を追加する目印となる部分 ?>
<div id='order_data_last_div' class='display-none'></div>

<?php // 「さらに表示」ボタンの配置 ?>
<?= $this->fast_render('preview/_more_display') ?>