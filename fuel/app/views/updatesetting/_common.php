<script>
    // 名前空間の設定
    var EXT_BUO = EXT_BUO || {};
    EXT_BUO.UPDATESETTING = EXT_BUO.UPDATESETTING || {};

    // 「登録されている一括更新設定の内容」と「現在表示している一括更新設定の内容」の差異あり判定情報
    EXT_BUO.UPDATESETTING.is_different_original = <?= json_encode($is_different_original) ?>;
    // カラム情報の連想配列
    EXT_BUO.UPDATESETTING.columns = <?= json_encode($js_columns) ?>;
    // マスタ情報の連想配列
    EXT_BUO.UPDATESETTING.master_list = <?= json_encode($js_master_list) ?>;
    // 発送関連のカラムIDの配列
    EXT_BUO.UPDATESETTING.delivery_column_ids = <?= json_encode($js_delivery_column_ids) ?>;
    // 支払関連のカラムIDの配列
    EXT_BUO.UPDATESETTING.payment_column_ids = <?= json_encode($js_payment_column_ids) ?>;
    // 支払方法のカラムID
    EXT_BUO.UPDATESETTING.payment_method_column_id = <?= json_encode(Model_Receiveordercolumn::COLUMN_ID_PAYMENT) ?>;
    // 受注金額関連のカラムIDの配列
    EXT_BUO.UPDATESETTING.order_amount_column_ids = <?= json_encode($js_order_amount_column_ids) ?>;
    // 総合計のカラムID
    EXT_BUO.UPDATESETTING.total_amount_column_id = <?= json_encode(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT) ?>;
    // 発送関連情報のタイプ情報の連想配列
    EXT_BUO.UPDATESETTING.forwarding_agent_types = <?= json_encode($js_forwarding_agent_types) ?>;
    // 設定名の最大入力文字数
    EXT_BUO.UPDATESETTING.setting_name_max_length = <?= Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH ?>;

    // 直接入力する日付の入力方法
    EXT_BUO.UPDATESETTING.date_select_type_input = '<?= Model_Receiveordercolumn::DATE_SELECT_TYPE_INPUT ?>';
    // 相対的な日付の入力方法の配列(「今日」「明日」「明後日」)
    EXT_BUO.UPDATESETTING.date_select_type_relative_list = <?= json_encode(Model_Receiveordercolumn::get_relative_date_list()) ?>;

</script>
<?= Form::hidden('no-select-value', __p('no_select_value'), ['id' => 'no-select-value']);?>
<div id="setting-list">
    <table id="setting-list-tr-template" class="display-none">
        <?php
        /**
         * 項目のテンプレート
         * jsで項目を追加する際はこの要素をcloneする
         */
        ?>
        <?= $this->fast_render('updatesetting/templates/_add') ?>
        <?= $this->fast_render('updatesetting/templates/_bool') ?>
        <?= $this->fast_render('updatesetting/templates/_date') ?>
        <?= $this->fast_render('updatesetting/templates/_delivery') ?>
        <?= $this->fast_render('updatesetting/templates/_master') ?>
        <?= $this->fast_render('updatesetting/templates/_text_area') ?>
        <?= $this->fast_render('updatesetting/templates/_text_box') ?>
        <?= $this->fast_render('updatesetting/templates/_tag') ?>
    </table>
    <div id="setting-list-data-caution-template" class="display-none">
        <?php
        /**
         * 更新項目の注意文言のテンプレート
         * jsで項目を追加する際はこの要素をcloneする
         */
        ?>
        <?= $this->fast_render('updatesetting/templates/_caution_payment') ?>
        <?= $this->fast_render('updatesetting/templates/_caution_order_amount_detail') ?>
        <?= $this->fast_render('updatesetting/templates/_caution_order_amount_total') ?>
    </div>
    <div id="setting-list-forwarding-agent-template" style="display: none">
        <?= $this->fast_render('updatesetting/templates/_forwarding_agent') ?>
    </div>

    <form id="setting-form" name="settingform" class="add-params" method="post" action="">
        <?= Form::hidden(BULK_UPDATE_SETTING_ID, $bulk_update_setting_id);?>
        <?= Form::hidden('name', $setting->name);?>
        <table id="setting-list-table" class="table-bordered">
            <tr>
                <th class='column_name_col'><?= __p('table.column_name') ?></th>
                <th class='update_detail_col'><?= __p('table.update_detail') ?></th>
                <th class='setting-list-button'></th>
            </tr>
            <?php if(empty($setting->bulk_update_columns)) {
                /**
                 * 新規作成画面
                 */
                echo $this->fast_render('updatesetting/templates/_add', ['is_first_add' => true]);
            }else{
                /**
                 * 編集画面
                 */
                foreach ($setting->bulk_update_columns as $index => $bulk_update_column) {
                    // 発送方法別タイプ区分の場合は$template_file_listには含まれないのでスキップする
                    if (!empty($template_file_list[$index])) {
                        echo $this->fast_render(
                            $template_file_list[$index],
                            [
                                'bulk_update_column' => $bulk_update_column,
                                'caution_template' => $caution_template_file_list[$index]
                            ]
                        );
                    }
                }
            }
            ?>
        </table>

        <?php $columns_len = count($setting->bulk_update_columns) ?>
        <div id="add-button" class="text-center"<?php if($columns_len === 0 || $columns_len >= 20 ) { ?> style="display: none"<?php } ?>>
            <a href="javascript:void(0);" class="btn btn-ne">
                <span class="glyphicon glyphicon-plus-sign icon"></span><?= __p('button.add_column') ?>
            </a>
        </div>

        <div class="text-center">
            <div id="setting-option">
                <div id="setting-option-button" data-toggle="collapse" data-target="#setting-option-list">
                    <span class="glyphicon glyphicon-wrench icon"></span><?= __p('setting_option') ?><span class="glyphicon glyphicon-chevron-down"></span>
                </div>
                <ul id="setting-option-list" class="collapse <?= $is_open_option ? ' in' : '' ?>">
                    <?php foreach ($option_list as $option) { ?>
                        <li>
                            <label>
                                <?= Form::hidden($option, 0);?>
                                <?= Form::checkbox($option, 1, $setting->{$option} === '1'); ?><?= __p( $option . '.title') ?>
                            </label>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" data-original-title="<?= __p($option . '.help') ?>"><span class="glyphicon glyphicon-question-sign"></span></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </form>
</div>