<?php

class Presenter_Updatesettingdataprovider
{
    private $_columns = [
        '1' => ['COLUMN'], '2' => ['COLUMN'], '3' => ['COLUMN'], '5' => ['COLUMN'], '6' => ['COLUMN'], '7' => ['COLUMN'], '8' => ['COLUMN'], '9' => ['COLUMN'], '10' => ['COLUMN'],
        '11' => ['COLUMN'], '12' => ['COLUMN'], '13' => ['COLUMN'], '14' => ['COLUMN'], '15' => ['COLUMN'], '16' => ['COLUMN'], '17' => ['COLUMN'], '18' => ['COLUMN'], '19' => ['COLUMN'], '20' => ['COLUMN'],
        '21' => ['COLUMN'], '22' => ['COLUMN'], '23' => ['COLUMN'], '24' => ['COLUMN'], '25' => ['COLUMN'], '26' => ['COLUMN'], '27' => ['COLUMN'], '28' => ['COLUMN'], '29' => ['COLUMN'], '30' => ['COLUMN'],
        '31' => ['COLUMN'], '32' => ['COLUMN'], '33' => ['COLUMN'], '34' => ['COLUMN'], '35' => ['COLUMN'], '36' => ['COLUMN'], '37' => ['COLUMN'], '40' => ['COLUMN'],
        '41' => ['COLUMN'], '43' => ['COLUMN'], '44' => ['COLUMN'], '45' => ['COLUMN'], '46' => ['COLUMN'], '47' => ['COLUMN'], '48' => ['COLUMN'], '49' => ['COLUMN'], '50' => ['COLUMN'],
        '51' => ['COLUMN'], '52' => ['COLUMN'], '53' => ['COLUMN'], '54' => ['COLUMN'], '55' => ['COLUMN'], '56' => ['COLUMN'], '57' => ['COLUMN'], '58' => ['COLUMN'], '59' => ['COLUMN'], '60' => ['COLUMN'],
        '62' => ['COLUMN'], '63' => ['COLUMN'], '64' => ['COLUMN'], '65' => ['COLUMN'], '66' => ['COLUMN'], '67' => ['COLUMN'], '68' => ['COLUMN'], '69' => ['COLUMN'], '70' => ['COLUMN'],
        '71' => ['COLUMN'], '72' => ['COLUMN'], '73' => ['COLUMN'], '74' => ['COLUMN'], '75' => ['COLUMN'], '76' => ['COLUMN'],
    ];

    private $_update_method = [
        '1' => ['UPDATE_METHOD'], '2' => ['UPDATE_METHOD'], '3' => ['UPDATE_METHOD'], '5' => ['UPDATE_METHOD'], '6' => ['UPDATE_METHOD'], '7' => ['UPDATE_METHOD'], '8' => ['UPDATE_METHOD'], '9' => ['UPDATE_METHOD'], '10' => ['UPDATE_METHOD'],
        '11' => ['UPDATE_METHOD'], '12' => ['UPDATE_METHOD'], '13' => ['UPDATE_METHOD'], '14' => ['UPDATE_METHOD'], '15' => ['UPDATE_METHOD'], '16' => ['UPDATE_METHOD'], '17' => ['UPDATE_METHOD'], '18' => ['UPDATE_METHOD'], '19' => ['UPDATE_METHOD'], '20' => ['UPDATE_METHOD'],
        '21' => ['UPDATE_METHOD'], '22' => ['UPDATE_METHOD'], '23' => ['UPDATE_METHOD'], '24' => ['UPDATE_METHOD'], '25' => ['UPDATE_METHOD'], '26' => ['UPDATE_METHOD'], '27' => ['UPDATE_METHOD'], '28' => ['UPDATE_METHOD'], '29' => ['UPDATE_METHOD'], '30' => ['UPDATE_METHOD'],
        '31' => ['UPDATE_METHOD'], '32' => ['UPDATE_METHOD'], '33' => ['UPDATE_METHOD'], '34' => ['UPDATE_METHOD'], '35' => ['UPDATE_METHOD'], '36' => ['UPDATE_METHOD'], '37' => ['UPDATE_METHOD'], '40' => ['UPDATE_METHOD'],
        '41' => ['UPDATE_METHOD'], '43' => ['UPDATE_METHOD'], '44' => ['UPDATE_METHOD'], '45' => ['UPDATE_METHOD'], '46' => ['UPDATE_METHOD'], '47' => ['UPDATE_METHOD'], '48' => ['UPDATE_METHOD'], '49' => ['UPDATE_METHOD'], '50' => ['UPDATE_METHOD'],
        '51' => ['UPDATE_METHOD'], '52' => ['UPDATE_METHOD'], '53' => ['UPDATE_METHOD'], '54' => ['UPDATE_METHOD'], '55' => ['UPDATE_METHOD'], '56' => ['UPDATE_METHOD'], '57' => ['UPDATE_METHOD'], '58' => ['UPDATE_METHOD'], '59' => ['UPDATE_METHOD'], '60' => ['UPDATE_METHOD'],
        '62' => ['UPDATE_METHOD'], '63' => ['UPDATE_METHOD'], '64' => ['UPDATE_METHOD'], '65' => ['UPDATE_METHOD'], '66' => ['UPDATE_METHOD'], '67' => ['UPDATE_METHOD'], '68' => ['UPDATE_METHOD'], '69' => ['UPDATE_METHOD'], '70' => ['UPDATE_METHOD'],
        '71' => ['UPDATE_METHOD'], '72' => ['UPDATE_METHOD'], '73' => ['UPDATE_METHOD'], '74' => ['UPDATE_METHOD'], '75' => ['UPDATE_METHOD'], '76' => ['UPDATE_METHOD'],
    ];

    private $_forwarding_agent_types = [
        'jikantaisitei_kbn' => ['id' => '26', 'name' => '時間指定'],
        'binsyu_kbn' => ['id' => '27', 'name' => '便種'],
        'eigyosyo_dome_kbn' => ['id' => '28', 'name' => '営業所止'],
        'okurijyo_kbn' => ['id' => '29', 'name' => '送り状'],
        'ondo_kbn' => ['id' => '30', 'name' => '温度'],
        'seal1_kbn' => ['id' => '31', 'name' => 'シール1'],
        'seal2_kbn' => ['id' => '32', 'name' => 'シール2'],
        'seal3_kbn' => ['id' => '33', 'name' => 'シール3'],
        'seal4_kbn' => ['id' => '34', 'name' => 'シール4'],
    ];

    private $_delivery_column_ids = ['8', '26', '27', '28', '29', '30', '31', '32', '33', '34'];

    private $_payment_column_ids = ['9', '18', '41'];

    private $_order_amount_column_ids = ['10', '11', '12', '13', '14', '15', '16'];

    private $_forwarding_agent_names = [
        Utility_Master::MASTER_NAME_FORWARDINGAGENT . '_TEST1',
        Utility_Master::MASTER_NAME_FORWARDINGAGENT . '_TEST2',
        Utility_Master::MASTER_NAME_FORWARDINGAGENT . '_TEST3',
    ];

    public function data_provider_get_view_info_by_setting()
    {

        $get_bulk_update_column = function (
            string $bulk_update_column_id,
            string $receive_order_column_id,
            string $column_type_id,
            bool $is_order_amount = false,
            bool $is_payment = false,
            bool $is_delivery = false,
            string $master_name = '',
            string $update_value = ''
        ) {
            return self::_get_bulk_update_column(
                $bulk_update_column_id,
                self::_get_receive_order_column(
                    $receive_order_column_id,
                    $column_type_id,
                    $is_order_amount,
                    $is_payment,
                    $is_delivery,
                    $master_name),
                Model_Updatemethod::OVERWRITE,
                $update_value);
        };

        $data = [];
        // 設定情報が無い場合（Model_Bulkupdatesettingのbulk_update_columnsが空だった場合）
        $data[] = [
            'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
            'user_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_USER_ID1,
            'bulk_update_setting' => new Model_Bulkupdatesetting(),
            'updatesetting_by_setting' => new Domain_Value_Updatesettingbysetting([], [], [], [], [], false),
        ];

        // 設定情報がある場合
        $bulk_update_setting = self::_get_bulk_update_setting(
            [
                $get_bulk_update_column('1', '5', Model_Columntype::STRING),
                $get_bulk_update_column('2', '2', Model_Columntype::STRING),
                $get_bulk_update_column('3', '3', Model_Columntype::STRING)
            ]);
        $data[] = [
            'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
            'user_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_USER_ID1,
            'bulk_update_setting' => $bulk_update_setting,
            'updatesetting_by_setting' => new Domain_Value_Updatesettingbysetting(
                [
                    'PATH',
                    'PATH',
                    'PATH',
                ],
                [
                    'CAUTION_PATH',
                    'CAUTION_PATH',
                    'CAUTION_PATH',
                ],
                [], [], [], false),
        ];

        // マスタデータの設定情報がある場合
        $bulk_update_setting = self::_get_bulk_update_setting(
            [
                $get_bulk_update_column('1', '1', Model_Columntype::MASTER, false, false, false, 'shop')
            ]);
        $bulk_update_setting->allow_update_shipment_confirmed = '1';
        $data[] = [
            'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
            'user_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_USER_ID1,
            'bulk_update_setting' => $bulk_update_setting,
            'updatesetting_by_setting' => new Domain_Value_Updatesettingbysetting(
                ['PATH'],
                ['CAUTION_PATH'],
                ['shop' => ['MASTER1', 'MASTER2']],
                [],
                [],
                true),
        ];

        // 発送関連情報の設定情報がある場合
        $bulk_update_setting = self::_get_bulk_update_setting(
            [
                $get_bulk_update_column('1', '1', Model_Columntype::MASTER, false, false, false, 'delivery', 'VALUE'),
                $get_bulk_update_column('2', '2', Model_Columntype::MASTER, false, false, false, 'forwarding_agent_binsyu'),
            ]);
        $bulk_update_setting->allow_update_yahoo_cancel = '1';
        $data[] = [
            'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
            'user_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_USER_ID1,
            'bulk_update_setting' => $bulk_update_setting,
            'updatesetting_by_setting' => new Domain_Value_Updatesettingbysetting(
                [
                    'PATH',
                    'PATH'
                ],
                [
                    'CAUTION_PATH',
                    'CAUTION_PATH'
                ],
                ['delivery' => ['MASTER1', 'MASTER2']],
                ['OPTION1', 'OPTION2'],
                [],
                true)
        ];

        // 受注分類タグの設定情報がある場合
        $bulk_update_setting = self::_get_bulk_update_setting(
            [
                $get_bulk_update_column('1', '1', Model_Columntype::TAG),
            ]);
        $data[] = [
            'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
            'user_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_USER_ID1,
            'bulk_update_setting' => $bulk_update_setting,
            'updatesetting_by_setting' => new Domain_Value_Updatesettingbysetting(
                ['PATH'],
                ['CAUTION_PATH'],
                [],
                [],
                ['TAG1', 'TAG2'],
                false),
        ];

        return $data;
    }

    public function data_provider_get_view_info_by_columns()
    {
        return [
            [
                // 引数によって変わる処理は、このメソッドから呼んでいる_get_target_listと_get_forwarding_agent_column_listのみのため、それらのテストは各メソッドで行う。
                // よってここは1パターンのみでよい
                'company_id' => Test_Presenter_Updatesetting_Presenter::DUMMY_COMPANY_ID1,
                'bulk_update_setting' => new Model_Bulkupdatesetting(),
                'updatesetting_by_column' => new Domain_Value_Updatesettingbycolumn(
                    $this->_columns,
                    $this->_update_method,
                    $this->_delivery_column_ids,
                    $this->_payment_column_ids,
                    $this->_order_amount_column_ids,
                    $this->_forwarding_agent_types,
                    ['TARGET_LIST'],
                    ['FORWARD_AGENT_COLUMN']),
            ]
        ];
    }

    public function data_provider_get_forwarding_agent_options()
    {
        return [
            [
                // 引数にforwarding_agent_namesがあり、マスタデータを取得できた場合
                'delivery_id' => 1,
                'forwarding_agent_names' => $this->_forwarding_agent_names,
                'get_forwarding_agent_returns' => [
                    $this->_forwarding_agent_names[0] => [
                        1 => new Domain_Value_Master(1, 'MASTER1'),
                        2 => new Domain_Value_Master(2, 'MASTER2'),
                        3 => new Domain_Value_Master(3, 'MASTER3'),
                    ],
                    $this->_forwarding_agent_names[1] => [
                        4 => new Domain_Value_Master(4, 'MASTER4'),
                        5 => new Domain_Value_Master(5, 'MASTER5'),
                        6 => new Domain_Value_Master(6, 'MASTER6'),
                    ],
                    $this->_forwarding_agent_names[2] => [
                        7 => new Domain_Value_Master(7, 'MASTER7'),
                        8 => new Domain_Value_Master(8, 'MASTER8'),
                        9 => new Domain_Value_Master(9, 'MASTER9'),
                    ],
                ],
                'expected' => [
                    $this->_forwarding_agent_names[0] => [
                        '' => '',
                        1 => 'MASTER1',
                        2 => 'MASTER2',
                        3 => 'MASTER3',
                    ],
                    $this->_forwarding_agent_names[1] => [
                        '' => '',
                        4 => 'MASTER4',
                        5 => 'MASTER5',
                        6 => 'MASTER6',
                    ],
                    $this->_forwarding_agent_names[2] => [
                        '' => '',
                        7 => 'MASTER7',
                        8 => 'MASTER8',
                        9 => 'MASTER9',
                    ],
                ],
            ],
            [
                // 引数にforwarding_agent_namesがあるが、一部マスタデータを取得できなかった場合
                'delivery_id' => 1,
                'forwarding_agent_names' => $this->_forwarding_agent_names,
                'get_forwarding_agent_returns' => [
                    $this->_forwarding_agent_names[0] => [
                        1 => new Domain_Value_Master(1, 'MASTER1'),
                        2 => new Domain_Value_Master(2, 'MASTER2'),
                        3 => new Domain_Value_Master(3, 'MASTER3'),
                    ],
                    $this->_forwarding_agent_names[1] => [],
                    $this->_forwarding_agent_names[2] => [
                        7 => new Domain_Value_Master(7, 'MASTER7'),
                        8 => new Domain_Value_Master(8, 'MASTER8'),
                        9 => new Domain_Value_Master(9, 'MASTER9'),
                    ],
                ],
                'expected' => [
                    $this->_forwarding_agent_names[0] => [
                        '' => '',
                        1 => 'MASTER1',
                        2 => 'MASTER2',
                        3 => 'MASTER3',
                    ],
                    $this->_forwarding_agent_names[2] => [
                        '' => '',
                        7 => 'MASTER7',
                        8 => 'MASTER8',
                        9 => 'MASTER9',
                    ],
                ],
            ],
            [
                // 引数にforwarding_agent_namesがあるが、マスタデータを取得できなかった場合
                'delivery_id' => 1,
                'forwarding_agent_names' => $this->_forwarding_agent_names,
                'get_forwarding_agent_returns' => [
                    $this->_forwarding_agent_names[0] => [],
                    $this->_forwarding_agent_names[1] => [],
                    $this->_forwarding_agent_names[2] => [],
                ],
                'expected' => [],
            ],
            [
                // 引数にforwarding_agent_namesがなかった場合
                'delivery_id' => 1,
                'forwarding_agent_names' => [],
                'get_forwarding_agent_returns' => [
                    $this->_forwarding_agent_names[0] => [],
                    $this->_forwarding_agent_names[1] => [],
                    $this->_forwarding_agent_names[2] => [],
                ],
                'expected' => [],
            ],
        ];
    }

    public function data_provider_get_template_names()
    {
        return [
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::STRING),
                'template_name' => 'textbox',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::TEXT_AREA),
                'template_name' => 'textarea',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::EMAIL),
                'template_name' => 'textbox',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::NUMBER),
                'template_name' => 'textbox',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::DATE),
                'template_name' => 'date',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::BOOL),
                'template_name' => 'bool',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::MASTER),
                'template_name' => 'master',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::MASTER, false, false, true),
                'template_name' => 'delivery',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::TAG),
                'template_name' => 'tag',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::TELEPHONE),
                'template_name' => 'textbox',
            ],
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::ZIP),
                'template_name' => 'textbox',
            ],
        ];
    }

    public function data_provider_get_caution_template_names()
    {
        return [
            // 支払方法の場合
            [
                'receive_order_column' => self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER),
                'template_name' => 'caution_payment',
            ],
            // 受注金額関連 かつ 総合計の場合
            [
                'receive_order_column' => self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true),
                'template_name' => 'caution_order_amount_total',
            ],
            // 受注金額関連 かつ 総合計以外の場合
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::NUMBER, true),
                'template_name' => 'caution_order_amount_detail',
            ],
            // 上記以外の場合
            [
                'receive_order_column' => self::_get_receive_order_column('1', Model_Columntype::STRING),
                'template_name' => null,
            ],
        ];
    }

    public function data_provider_get_view_path() {

        $get_bulk_update_column = function (string $column_type_id, string $master_name = '') {
            return self::_get_bulk_update_column('1', self::_get_receive_order_column('1', $column_type_id, false, false, false, $master_name), Model_Updatemethod::OVERWRITE);
        };

        return [
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::STRING),
                'view_path' => 'updatesetting/templates/_text_box',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::TEXT_AREA),
                'view_path' => 'updatesetting/templates/_text_area',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::EMAIL),
                'view_path' => 'updatesetting/templates/_text_box',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::NUMBER),
                'view_path' => 'updatesetting/templates/_text_box',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::DATE),
                'view_path' => 'updatesetting/templates/_date',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::BOOL),
                'view_path' => 'updatesetting/templates/_bool',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::MASTER, 'TEST'),
                'view_path' => 'updatesetting/templates/_master',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::MASTER, Utility_Master::MASTER_NAME_DELIVERY),
                'view_path' => 'updatesetting/templates/_delivery',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::MASTER, Utility_Master::MASTER_NAME_FORWARDINGAGENT . 'TEST'),
                'view_path' => '',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::TAG),
                'view_path' => 'updatesetting/templates/_tag',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::TELEPHONE),
                'view_path' => 'updatesetting/templates/_text_box',
            ],
            [
                'bulk_update_column' => $get_bulk_update_column(Model_Columntype::ZIP),
                'view_path' => 'updatesetting/templates/_text_box',
            ],
        ];
    }

    public function data_provider_get_forwarding_agent_column_list() {

        $data = [];
        // 発送方法別タイプのカラムが１つも無い場合
        $data[] = [
            'receive_order_columns' => [
                self::_get_receive_order_column(1, Model_Columntype::STRING, false, false, false),
                self::_get_receive_order_column(2, Model_Columntype::STRING, false, false, false),
                self::_get_receive_order_column(3, Model_Columntype::STRING, false, false, false),
            ],
            'setting' => new Model_Bulkupdatesetting(),
            'forwarding_agent_column_list' => [],
        ];
        // 発送方法のカラムだけがあった場合
        $data[] = [
            'receive_order_columns' => [
                self::_get_receive_order_column(1, Model_Columntype::MASTER, false, false, true),
            ],
            'setting' => new Model_Bulkupdatesetting(),
            'forwarding_agent_column_list' => [],
        ];
        // 発送方法別タイプのカラムがあるが、設定済みでは無い場合
        $receive_order_columns = [
            self::_get_receive_order_column(1, Model_Columntype::MASTER, false, false, true, 'delivery'),
            self::_get_receive_order_column(2, Model_Columntype::MASTER, false, false, true, 'forwarding_agent_jikantaisitei'),
            self::_get_receive_order_column(3, Model_Columntype::MASTER, false, false, true, 'forwarding_agent_binsyu'),
        ];
        $data[] = [
            'receive_order_columns' => $receive_order_columns,
            'setting' => new Model_Bulkupdatesetting(),
            'forwarding_agent_column_list' => [
                self::_get_bulk_update_column(null, $receive_order_columns[1], Model_Updatemethod::OVERWRITE),
                self::_get_bulk_update_column(null, $receive_order_columns[2], Model_Updatemethod::OVERWRITE),
            ],
        ];
        // 発送方法別タイプのカラムがあり、設定済みのカラムがある場合
        $receive_order_columns = [
            self::_get_receive_order_column('1', Model_Columntype::MASTER, false, false, true, 'delivery'),
            self::_get_receive_order_column('2', Model_Columntype::MASTER, false, false, true, 'forwarding_agent_jikantaisitei'),
            self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, 'forwarding_agent_binsyu'),
            self::_get_receive_order_column('4', Model_Columntype::MASTER, false, false, true, 'forwarding_agent_eigyosyo_dome'),
        ];
        $setting_bulik_update_column = self::_get_bulk_update_column('3', $receive_order_columns[2], Model_Updatemethod::OVERWRITE, 'VALUE');
        $data[] = [
            'receive_order_columns' => $receive_order_columns,
            'setting' => self::_get_bulk_update_setting([$setting_bulik_update_column]),
            'forwarding_agent_column_list' => [
                self::_get_bulk_update_column(null, $receive_order_columns[1], Model_Updatemethod::OVERWRITE),
                $setting_bulik_update_column,
                self::_get_bulk_update_column(null, $receive_order_columns[3], Model_Updatemethod::OVERWRITE),
            ],
        ];
        return $data;
    }

    public function data_provider_get_target_list() {

        $get_setting_bulik_update_column = function (string $receive_order_column_id) {
            return self::_get_bulk_update_column('1', self::_get_receive_order_column($receive_order_column_id, Model_Columntype::STRING), Model_Updatemethod::OVERWRITE, 'VALUE');
        };

        return [
            // 条件
            // 　・受注伝票の項目の情報が存在しない
            // 　・発送方法が未選択の場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・発送方法関連（非表示）」で生成されること
            [
                'columns' => [],
                'setting' => new Model_Bulkupdatesetting(),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' =>__('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報が存在しない
            // 　・発送方法が選択済みの場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '1',
                                Model_Columntype::MASTER,
                                false,
                                false,
                                true
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' =>__('page.updatesetting.no_select_value'), 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' =>__('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する、
            // 　・発送方法が未選択
            // 　・支払関連項目が未選択
            // 　・支払方法が未選択
            // 　・受注金額関連項目が未選択
            // 　・総合計が未選択の場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「受注伝票の各項目（表示状態）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                ],
                'setting' => new Model_Bulkupdatesetting(),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => true],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => true],
                    '5' => ['name' => '受注金額関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が未選択
            // 　・支払関連項目が未選択
            // 　・支払方法が未選択
            // 　・受注金額関連項目が未選択
            // 　・総合計が未選択
            // 　・更新項目として選択している項目が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                ],
                'setting' => self::_get_bulk_update_setting([$get_setting_bulik_update_column(2)]),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => true],
                    '5' => ['name' => '受注金額関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が選択済み
            // 　・支払関連項目が未選択
            // 　・支払方法が未選択
            // 　・受注金額関連項目が未選択
            // 　・総合計が未選択
            // 　・更新項目として選択している項目（発送方法関連の項目を含む）が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「発送方法関連の受注伝票の各項目（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連1'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                    self::_get_receive_order_column('6', Model_Columntype::MASTER, false, false, true, '', '発送方法関連2'),
                ],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '2',
                                Model_Columntype::STRING
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        ),
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '3',
                                Model_Columntype::MASTER,
                                false,
                                false,
                                true
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連1', 'is_display' => false],
                    '4' => ['name' => '支払関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => true],
                    '5' => ['name' => '受注金額関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => true],
                    '6' => ['name' => '発送方法関連2', 'is_display' => false],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が未選択
            // 　・支払関連項目が選択済み
            // 　・支払方法が未選択
            // 　・受注金額関連項目が未選択
            // 　・総合計が未選択
            // 　・更新項目として選択している項目が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「受注伝票の支払方法（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連1'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::MASTER, false, true, false, '', '支払関連2'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                    self::_get_receive_order_column('6', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                ],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '2',
                                Model_Columntype::STRING
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        ),
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '4',
                                Model_Columntype::MASTER,
                                false,
                                true,
                                false
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連1', 'is_display' => false],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => false],
                    '5' => ['name' => '支払関連2', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => true],
                    '6' => ['name' => '受注金額関連', 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が未選択
            // 　・支払関連項目が未選択
            // 　・支払方法が選択済み
            // 　・受注金額関連項目が未選択
            // 　・総合計が未選択
            // 　・更新項目として選択している項目が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「支払関連の受注伝票の各項目（非表示）」
            // 　・「受注伝票の支払方法（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連1'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::MASTER, false, true, false, '', '支払関連2'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                    self::_get_receive_order_column('6', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                ],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '2',
                                Model_Columntype::STRING
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        ),
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                Model_Receiveordercolumn::COLUMN_ID_PAYMENT,
                                Model_Columntype::MASTER,
                                false,
                                true,
                                false
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連1', 'is_display' => false],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => false],
                    '5' => ['name' => '支払関連2', 'is_display' => false],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => true],
                    '6' => ['name' => '受注金額関連', 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が未選択
            // 　・支払関連項目が未選択
            // 　・支払方法が未選択
            // 　・受注金額関連項目が選択済み
            // 　・総合計が未選択
            // 　・更新項目として選択している項目が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「受注伝票の総合計（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連1'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                    self::_get_receive_order_column('6', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連2'),
                ],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '2',
                                Model_Columntype::STRING
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        ),
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '5',
                                Model_Columntype::NUMBER,
                                true,
                                false,
                                false
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => true],
                    '5' => ['name' => '受注金額関連1', 'is_display' => false],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => false],
                    '6' => ['name' => '受注金額関連2', 'is_display' => true],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
            // 条件
            // 　・受注伝票の項目の情報（発送方法関連の項目を含む）が存在する
            // 　・発送方法が未選択
            // 　・支払関連項目が未選択
            // 　・支払方法が未選択
            // 　・受注金額関連項目が未選択
            // 　・総合計が選択済み
            // 　・更新項目として選択している項目が存在する場合
            // 結果
            // 　・更新項目のリストが「未選択」
            // 　・「未選択の受注伝票の各項目（表示状態）」
            // 　・「選択済みの受注伝票の各項目（非表示）」
            // 　・「受注金額関連の受注伝票の各項目（非表示）」
            // 　・「受注伝票の総合計（非表示）」
            // 　・「発送方法関連（非表示）」で生成されること
            [
                'columns' => [
                    self::_get_receive_order_column('1', Model_Columntype::STRING, false, false, false, '', '項目1'),
                    self::_get_receive_order_column('2', Model_Columntype::STRING, false, false, false, '', '項目2'),
                    self::_get_receive_order_column('3', Model_Columntype::MASTER, false, false, true, '', '発送方法関連'),
                    self::_get_receive_order_column('4', Model_Columntype::MASTER, false, true, false, '', '支払関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_PAYMENT, Model_Columntype::MASTER, false, true, false, '', '支払方法'),
                    self::_get_receive_order_column('5', Model_Columntype::NUMBER, true, false, false, '', '受注金額関連'),
                    self::_get_receive_order_column(Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT, Model_Columntype::NUMBER, true, false, false, '', '総合計'),
                ],
                'setting' => self::_get_bulk_update_setting(
                    [
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                '2',
                                Model_Columntype::STRING
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        ),
                        self::_get_bulk_update_column(
                            '1',
                            self::_get_receive_order_column(
                                Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT,
                                Model_Columntype::NUMBER,
                                false,
                                false,
                                false
                            ),
                            Model_Updatemethod::OVERWRITE,
                            'VALUE'
                        )
                    ]
                ),
                'target_list' => [
                    '' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true],
                    '1' => ['name' => '項目1', 'is_display' => true],
                    '2' => ['name' => '項目2', 'is_display' => false],
                    '3' => ['name' => '発送方法関連', 'is_display' => true],
                    '4' => ['name' => '支払関連', 'is_display' => true],
                    Model_Receiveordercolumn::COLUMN_ID_PAYMENT => ['name' => '支払方法', 'is_display' => true],
                    '5' => ['name' => '受注金額関連', 'is_display' => false],
                    Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT => ['name' => '総合計', 'is_display' => false],
                    Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE => ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false],
                ],
            ],
        ];
    }

    /**
     * Model_Receiveordercolumnオブジェクトを取得する
     *
     * @param string $id receive_order_columns.id
     * @param string $column_type_id receive_order_columns.column_type_id
     * @param bool $is_order_amount true:receive_order_columns.order_amount=1/false:receive_order_columns.order_amount=0
     * @param bool $is_payment true:receive_order_columns.payment=1/false:receive_order_columns.payment=0
     * @param bool $is_delivery true:receive_order_columns.delivery=1/false:receive_order_columns.delivery=0
     * @param string $master_name receive_order_columns.master_name
     * @param string $logical_name receive_order_columns.logical_name
     * @return Model_Receiveordercolumn
     * @throws ErrorException
     */
    private static function _get_receive_order_column(
        string $id,
        string $column_type_id,
        bool $is_order_amount = false,
        bool $is_payment = false,
        bool $is_delivery = false,
        string $master_name = '',
        string $logical_name = ''
    ) : Model_Receiveordercolumn {
        $receive_order_column = new Model_Receiveordercolumn();
        $column_types_update_methods = [];
        $column_type_update_method = new Model_Columntypesupdatemethod();
        $column_type_update_method->update_method_id = '1';
        $column_types_update_methods[] = $column_type_update_method;
        $column_type_update_method = new Model_Columntypesupdatemethod();
        $column_type_update_method->update_method_id = '2';
        $column_types_update_methods[] = $column_type_update_method;
        $column_type = new Model_Columntype();
        $column_type->id = $column_type_id;
        $column_type->column_types_update_methods = $column_types_update_methods;
        $receive_order_column->id = $id;
        $receive_order_column->column_type = $column_type;
        $receive_order_column->column_type_id = $column_type_id;
        $receive_order_column->order_amount = $is_order_amount ? '1' : '0';
        $receive_order_column->payment = $is_payment ? '1' : '0';
        $receive_order_column->delivery = $is_delivery ? '1' : '0';
        $receive_order_column->master_name = $master_name;
        $receive_order_column->logical_name = $logical_name;
        return $receive_order_column;
    }

    /**
     * Model_Bulkupdatecolumnオブジェクトを取得する
     *
     * @param string|null $id bulk_update_columns.id
     * @param Model_Receiveordercolumn $receive_order_column Model_Receiveordercolumnオブジェクト
     * @param string $update_method_id bulk_update_columns.update_method_id
     * @param string|null $update_value bulk_update_columns.update_value
     * @return Model_Bulkupdatecolumn
     * @throws ErrorException
     */
    private static function _get_bulk_update_column($id, Model_Receiveordercolumn $receive_order_column, string $update_method_id, string $update_value = null) : Model_Bulkupdatecolumn {
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->id = $id;
        $bulk_update_column->receive_order_column = $receive_order_column;
        $bulk_update_column->receive_order_column_id = $receive_order_column->id;
        $bulk_update_column->update_method_id = $update_method_id;
        if (!is_null($update_value)) {
            $bulk_update_column->update_value = $update_value;
        }
        return $bulk_update_column;
    }

    /**
     * Model_Bulkupdatesettingオブジェクトを取得する
     *
     * @param array $bulk_update_columns Model_Bulkupdatecolumnの配列
     * @return Model_Bulkupdatesetting
     * @throws ErrorException
     */
    private static function _get_bulk_update_setting(array $bulk_update_columns) : Model_Bulkupdatesetting {
        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;
        return $setting;
    }
}