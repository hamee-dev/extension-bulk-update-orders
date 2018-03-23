<?php // 「さらに表示」ボタンの配置 ?>
<?php if($other_count > 0 ) { ?>
<div class='more_display_button_area'>
    <button class='btn btn-default more_display_button'><?= __p('more_display.button') ?></button>
    <div class='more_display_other' data-value="<?= $other_count ?>"><?= __p('more_display.other', ['number' => $other_count]) ?></div>
</div>
<?php } ?>