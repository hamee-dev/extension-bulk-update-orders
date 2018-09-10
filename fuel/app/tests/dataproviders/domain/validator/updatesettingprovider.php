<?php
class Domain_Validator_Updatesettingprovider
{
    /**
     * バリデーション時のPOSTデータを取得する
     *
     * @param string $name 設定名
     * @param string $bulk_update_setting_id 設定ID
     * @param array $select_column 更新する項目
     * @param array $select_master マスタ型の更新する値
     * @param array $select_update 更新する方法
     * @param array $update_value マスタ型以外の更新する値
     * @param string $update_shipment_confirmed 「出荷確定済の伝票を更新対象にする」の更新値
     * @param string $update_yahoo_cancel 「Yahoo!ショッピング]伝票のキャンセル区分を更新対象にする」の更新値
     * @param string $optimistic_lock_update_retry 「一括更新処理中に別担当者が更新した伝票へ再実行を行う」の更新値
     * @param string $reflect_order_amount 「商品計、税金、手数料、発送代、他費用、ポイント数を更新した場合に総合計に反映する」の更新値
     * @return array
     */
    private static function _get_post_params(
        string $name = '',
        string $bulk_update_setting_id = '',
        array $select_column = [],
        array $select_master = [],
        array $select_update = [],
        array $update_value = [],
        string $update_shipment_confirmed = '0',
        string $update_yahoo_cancel = '0',
        string $optimistic_lock_update_retry = '0',
        string $reflect_order_amount = '0'
    ) : array {
        return [
            'name' => $name,
            'bulk_update_setting_id' => $bulk_update_setting_id,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => $select_column,
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => $select_master,
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => $select_update,
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => $update_value,
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => $update_shipment_confirmed,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => $update_yahoo_cancel,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => $optimistic_lock_update_retry,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => $reflect_order_amount
        ];
    }

    /**
     * 指定したランダムな文字列を返す
     *
     * @param int $length 文字数
     * @return string
     */
    private static function _make_rand_str(int $length) : string {
        $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
        $r_str = '';
        for ($i = 0; $i < $length; $i++) {
            $r_str .= $str[rand(0, count($str) - 1)];
        }
        return $r_str;
    }

    public function data_provider_run_name()
    {
        $data = [
            [
                // 新規作成
                'post_params' => self::_get_post_params('テスト1'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 新規作成（設定名が無い場合バリデーションエラーになること）
                'post_params' => self::_get_post_params(),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（設定名が90文字の場合バリデーションエラーにならないこと）
                'post_params' => self::_get_post_params(self::_make_rand_str(Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH)),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 新規作成（設定名が90文字を超えた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params(self::_make_rand_str(Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH + 1)),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（他の企業の設定名と重複していてもバリデーションエラーにはならないこと）
                'post_params' => self::_get_post_params('TEST4'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 新規作成（temporary=1の設定名と重複していてもバリデーションエラーにはならないこと）
                'post_params' => self::_get_post_params('TEST3'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 新規作成（設定名が重複した場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('TEST1'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集
                'post_params' => self::_get_post_params('テスト1', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 編集（設定名が無い場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（名前を変更してない場合バリデーションエラーにならないこと）
                'post_params' => self::_get_post_params('TEST1', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 編集（他の企業の設定名と重複していてもバリデーションエラーにはならないこと）
                'post_params' => self::_get_post_params('TEST4', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 編集（temporary=1の設定名と重複していてもバリデーションエラーにはならないこと）
                'post_params' => self::_get_post_params('TEST3', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 編集（設定名が重複した場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('TEST2', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // create=1だった場合（bulk_update_setting_idがあっても新規作成と同じ扱いになるため、bulk_update_setting_idの設定名と重複している場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('TEST1', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1), ['create' => '1']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名がなくてもバリデーションエラーにならないこと）
                'post_params' => array_merge(self::_get_post_params()),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => true,
            ],
            [
                // 一時保存（設定名が重複していてもバリデーションエラーにならないこと）
                'post_params' => array_merge(self::_get_post_params('TEST1')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => true,
            ],
            [
                // 一時保存（設定名が90文字の場合バリデーションエラーにならないこと）
                'post_params' => self::_get_post_params(self::_make_rand_str(Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH)),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => true,
            ],
            [
                // 一時保存（設定名が90文字を超えた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params(self::_make_rand_str(Domain_Validator_Updatesetting::SETTING_NAME_MAX_LENGTH + 1)),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名がなくてもバリデーションエラーにならないこと）
                'post_params' => array_merge(self::_get_post_params()),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => true,
            ],
            [
                // キュー登録（設定名が重複していてもバリデーションエラーにならないこと）
                'post_params' => array_merge(self::_get_post_params('TEST1')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => true,
            ],
            [
                // 新規作成（設定名に「'」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1\''),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（設定名に「"」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1"'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（設定名に「<」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1<'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（設定名に「[」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1['),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 新規作成（設定名に「]」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1]'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（設定名に「'」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1\'', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（設定名に「"」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1"', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（設定名に「<」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1<', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（設定名に「[」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1[', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 編集（設定名に「]」が入っていた場合バリデーションエラーになること）
                'post_params' => self::_get_post_params('テスト1]', Test_Domain_Validator_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名に「'」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1\'')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名に「"」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1"')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名に「<」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1<')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名に「[」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1[')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // 一時保存（設定名に「]」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1]')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_TEMPORARY,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名に「'」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1\'')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名に「"」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1"')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名に「<」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1<')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名に「[」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1[')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => false,
            ],
            [
                // キュー登録（設定名に「]」が入っていた場合バリデーションエラーになること）
                'post_params' => array_merge(self::_get_post_params('テスト1]')),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_EXECUTE,
                'valid_result' => false,
            ],
        ];

        $post_params = self::_get_post_params('');
        unset($post_params['name']);
        $data[] = [
            // 設定名の要素がない場合バリデーションエラーになること
            'post_params' => $post_params,
            'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
            'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
            'valid_result' => false,
        ];

        return $data;
    }

    public function data_provider_run_option()
    {
        $data = [
            [
                // 高度な設定の正常系(0の場合エラーにならないこと)
                'post_params' => self::_get_post_params('TEST4', '', [], [], [], [], '0', '0', '0'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 高度な設定の正常系(1の場合エラーにならないこと)
                'post_params' => self::_get_post_params('TEST4', '', [], [], [], [], '1', '1', '1'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 「出荷確定済の伝票を更新対象にする」が0及び1以外の値だった場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [], [], [], [], 'hoge', '0', '1'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 「[Yahoo!ショッピング]伝票のキャンセル区分を更新対象にする」が0及び1以外の値だった場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [], [], [], [], '0', 'hoge', '1'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 「一括更新処理中に別担当者が更新した伝票へ再実行を行う」が0及び1以外の値だった場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [], [], [], [], '0', '0', 'hoge'),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];

        $post_params = self::_get_post_params('TEST4');
        unset($post_params[Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME]);
        $data[] = [
            // 「出荷確定済の伝票を更新対象にする」の要素が存在しなかった場合バリデーションエラーになること
            'post_params' => $post_params,
            'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
            'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
            'valid_result' => false,
        ];

        $post_params = self::_get_post_params('TEST4');
        unset($post_params[Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME]);
        $data[] = [
            // 「[Yahoo!ショッピング]伝票のキャンセル区分を更新対象にする」の要素が存在しなかった場合バリデーションエラーになること
            'post_params' => $post_params,
            'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
            'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
            'valid_result' => false,
        ];

        $post_params = self::_get_post_params('TEST4');
        unset($post_params[Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME]);
        $data[] = [
            // 「一括更新処理中に別担当者が更新した伝票へ再実行を行う」の要素が存在しなかった場合バリデーションエラーになること
            'post_params' => $post_params,
            'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
            'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
            'valid_result' => false,
        ];

        return $data;
    }

    public function data_provider_run_select_column()
    {
        return [
            [
                // 更新する項目の正常系
                'post_params' => self::_get_post_params('TEST4', '', [2, 36, 37], [], [2 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [2 => 'VALUE1', 36 => 'VALUE2', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 存在しないカラムIDだった場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10000 , 2, 36], [], [10000 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE], [10000 => 'VALUE1', 2 => 'VALUE2', 36 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 支払方法が選択されている かつ 支払方法を除く支払関連が選択されていない場合バリデーションエラーにはならないこと
                'post_params' => self::_get_post_params('TEST4', '', [9, 36, 37], [9 => '0'], [9 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [36 => 'VALUE2', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 支払方法が選択されていない かつ 支払方法を除く支払関連が選択されている場合バリデーションエラーにはならないこと
                'post_params' => self::_get_post_params('TEST4', '', [18, 41, 37], [18 => '1', 41 => '10'], [18 => Model_Updatemethod::OVERWRITE, 41 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 支払方法と支払方法を除く支払関連を同時に選択している場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [9, 18, 41], [9 => '0', 18 => '1', 41 => '10'], [9 => Model_Updatemethod::OVERWRITE, 18 => Model_Updatemethod::OVERWRITE, 41 => Model_Updatemethod::OVERWRITE], []),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 総合計が選択されている かつ 総合計を除く受注金額関連が選択されていない場合バリデーションエラーにはならないこと
                'post_params' => self::_get_post_params('TEST4', '', [16, 36, 37], [], [16 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [16 => '11111', 36 => 'VALUE2', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 総合計が選択されていない かつ 総合計を除く受注金額関連が選択されている場合バリデーションエラーにはならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10, 11, 37], [], [10 => Model_Updatemethod::OVERWRITE, 11 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [10 => '11111', 11 => '22222', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 総合計と総合計を除く受注金額関連を同時に選択している場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [16, 10, 11], [], [16 => Model_Updatemethod::OVERWRITE, 10 => Model_Updatemethod::OVERWRITE, 11 => Model_Updatemethod::OVERWRITE], [16 => '11111', 10 => '22222', 11 => '33333']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_select_update()
    {
        $data = [
            [
                // 更新方法の正常系
                'post_params' => self::_get_post_params('TEST4', '', [2, 36, 37], [], [2 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [2 => 'VALUE1', 36 => 'VALUE2', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 対応していない更新方法だった場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [2, 36, 37], [], [2 => Model_Updatemethod::DIVISION, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE], [2 => 'VALUE1', 36 => 'VALUE2', 37 => 'VALUE3']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];

        $post_params = self::_get_post_params('TEST4', '', [2, 36, 37], [], [], [2 => 'VALUE1', 36 => 'VALUE2', 37 => 'VALUE3']);
        unset($post_params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME]);
        $data[] = [
            // 更新方法の要素が存在しなかった場合バリデーションエラーになること
            'post_params' => $post_params,
            'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
            'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
            'valid_result' => false,
        ];
        return $data;
    }

    public function data_provider_run_forwarding_agent()
    {
        return [
            [
                // 発送方法関連項目の正常系
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 発送方法関連項目がすべて空文字でもバリデーションエラーにならないこと
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '', 27 => '', 28 => '', 29 => '', 30 => '', 31 => '', 32 => '', 33 => '', 34 => ''],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 発送方法が無い場合バリデーションエラーになること
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（時間指定）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => 'VALUE', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（便種）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => 'VALUE', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（営業所止）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => 'VALUE', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（送り状）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => 'VALUE', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（温度）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => 'VALUE', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（シール1）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => 'VALUE', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（シール2）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => 'VALUE', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（シール3）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => 'VALUE', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること（シール4）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => 'VALUE'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_master()
    {
        return [
            [
                // マスタ型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [1], [1 => '1'], [1 => Model_Updatemethod::OVERWRITE], []),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // マスタに存在しない値を選択した場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [1], [1 => 'VALUE'], [1 => Model_Updatemethod::OVERWRITE], []),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（時間指定がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（便種がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（営業所止がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（送り状がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（温度がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（シール1がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（シール2がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（シール3がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // マスタが発送方法だった場合、発送方法関連項目が足りない場合バリデーションエラーになること（シール4がない）
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_addwrite()
    {
        return [
            [
                // 空文字以外の追記でバリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [2], [], [2 => Model_Updatemethod::ADDWRITE], [2 => 'VALUE1']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 空文字を追記しようとした場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [2], [], [2 => Model_Updatemethod::ADDWRITE], [2 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_text()
    {
        return [
            [
                // テキスト型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [36], [], [36 => Model_Updatemethod::OVERWRITE], [36 => 'VALUE1']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最小文字数が指定されてない場合は空文字でもバリデーションエラーは発生しないこと
                'post_params' => self::_get_post_params('TEST4', '', [36], [], [36 => Model_Updatemethod::OVERWRITE], [36 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最小文字数が指定されいる場合は空文字はバリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [2], [], [2 => Model_Updatemethod::OVERWRITE], [2 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 最大文字数と同じ文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [2], [], [2 => Model_Updatemethod::OVERWRITE], [2 => self::_make_rand_str(50)]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [2], [], [2 => Model_Updatemethod::OVERWRITE], [2 => self::_make_rand_str(51)]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_number()
    {
        return [
            [
                // 数値型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => '1000']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 小数点でもバリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => '0.5']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 範囲を下回った場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN - 0.001]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 範囲の下限と同じだった場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 範囲を上回った場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX + 0.001]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 範囲を上限と同じだった場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数と同じ文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => '-111111111.11']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => '11111111111111']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 0以外の除算の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::DIVISION], [10 => '1']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 0除算の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::DIVISION], [10 => '0']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 0で除算以外の計算の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::MULTIPLICATION], [10 => '0']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 空文字の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::MULTIPLICATION], [10 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 1e-2などの指数表記の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [10], [], [10 => Model_Updatemethod::OVERWRITE], [10 => '1e-2']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_email()
    {
        return [
            [
                // Eメール型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [52], [], [52 => Model_Updatemethod::OVERWRITE], [52 => 'hoge@nextengine.com']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // メールアドレス形式以外の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [52], [], [52 => Model_Updatemethod::OVERWRITE], [52 => 'hoge']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 最大文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [52], [], [52 => Model_Updatemethod::OVERWRITE], [52 => self::_make_rand_str(64).'@' . self::_make_rand_str(31) . '.com']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [52], [], [52 => Model_Updatemethod::OVERWRITE], [52 => self::_make_rand_str(64).'@' . self::_make_rand_str(32) . '.com']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 空文字の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [52], [], [52 => Model_Updatemethod::OVERWRITE], [52 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
        ];
    }

    public function data_provider_run_bool()
    {
        return [
            [
                // ブール型の正常系（0の場合）
                'post_params' => self::_get_post_params('TEST4', '', [5], [], [5 => Model_Updatemethod::OVERWRITE], [5 => '0']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // ブール型の正常系（1の場合）
                'post_params' => self::_get_post_params('TEST4', '', [5], [], [5 => Model_Updatemethod::OVERWRITE], [5 => '1']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 0及び1以外の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [5], [], [5 => Model_Updatemethod::OVERWRITE], [5 => 'hoge']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_tag()
    {
        // 最大入力文字数ぴったりのタグと、それを超えた文字数のタグを作成する
        $max_tag = '';
        $max_length = 65533;
        $tag_max_length = Domain_Validator_Updatesetting::TAG_NAME_MAX_LENGTH;
        while (true) {
            $max_tag .= '[' . self::_make_rand_str($tag_max_length) . ']';
            $sub = $max_length - mb_strlen($max_tag);
            if ($sub <= 0) {
                break;
            }else if ($sub < $tag_max_length + 2) {
                $max_tag .= '[' . self::_make_rand_str($sub - 2) . ']';
                break;
            }
        }
        $max_orver_tag = $max_tag;
        $max_orver_tag .= '[' . self::_make_rand_str(1) . ']';

        return [
            [
                // タグ型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => '[TAG1][TAG2][TAG3]']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // タグ形式では無い場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => '[TAG1][TAG2[TAG3]']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // タグ名の最大文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => '['.self::_make_rand_str($tag_max_length).']']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // タグ名の最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => '['.self::_make_rand_str($tag_max_length + 1).']']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 最大文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => $max_tag]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => $max_orver_tag]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 空文字の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // タグ名に「'」が含まれている場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => "[TEST']"]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // タグ名に「"」が含まれている場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => "[TEST\"]"]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // タグ名に「<」が含まれている場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => "[TEST<]"]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // タグ名に「[」が含まれている場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [6], [], [6 => Model_Updatemethod::OVERWRITE], [6 => "[TEST[]"]),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            // タグ名に「]」が含まれている場合バリデーションエラーになるが、正規表現的にこのパターンになることはありえないので省略する
        ];
    }

    public function data_provider_run_telephone()
    {
        return [
            [
                // 電話番号型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [51], [], [51 => Model_Updatemethod::OVERWRITE], [51 => '08011112222']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [51], [], [51 => Model_Updatemethod::OVERWRITE], [51 => '11111111112222222222']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [51], [], [51 => Model_Updatemethod::OVERWRITE], [51 => '111111111122222222223']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 数字以外の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [51], [], [51 => Model_Updatemethod::OVERWRITE], [51 => 'hoge']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 空文字の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [51], [], [51 => Model_Updatemethod::OVERWRITE], [51 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
        ];
    }

    public function data_provider_run_zip()
    {
        return [
            [
                // 郵便番号型の正常系
                'post_params' => self::_get_post_params('TEST4', '', [48], [], [48 => Model_Updatemethod::OVERWRITE], [48 => '2500011']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [48], [], [48 => Model_Updatemethod::OVERWRITE], [48 => '25000111111111111111']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 最大文字数を超えた場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [48], [], [48 => Model_Updatemethod::OVERWRITE], [48 => '250001111111111111111']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 数字以外の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [48], [], [48 => Model_Updatemethod::OVERWRITE], [48 => 'hoge']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 空文字の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [48], [], [48 => Model_Updatemethod::OVERWRITE], [48 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
        ];
    }

    public function data_provider_run_date()
    {
        return [
            [
                // Y/m/dのフォーマットの日付の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => '2018/06/01']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // Y/m/dのフォーマット以外の日付の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => '2018-06-01']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 「today」の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => 'today']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 「tomorrow」の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => 'tomorrow']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 「+2 day」の場合バリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => '+2 day']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 「Y/m/dのフォーマット以外の日付」「today」「tomorrow」「+2 day」以外の場合バリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => 'hoge']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 対象項目が受注日以外の場合、空文字だとバリデーションエラーにならないこと
                'post_params' => self::_get_post_params('TEST4', '', [19], [], [19 => Model_Updatemethod::OVERWRITE], [19 => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 対象項目が受注日の場合、空文字だとバリデーションエラーになること
                'post_params' => self::_get_post_params('TEST4', '', [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE], [], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => Model_Updatemethod::OVERWRITE], [Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE => '']),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_forwarding_agent_seal()
    {
        return [
            [
                // 発送方法関連項目のシールの正常系
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '004'],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE,27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // // 発送方法関連項目のシールがすべて空文字でもバリデーションエラーにならないこと
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [8, 26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '', 32 => '', 33 => '', 34 => ''],
                    [8 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール1とシール2が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '001', 33 => '003', 34 => '004'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール1とシール3が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '001', 34 => '004'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール1とシール4が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '001', 33 => '003', 34 => '001'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール2とシール3が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '002', 34 => '004'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール2とシール4が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '002'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(シール3とシール4が重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '002', 33 => '003', 34 => '003'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
            [
                // 発送方法関連項目のシールに重複がある場合バリデーションエラーになること(すべて重複)
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [26, 27, 28, 29, 30, 31, 32, 33, 34],
                    [8 => '10', 26 => '01', 27 => '001', 28 => '0', 29 => '', 30 => '', 31 => '001', 32 => '001', 33 => '001', 34 => '001'],
                    [26 => Model_Updatemethod::OVERWRITE, 26 => Model_Updatemethod::OVERWRITE, 27 => Model_Updatemethod::OVERWRITE, 28 => Model_Updatemethod::OVERWRITE, 29 => Model_Updatemethod::OVERWRITE, 30 => Model_Updatemethod::OVERWRITE, 31 => Model_Updatemethod::OVERWRITE, 32 => Model_Updatemethod::OVERWRITE, 33 => Model_Updatemethod::OVERWRITE, 34 => Model_Updatemethod::OVERWRITE],
                    []
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }

    public function data_provider_run_max_column_count()
    {
        return [
            [
                // 設定項目数がちょうど上限値でバリデーションtrue
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [2, 36, 37, 45, 46, 47, 49, 50, 53, 54, 56, 57, 20, 22, 23, 24, 63, 64, 65, 66],
                    [],
                    [2 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE, 45 => Model_Updatemethod::OVERWRITE, 46 => Model_Updatemethod::OVERWRITE, 47 => Model_Updatemethod::OVERWRITE, 49 => Model_Updatemethod::OVERWRITE, 50 => Model_Updatemethod::OVERWRITE, 53 => Model_Updatemethod::OVERWRITE, 54 => Model_Updatemethod::OVERWRITE, 56 => Model_Updatemethod::OVERWRITE, 57 => Model_Updatemethod::OVERWRITE, 20 => Model_Updatemethod::OVERWRITE, 22 => Model_Updatemethod::OVERWRITE, 23 => Model_Updatemethod::OVERWRITE, 24 => Model_Updatemethod::OVERWRITE, 63 => Model_Updatemethod::OVERWRITE, 64 => Model_Updatemethod::OVERWRITE, 65 => Model_Updatemethod::OVERWRITE, 66 => Model_Updatemethod::OVERWRITE],
                    [2 => 'TEST', 36 => 'TEST', 37 => 'TEST', 45 => 'TEST', 46 => 'TEST', 47 => 'TEST', 49 => 'TEST', 50 => 'TEST', 53 => 'TEST', 54 => 'TEST', 56 => 'TEST', 57 => 'TEST', 20 => 'TEST', 22 => 'TEST', 23 => 'TEST', 24 => 'TEST', 63 => 'TEST', 64 => 'TEST', 65 => 'TEST', 66 => 'TEST']
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => true,
            ],
            [
                // 設定項目数が上限値を超えてバリデーションfalse
                'post_params' => self::_get_post_params(
                    'TEST4',
                    '',
                    [2, 36, 37, 45, 46, 47, 49, 50, 53, 54, 56, 57, 20, 22, 23, 24, 63, 64, 65, 66, 67],
                    [],
                    [2 => Model_Updatemethod::OVERWRITE, 36 => Model_Updatemethod::OVERWRITE, 37 => Model_Updatemethod::OVERWRITE, 45 => Model_Updatemethod::OVERWRITE, 46 => Model_Updatemethod::OVERWRITE, 47 => Model_Updatemethod::OVERWRITE, 49 => Model_Updatemethod::OVERWRITE, 50 => Model_Updatemethod::OVERWRITE, 53 => Model_Updatemethod::OVERWRITE, 54 => Model_Updatemethod::OVERWRITE, 56 => Model_Updatemethod::OVERWRITE, 57 => Model_Updatemethod::OVERWRITE, 20 => Model_Updatemethod::OVERWRITE, 22 => Model_Updatemethod::OVERWRITE, 23 => Model_Updatemethod::OVERWRITE, 24 => Model_Updatemethod::OVERWRITE, 63 => Model_Updatemethod::OVERWRITE, 64 => Model_Updatemethod::OVERWRITE, 65 => Model_Updatemethod::OVERWRITE, 66 => Model_Updatemethod::OVERWRITE, 67 => Model_Updatemethod::OVERWRITE],
                    [2 => 'TEST', 36 => 'TEST', 37 => 'TEST', 45 => 'TEST', 46 => 'TEST', 47 => 'TEST', 49 => 'TEST', 50 => 'TEST', 53 => 'TEST', 54 => 'TEST', 56 => 'TEST', 57 => 'TEST', 20 => 'TEST', 22 => 'TEST', 23 => 'TEST', 24 => 'TEST', 63 => 'TEST', 64 => 'TEST', 65 => 'TEST', 66 => 'TEST', 67 => 'TEST']
                ),
                'company_id' => Test_Domain_Validator_Updatesetting::DUMMY_COMPANY_ID1,
                'type' => Domain_Validator_Updatesetting::VALID_TYPE_SAVE,
                'valid_result' => false,
            ],
        ];
    }
}