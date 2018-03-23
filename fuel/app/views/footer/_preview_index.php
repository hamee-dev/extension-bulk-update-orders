<?php
/**
 * プレビュー画面のフッター
 */
?>
<div id="footer">
    <div id="footer-inner">
        <a href="<?= ${TRANSITION_PATH} ?>" class="btn btn-back add-params preview-back">
            <span class="glyphicon glyphicon-arrow-left icon"></span><?= __c('button.back') ?>
        </a>
        <a href="javascript:void(0);" class="btn btn-success pull-right" id="preview-execution" data-execute_cautions="<?= $execute_cautions ?>">
            <span class="glyphicon glyphicon glyphicon-play icon"></span><?= __p('button.execute') ?>
        </a>
    </div>
</div>