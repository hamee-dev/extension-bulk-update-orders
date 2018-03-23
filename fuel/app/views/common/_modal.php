<?php
/**
 * アラート（メッセージのみのモーダル）
 */
?>
<div class="modal" id="alert-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-description"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * タイトルとメッセージのみの簡単なモーダル
 */
?>
<div class="modal" id="basic-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="add-params" action="" method="post">
                <div class="modal-header no-border-modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h3 class="modal-title text-center font_bold"></h3>
                </div>
                <div class="modal-body">
                    <div class="modal-body-description"></div>
                </div>
                <div class="modal-footer no-border-modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-danger done_button"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
/**
 * テキストボックス付きモーダル
 */
?>
<div class="modal" id="text-box-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="add-params" action="" method="post">
                <div class="modal-header no-border-modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h3 class="modal-title text-center font_bold"></h3>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert"></div>
                    <div class="modal-body-description text-center"></div>
                    <div class="form-group">
                        <p class="modal-body-label"></p>
                        <input type="text" class="form-control text-box maxlength" name="modal_text" maxlength="">
                    </div>
                </div>
                <div class="modal-footer no-border-modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-ne done_button"></button>
                </div>
            </form>
        </div>
    </div>
</div>