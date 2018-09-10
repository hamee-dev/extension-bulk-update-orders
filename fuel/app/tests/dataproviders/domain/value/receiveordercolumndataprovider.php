<?php
class Domain_Value_Receiveordercolumndataprovider{
    /**
     * get_display_valueのbool型のテストケース用
     * $receive_order_column_id: 確認チェック,重要チェック
     * $original_value: 0,1,2
     * $is_order_value: true,false
     */
    public function data_provider_get_display_value_for_bool() {
        $column_id_important_check = '59';
        return [
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '0', 'is_order_value' => true,  'expect' => ''],
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '0', 'is_order_value' => false, 'expect' => 'なし'],
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '1', 'is_order_value' => true,  'expect' => 'なし'],
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '1', 'is_order_value' => false, 'expect' => 'あり'],
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '2', 'is_order_value' => true,  'expect' => 'あり'],
            ['receive_order_column_id' => Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK, 'original_value' => '2', 'is_order_value' => false, 'expect' => \Lang::get('common.empty_update_dom')],

            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '0', 'is_order_value' => true,  'expect' => 'なし'],
            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '0', 'is_order_value' => false, 'expect' => 'なし'],
            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '1', 'is_order_value' => true,  'expect' => 'あり'],
            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '1', 'is_order_value' => false, 'expect' => 'あり'],
            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '2', 'is_order_value' => true,  'expect' => ''],
            ['receive_order_column_id' => $column_id_important_check, 'original_value' => '2', 'is_order_value' => false, 'expect' => \Lang::get('common.empty_update_dom')],
        ];
    }

    /**
     * get_display_valueの日付型のテストケース用
     */
    public function data_provider_get_display_value_for_date() {
        return [
            // 正常系
            ['update_value' => '2018/06/15 00:00:00', 'expect' => '2018/06/15'],
            ['update_value' => '2018-06-15 11:11:11', 'expect' => '2018/06/15'],
            ['update_value' => '20180615 000000',     'expect' => '2018/06/15'],
            ['update_value' => '20180615',            'expect' => '2018/06/15'],
            ['update_value' => '2016/02/29 00:00:00', 'expect' => '2016/02/29'],
            ['update_value' => 'today', 'expect' => '<div class="setting-detail-icon blue-area">今日</div>'],
            ['update_value' => 'tomorrow', 'expect' => '<div class="setting-detail-icon blue-area">明日</div>'],
            ['update_value' => '+2 day', 'expect' => '<div class="setting-detail-icon blue-area">明後日</div>'],

            // 異常系
            ['update_value' => '',       'expect' => \Lang::get('common.empty_update_dom')],
            ['update_value' => null,     'expect' => \Lang::get('common.empty_update_dom')],
            ['update_value' => '123',    'expect' => \Lang::get('common.empty_update_dom')],
            ['update_value' => 'あああ', 'expect' => \Lang::get('common.empty_update_dom')],
            ['update_value' => '0000-00-00 00:00:00', 'expect' => \Lang::get('common.empty_update_dom')],

            // あり得ない日付に関しては空文字ではなくあり得ない日付を無視した日付を返す
            ['update_value' => '2018/02/30', 'expect' => '2018/03/02'],
            ['update_value' => '2017/02/29', 'expect' => '2017/03/01'],
        ];
    }

    public function data_provider_get_display_value_for_number() {
        return [
            // 小数点が無い場合そのまま返ること
            ['value' => '0', 'expected' => '0'],
            ['value' => '100', 'expected' => '100'],
            // 小数点があるが0の場合、小数点が消えること
            ['value' => '0.0', 'expected' => '0'],
            ['value' => '50.0', 'expected' => '50'],
            ['value' => '45.00', 'expected' => '45'],
            // 小数点があり0ではない場合、そのまま返ること
            ['value' => '0.1', 'expected' => '0.1'],
            ['value' => '100.3', 'expected' => '100.3'],
            ['value' => '52.04', 'expected' => '52.04'],
        ];
    }

    public function data_provider_get_display_value_for_trim() {
        return [
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => false,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH),
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH),
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => false,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH+1),
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH).'...',
            ],
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => true,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH),
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH),
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => true,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH+1),
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH).'...',
            ],
            // 改行を含む場合、改行は2文字でカウントされること
            // trimされた後にnl2brが適用されること
            [
                'is_preview' => false,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH-2)."\r\n",
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH-2)."<br />\r\n",
            ],
            [
                'is_preview' => false,
                'update_value' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH-3)."\r\n".'aa',
                'expect' => str_repeat('a', Domain_Value_Receiveordercolumn::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH-3)."<br />\r\na...",
            ],
            // nullの時に「空欄の表示」になること
            [
                'is_preview' => false,
                'update_value' => null,
                'expect' => __c('empty_update_dom'),
            ],
        ];
    }

    public function data_provider_get_display_value_for_tag_trim() {
        return [
            // タグ名に関するtrim
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => false,
                'update_value' => '['.str_repeat('a', Domain_Value_Receiveordercolumn::TAG_NAME_DISPLAY_MAX_LENGTH).']',
                'expect' => '<div class="font_bold tag-list"><span>'.str_repeat('a', Domain_Value_Receiveordercolumn::TAG_NAME_DISPLAY_MAX_LENGTH).'</span></div>',
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => false,
                'update_value' => '['.str_repeat('a', Domain_Value_Receiveordercolumn::TAG_NAME_DISPLAY_MAX_LENGTH+1).']',
                'expect' => '<div class="font_bold tag-list"><span>'.str_repeat('a', Domain_Value_Receiveordercolumn::TAG_NAME_DISPLAY_MAX_LENGTH).'...</span></div>',
            ],
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => true,
                'update_value' => '['.str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH).']',
                'expect' => '<div class="font_bold tag-list"><span>'.str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH).'</span></div>',
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => true,
                'update_value' => '['.str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH+1).']',
                'expect' => '<div class="font_bold tag-list"><span>'.str_repeat('a', Domain_Value_Receiveordercolumn::PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH).'...</span></div>',
            ],

            // タグの個数に関するtrim
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => false,
                'update_value' => str_repeat('[a]', Domain_Value_Receiveordercolumn::TAG_DISPLAY_MAX_COUNT),
                'expect' => '<div class="font_bold tag-list">'.str_repeat('<span>a</span>', Domain_Value_Receiveordercolumn::TAG_DISPLAY_MAX_COUNT).'</div>',
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => false,
                'update_value' => str_repeat('[a]', Domain_Value_Receiveordercolumn::TAG_DISPLAY_MAX_COUNT+1),
                'expect' => '<div class="font_bold tag-list">'.str_repeat('<span>a</span>', Domain_Value_Receiveordercolumn::TAG_DISPLAY_MAX_COUNT).\Lang::get('common.other_tag_count', ['number' => 1]).'</div>',
            ],
            // 上限値ならtrimされずそのまま返ること
            [
                'is_preview' => true,
                'update_value' => str_repeat('[a]', Domain_Value_Receiveordercolumn::PREVIEW_TAG_DISPLAY_MAX_COUNT),
                'expect' => '<div class="font_bold tag-list">'.str_repeat('<span>a</span>', Domain_Value_Receiveordercolumn::PREVIEW_TAG_DISPLAY_MAX_COUNT).'</div>',
            ],
            // 上限値をこえている場合はtrimされること
            [
                'is_preview' => true,
                'update_value' => str_repeat('[a]', Domain_Value_Receiveordercolumn::PREVIEW_TAG_DISPLAY_MAX_COUNT+1),
                'expect' => '<div class="font_bold tag-list">'.str_repeat('<span>a</span>', Domain_Value_Receiveordercolumn::PREVIEW_TAG_DISPLAY_MAX_COUNT).\Lang::get('common.other_tag_count', ['number' => 1]).'</div>',
            ],
        ];
    }

    public function data_provider_is_show_update_method() {
        return [
            // 通常、更新方法は表示されること（テキスト型）
            ['column_type_id' => Model_Columntype::STRING, 'value' => 'TEST', 'expected' => true],
            // 通常、更新方法は表示されること（数値型）
            ['column_type_id' => Model_Columntype::NUMBER, 'value' => '100', 'expected' => true],
            // 「空欄にする」の場合は更新方法は表示されないこと
            ['column_type_id' => Model_Columntype::STRING, 'value' => __c('empty_update_dom'), 'expected' => false],
            // マスタ型の場合は更新方法は表示されないこと
            ['column_type_id' => Model_Columntype::MASTER, 'value' => 'TEST', 'expected' => false],
            // ブール型の場合は更新方法は表示されないこと
            ['column_type_id' => Model_Columntype::BOOL, 'value' => '1', 'expected' => false],
            // 日付型の場合は更新方法は表示されないこと
            ['column_type_id' => Model_Columntype::DATE, 'value' => '2018-07-01', 'expected' => false],
        ];
    }
}