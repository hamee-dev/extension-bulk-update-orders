<?php
/**
 * 設定編集画面のフッター
 */
?>
<div id="footer">
    <div id="footer-inner">
        <a href="/top" class="btn btn-back add-params">
            <span class="glyphicon glyphicon-arrow-left icon"></span><?= __c('button.back') ?>
        </a>
        <?php if ($execution_method === EXECUTION_METHOD_EXTENSION) { ?>
            <a href="javascript:void(0);" class="btn btn-success pull-right" id="setting-execution">
                <span class="glyphicon glyphicon glyphicon-play icon"></span><?= __c('button.execute') ?>
            </a>
        <?php } ?>
        <a href="javascript:void(0);" class="btn btn-ne pull-right" id="setting-save-new">
            <span class="glyphicon glyphicon-floppy-disk icon"></span><?= __p('button.save_other_name') ?>
        </a>
        <a href="javascript:void(0);" class="btn btn-ne-white pull-right" id="setting-save">
            <span class="glyphicon glyphicon-floppy-disk icon"></span><?= __p('button.save_update') ?>
        </a>
    </div>
</div>