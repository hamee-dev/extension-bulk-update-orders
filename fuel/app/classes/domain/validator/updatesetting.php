<?php
/**
 * 設定保存のバリデーション
 *
 * Class Domain_Validator_Updatesetting
 */

class Domain_Validator_Updatesetting
{
    // 設定新規保存・編集のバリデーション
    const VALID_TYPE_SAVE = 0;
    // 一時保存のバリデーション(プレビューを表示するための保存時)
    const VALID_TYPE_TEMPORARY = 1;
    // キュー登録直前のバリデーション
    const VALID_TYPE_EXECUTE = 2;
    // タグ名の最大文字数
    const TAG_NAME_MAX_LENGTH = 100;
    // 設定名の最大文字数(受注分類タグに「【済】設定名」として追加されるため90文字とする)
    const SETTING_NAME_MAX_LENGTH = 90;

    /**
     * 一時保存用のバリデーションを実行する
     *
     * @param array $post_params ポストパラメータ
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @return array ['result' => true:成功/false:バリデーションエラー, 'messages' => バリデーションエラーがあった場合のメッセージ]
     * @throws FuelException
     */
    public static function run_temporary(array $post_params, string $company_id, string $user_id) : array {
        return self::run($post_params, $company_id, $user_id, self::VALID_TYPE_TEMPORARY);
    }

    /**
     * キュー登録直前のバリデーションを実行する
     *
     * @param array $post_params ポストパラメータ
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @return array ['result' => true:成功/false:バリデーションエラー, 'messages' => バリデーションエラーがあった場合のメッセージ]
     * @throws FuelException
     */
    public static function run_execute(array $post_params, string $company_id, string $user_id) : array {
        return self::run($post_params, $company_id, $user_id, self::VALID_TYPE_EXECUTE);
    }

    /**
     * 複製時のバリデーションを実行する
     * 複製時は名称のバリデーション・登録上限数のバリデーションのみを実行し中身のバリデーションは実行しない
     * （複製元はバリデーションが通っているものなので）
     *
     * @param string $company_id 企業ID
     * @param string $name 設定名称
     * @param string $bulk_update_setting_id 設定ID 設定名の重複判定に使用
     * @return array ['result' => true:成功/false:バリデーションエラー, 'messages' => バリデーションエラーがあった場合のメッセージ]
     * @throws FuelException
     */
    public static function run_copy(string $company_id, string $name, string $bulk_update_setting_id = '0') : array {
        $validation = \Validation::forge($name);
        self::name_validation_with_duplication($validation, $company_id, $name, $bulk_update_setting_id);
        self::max_count_validation($validation, $company_id);
        $validation->run(['name' => $name]);
        $message = $validation->error_message();

        return ['result' => count($message) > 0 ? false : true , 'messages' => $message];
    }

    /**
     * 設定名の更新時のバリデーションを実行する
     *
     * @param string $company_id 企業ID
     * @param string $name 設定名称
     * @param string $bulk_update_setting_id 設定ID 設定名の重複判定に使用
     * @return array ['result' => true:成功/false:バリデーションエラー, 'messages' => バリデーションエラーがあった場合のメッセージ]
     * @throws FuelException
     */
    public static function run_updatename(string $company_id, string $name, string $bulk_update_setting_id = '0') : array {
        $validation = \Validation::forge($name);
        self::name_validation_with_duplication($validation, $company_id, $name, $bulk_update_setting_id);
        $validation->run(['name' => $name]);
        $message = $validation->error_message();

        return ['result' => count($message) > 0 ? false : true , 'messages' => $message];
    }

    /**
     * 設定保存のバリデーションを実行する
     *
     * @param array $post_params ポストパラメータ
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param int $type 0:設定新規保存・編集のバリデーション/1:一時保存のバリデーション(プレビューを表示するための保存)/2:キュー登録直前のバリデーション
     * @return array ['result' => true:成功/false:バリデーションエラー, 'messages' => バリデーションエラーがあった場合のメッセージ]
     * @throws FuelException
     */
    public static function run(array $post_params, string $company_id, string $user_id, int $type = self::VALID_TYPE_SAVE) : array {
        $validation = \Validation::forge();

        // 設定名のバリデーション
        if ($type === self::VALID_TYPE_SAVE) {
            // 設定新規保存・編集時は設定名の重複もチェックする
            $bulk_update_setting_id = 0;
            if ((!isset($post_params['create']) || $post_params['create'] !== '1') && !empty($post_params['bulk_update_setting_id'])) {
                 // 編集時
                $bulk_update_setting_id = $post_params['bulk_update_setting_id'];
            } else {
                // 新規保存時
                // 最大登録数を超えていないかどうかをチェックする
                self::max_count_validation($validation, $company_id);
            }
            $name = isset($post_params['name']) ? $post_params['name'] : '';
            self::name_validation_with_duplication($validation, $company_id, $name, $bulk_update_setting_id);
        }else if (($type === self::VALID_TYPE_TEMPORARY || $type === self::VALID_TYPE_EXECUTE) && $post_params['name'] !== '') {
            // 一時保存、実行直前のバリデーション時は設定名が無い場合がある（保存しなかった場合）ので無い場合は設定名のバリデーションは不要
            // また、重複のチェック・最大登録数のチェックも不要
            self::name_validation($validation);
        }

        // 伝票に関する高度な更新設定のバリデーション
        self::option_validation($validation);

        $receive_order_columns = [];
        $receive_order_column_ids = [];
        foreach (Model_Receiveordercolumn::get_all_columns(false) as $receive_order_column) {
            $receive_order_column_ids[] = $receive_order_column->id;
            $receive_order_columns[$receive_order_column->id] = $receive_order_column;
        }

        $column_types = [];
        foreach (Model_Columntype::get_all() as $column_type) {
            foreach ($column_type->column_types_update_methods as $column_types_update_method) {
                if (!isset($column_types[$column_type->id])) {
                    $column_types[$column_type->id] = [];
                }
                $column_types[$column_type->id][] = $column_types_update_method->update_method_id;
            }
        }

        if (isset($post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME])) {
            // 発送方法関連のシールの重複チェックバリデーション
            self::forwarding_agent_seal_validation($validation, $receive_order_columns, $post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME]);
        }

        $select_colums = isset($post_params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME]) ? $post_params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME] : [];
        $master = new Utility_Master($company_id ,$user_id);

        // 設定項目数に関するバリデーション
        // 1設定に対して設定できる最大項目数を超えていないかどうかをチェックする
        self::max_column_count_validation($validation, count($select_colums));

        // 各項目ごとのバリデーション
        foreach ($select_colums as $index => $column_id) {
            $validation_name = Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME . '.' . $index;
            // 更新する項目のバリデーション
            self::select_column_validation(
                $validation,
                $validation_name,
                $receive_order_column_ids,
                $receive_order_columns,
                $select_colums,
                $column_id
            );
            if(!isset($receive_order_columns[$column_id])) {
                // 更新する項目が存在しない場合は後続のバリデーションはスキップする
                continue;
            }
            $receive_order_column = $receive_order_columns[$column_id];

            // 更新方法のバリデーション
            self::select_update_validation($validation, Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME . '.' . $column_id, $receive_order_column->logical_name, $column_types[$receive_order_column->column_type_id]);


            // カラムタイプごとのバリデーション
            if ($receive_order_column->column_type->is_master()) {
                $update_value = isset($post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$column_id]) ? $post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$column_id] : '';
                $validation_name = Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME . '.' . $column_id;
                if (Utility_Master::is_forwarding_agent($receive_order_column->master_name)) {
                    // 発送方法を取得する
                    $delivery_id = '';
                    foreach ($select_colums as $index => $column_id) {
                        if ($receive_order_columns[$column_id]->column_type->is_master() &&
                            $receive_order_columns[$column_id]->master_name === Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE) {
                            $delivery_id = $post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$column_id];
                            break;
                        }
                    }
                    // 発送方法別区分のバリデーション
                    self::forwarding_agent_validation($validation, $validation_name, $receive_order_column, $update_value, $master, $delivery_id);
                }else{

                    // マスタ型のバリデーション
                    self::master_validation($validation, $validation_name, $receive_order_column, $update_value, $master, $select_colums);
                }
            }else {
                $update_value = isset($post_params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][$column_id]) ? $post_params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][$column_id] : '';
                $select_update = isset($post_params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][$column_id]) ? $post_params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][$column_id] : '';
                $validation_name = Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME . '.' . $column_id;
                $fieldset_field = $validation->add($validation_name, $receive_order_column->logical_name);

                // 追記の場合のバリデーション（空文字は追記不可）
                self::addwrite_validation($fieldset_field, $update_value, $select_update);

                if ($receive_order_column->column_type->is_string() || $receive_order_column->column_type->is_textarea()) {
                    // テキスト型、テキストエリア型のバリデーション
                    self::text_validation($fieldset_field, $receive_order_column);
                }else if ($receive_order_column->column_type->is_number()) {
                    // 数値型のバリデーション
                    self::number_validation($fieldset_field, $receive_order_column, $update_value, $select_update);
                }else if ($receive_order_column->column_type->is_email()) {
                    // Eメール型のバリデーション
                    self::email_validation($fieldset_field, $receive_order_column, $update_value);
                }else if ($receive_order_column->column_type->is_bool()) {
                    // ブール型のバリデーション
                    self::bool_validation($fieldset_field);
                }else if ($receive_order_column->column_type->is_tag()) {
                    // タグ型のバリデーション
                    self::tag_validation($fieldset_field, $receive_order_column, $update_value);
                }else if ($receive_order_column->column_type->is_telephone()) {
                    // 電話番号型のバリデーション
                    self::telephone_validation($fieldset_field, $receive_order_column, $update_value);
                }else if ($receive_order_column->column_type->is_zip()) {
                    // 郵便番号型のバリデーション
                    self::zip_validation($fieldset_field, $receive_order_column, $update_value);
                }else if ($receive_order_column->column_type->is_date()) {
                    // 日付型のバリデーション
                    self::date_validation($fieldset_field, $receive_order_column, $update_value);
                }
            }
        }

        $validation->run($post_params);

        $message = $validation->error_message();

        return ['result' => count($message) > 0 ? false : true , 'messages' => $message];
    }

    /**
     * 設定名のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     */
    protected static function name_validation(\Validation $validation) {
        self::_set_name_validation_rule($validation);
    }

    /**
     * 設定名のバリデーション(設定名の重複チェックも行う)
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $company_id 企業ID
     * @param string $update_name 設定名
     * @param string $bulk_update_setting_id 設定ID
     */
    protected static function name_validation_with_duplication(\Validation $validation, string $company_id, string $update_name, string $bulk_update_setting_id) {
        $fieldset_field = self::_set_name_validation_rule($validation);
        $fieldset_field->add_rule(['setting_name' => function () use ($update_name, $company_id, $bulk_update_setting_id) {
                // 設定名が重複してないかチェックする
                $settings = Model_Bulkupdatesetting::findAll(
                    [
                        ['company_id', '=', $company_id],
                        ['name', '=', $update_name],
                        ['temporary', '=', 0],
                        ['id', '!=', $bulk_update_setting_id],
                    ]);
                if (count($settings) > 0) {
                    return false;
                }
                return true;
            }]);
    }

    /**
     * 設定名のバリデーションルールを設定し、そのFieldset_Fieldを返す
     *
     * @param \Validation $validation バリデーションオブジェクト
     * @return \Fieldset_Field
     */
    private static function _set_name_validation_rule(\Validation $validation) : \Fieldset_Field {
        return $validation->add('name', '設定名')
            ->add_rule('required')
            ->add_rule('min_length', 1)
            ->add_rule('max_length', self::SETTING_NAME_MAX_LENGTH)
            ->add_rule('match_pattern', "/^(?!.*(\'|\"|<|\[|\])).*$/", "に「'」「\"」「<」「[」「]」を使用することはできません。");
    }

    /**
     * 設定数の登録上限のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $company_id 企業ID
     */
    protected static function max_count_validation(\Validation $validation, string $company_id) {
        $validation->add('setting_count', '更新設定数')
            ->add_rule(['setting_count' => function () use ($company_id) {
                // 設定数が上限を超えていないかチェックする
                $settings = Model_Bulkupdatesetting::findAll(
                    [
                        'company_id' => $company_id,
                        'temporary' => 0,
                    ]
                );
                if (count($settings) >= Model_Bulkupdatesetting::SETTING_COUNT_MAX) {
                    return false;
                }
                return true;
            }], Model_Bulkupdatesetting::SETTING_COUNT_MAX);
    }

    /**
     * 設定項目数の登録上限のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param int $column_count 設定項目数
     */
    protected static function max_column_count_validation(\Validation $validation, int $column_count) {
        $validation->add('setting_column_count', '更新設定項目数')
            ->add_rule(['setting_column_count' => function () use ($column_count) {
                // 設定項目数が上限を超えていないかチェックする
                if ($column_count > Model_Bulkupdatecolumn::SETTING_COUNT_MAX) {
                    return false;
                }
                return true;
            }]);
    }

    /**
     * 更新する項目のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $validation_name バリデーションを行うname
     * @param array $match_collection 選択できる項目の配列
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $select_colums 選択しているカラム情報の配列
     * @param string $column_id 選択しているカラムID
     */
    protected static function select_column_validation(\Validation $validation, string $validation_name, array $match_collection, array $receive_order_columns, array $select_colums, string $column_id) {
        $fieldset_field = $validation->add($validation_name, '更新する項目');
        $fieldset_field
            ->add_rule('required')
            ->add_rule('match_collection', $match_collection);

            if(!isset($receive_order_columns[$column_id])) {
                // 更新する項目が存在しない場合は後続のバリデーションはスキップする
                return;
            }
            $receive_order_column = $receive_order_columns[$column_id];

            $fieldset_field->set_label($receive_order_column->logical_name);

            if($receive_order_column->is_payment()) {
                // 支払関連項目の場合

                if($receive_order_column->is_payment_method_id()) {
                    // 支払方法の場合
                    self::select_column_payment_method_validation(
                        $fieldset_field,
                        $receive_order_columns,
                        $select_colums
                    );
                } else {
                    // 支払方法以外の支払関連項目の場合
                    self::select_column_payment_validation(
                        $fieldset_field,
                        $receive_order_columns,
                        $select_colums
                    );
                }

            }

            if($receive_order_column->is_order_amount()) {
                // 受注金額関連項目の場合

                if($receive_order_column->is_total_amount()) {
                    // 総合計の場合
                    self::select_column_total_amount_validation(
                        $fieldset_field,
                        $receive_order_columns,
                        $select_colums
                    );
                } else {
                    // 総合計以外の受注金額関連項目の場合
                    self::select_column_order_amount_validation(
                        $fieldset_field,
                        $receive_order_columns,
                        $select_colums
                    );
                }

            }

    }

    /**
     * 支払方法のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $select_colums 選択しているカラム情報の配列
     */
    private static function select_column_payment_method_validation(\Fieldset_Field $fieldset_field, array $receive_order_columns, array $select_colums) {
        $fieldset_field
            ->add_rule(['payment_method' => function () use ($receive_order_columns, $select_colums) {
                // 支払方法を選択している場合は支払関連は同時に選択不可とする
                foreach ($select_colums as $index => $column_id) {
                    if(!isset($receive_order_columns[$column_id])) {
                        continue;
                    }
                    $receive_order_column = $receive_order_columns[$column_id];
                    if (!$receive_order_column->is_payment_method_id() && $receive_order_column->is_payment()) {
                        return false;
                    }
                }
                return true;
            }]);
    }

    /**
     * 支払関連項目のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $select_colums 選択しているカラム情報の配列
     */
    private static function select_column_payment_validation(\Fieldset_Field $fieldset_field, array $receive_order_columns, array $select_colums) {
        $fieldset_field
            ->add_rule(['payment' => function () use ($receive_order_columns, $select_colums) {
                // 支払関連項目を選択している場合は支払方法は同時に選択不可とする
                foreach ($select_colums as $index => $column_id) {
                    if(!isset($receive_order_columns[$column_id])) {
                        continue;
                    }
                    $receive_order_column = $receive_order_columns[$column_id];
                    if ($receive_order_column->is_payment_method_id()) {
                        return false;
                    }
                }
                return true;
            }]);
    }

    /**
     * 総合計のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $select_colums 選択しているカラム情報の配列
     */
    private static function select_column_total_amount_validation(\Fieldset_Field $fieldset_field, array $receive_order_columns, array $select_colums) {
        $fieldset_field
            ->add_rule(['total_amount' => function () use ($receive_order_columns, $select_colums) {
                // 総合計を選択している場合は受注金額関連は同時に選択不可とする
                foreach ($select_colums as $index => $column_id) {
                    if(!isset($receive_order_columns[$column_id])) {
                        continue;
                    }
                    $receive_order_column = $receive_order_columns[$column_id];
                    if (!$receive_order_column->is_total_amount() && $receive_order_column->is_order_amount()) {
                        return false;
                    }
                }
                return true;
            }]);
    }

    /**
     * 受注金額関連項目のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $select_colums 選択しているカラム情報の配列
     */
    private static function select_column_order_amount_validation(\Fieldset_Field $fieldset_field, array $receive_order_columns, array $select_colums) {
        $fieldset_field
            ->add_rule(['order_amount' => function () use ($receive_order_columns, $select_colums) {
                // 受注金額関連項目を選択している場合は総合計は同時に選択不可とする
                foreach ($select_colums as $index => $column_id) {
                    if(!isset($receive_order_columns[$column_id])) {
                        continue;
                    }
                    $receive_order_column = $receive_order_columns[$column_id];
                    if ($receive_order_column->is_total_amount()) {
                        return false;
                    }
                }
                return true;
            }]);
    }

    /**
     * 更新方法のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $validation_name バリデーションを行うname
     * @param string $column_name バリデーションを行う項目名（バリデーションエラー時に表示される項目名）
     * @param array $match_collection 選択できる項目の配列
     */
    protected static function select_update_validation(\Validation $validation, string $validation_name, string $column_name, array $match_collection) {
        $validation->add($validation_name, $column_name . 'の更新方法')
            ->add_rule('required')
            ->add_rule('match_collection', $match_collection);
    }

    /**
     * 伝票に関する高度な更新設定のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     */
    protected static function option_validation(\Validation $validation) {
        $validation->add(Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME, __('page.updatesetting.allow_update_shipment_confirmed.title'))
            ->add_rule('required')
            ->add_rule('match_collection', ['0', '1']);

        $validation->add(Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME, __('page.updatesetting.allow_update_yahoo_cancel.title'))
            ->add_rule('required')
            ->add_rule('match_collection', ['0', '1']);

        $validation->add(Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME, __('page.updatesetting.allow_optimistic_lock_update_retry.title'))
            ->add_rule('required')
            ->add_rule('match_collection', ['0', '1']);
        $validation->add(Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT, __('page.updatesetting.allow_reflect_order_amount.title'))
            ->add_rule('required')
            ->add_rule('match_collection', ['0', '1']);
    }

    /**
     * 追記の場合のバリデーション（空文字は追記不可）
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param string $update_value 更新値
     * @param string $select_update 更新方法
     */
    protected static function addwrite_validation(\Fieldset_Field $fieldset_field, string $update_value, string $select_update) {
        $fieldset_field->add_rule(['add_text' => function() use ($update_value, $select_update) {
                if ($update_value === '' && $select_update === Model_Updatemethod::ADDWRITE) {
                    return false;
                }else{
                    return true;
                }
            }]);
    }

    /**
     * マスタ型のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $validation_name バリデーションを行うname
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     * @param Utility_Master $master マスタデータオブジェクト
     * @param array $select_colums
     * @return void 内部的にvalidationにルールを追加している
     */
    protected static function master_validation(\Validation $validation, string $validation_name, Model_Receiveordercolumn $receive_order_column, string $update_value, Utility_Master $master, array $select_colums) {
        $fieldset_field = $validation->add($validation_name, $receive_order_column->logical_name);
        $fieldset_field->add_rule('required')
            ->add_rule(['master' => function() use ($update_value, $receive_order_column, $master) {
                $master_data = $master->get($receive_order_column->master_name);
                return isset($master_data[$update_value]);
            }]);

        if ($receive_order_column->is_delivery()) {
            // 発送方法の項目の場合、発送方法タイプ別区分がすべてあるかチェックする
            $delivery_receive_order_columns = Model_Receiveordercolumn::findAll(
                [
                    ['delivery', '=', '1'],
                    ['disabled', '=', '0'],
                    ['master_name', '!=', 'delivery']
                ]);
            $delivery_receive_order_column_list = [];
            foreach ($delivery_receive_order_columns as $delivery_receive_order_column) {
                $delivery_receive_order_column_list[$delivery_receive_order_column->id] = $delivery_receive_order_column->logical_name;
            }
            // 発送方法タイプ別区分のすべてのIDが$select_columsにあるかチェックする（足りない項目がある場合は差分が出る）
            $diff = array_diff(
                array_keys($delivery_receive_order_column_list),
                array_intersect(array_keys($delivery_receive_order_column_list), array_values($select_colums))
            );
            // 足りない項目名を配列で取得する
            $delivery_receive_order_column_names = [];
            foreach ($diff as $delivery_receive_order_column_id) {
                $delivery_receive_order_column_names[] = $delivery_receive_order_column_list[$delivery_receive_order_column_id];
            }

            $fieldset_field->add_rule(['delivery' => function() use ($diff) {
                // 差分がある場合は項目が足りていない
                return empty($diff);
            }], implode(', ', $delivery_receive_order_column_names));
        }
     }

    /**
     * 文字入力型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     */
    protected static function text_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column) {
        if ($receive_order_column->input_min_length !== '0') {
            $fieldset_field->add_rule('required');
        }
        $fieldset_field->add_rule('min_length', $receive_order_column->input_min_length)
            ->add_rule('max_length', $receive_order_column->input_max_length);
    }

    /**
     * 数値型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     * @param string $select_update 更新方法
     */
    protected static function number_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value, string $select_update) {
        $fieldset_field->add_rule('required')
            // 数値は範囲指定でマイナスも小数も入力できるようにしておく
            // NOTE: 入力値の下限上限はメイン機能での入力値の下限上限に合わせた
            // 計算に使う値なので必ずしも正しくないがこれだけの範囲を確保しておけばとりあえず十分と判断
            ->add_rule('numeric_between', \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN, \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX, '数値')
            ->add_rule('min_length', $receive_order_column->input_min_length)
            ->add_rule('max_length', $receive_order_column->input_max_length)
            ->add_rule(['zero_division' => function() use ($update_value, $select_update) {
                // 更新値0で更新方法が除算であれば0除算なのでバリデーションエラーとする
                if($update_value === '0' && $select_update === Model_Updatemethod::DIVISION){
                    return false;
                } else {
                    return true;
                }
            }])
            ->add_rule(['only_number' => function() use ($update_value) {
                // 更新値が数値型として意図していない値であればバリデーションエラーとする
                // NOTE: fuelのvalid_string:numericだとマイナスや小数点が許容されず、
                // numeric_betweenだと指数表記eも通ってしまうため自前でバリデーションする
                // 数字, 小数点用のドット, マイナス表記用の-のみを許容する、それ以外がある場合はエラー
                if(preg_match('/[^0-9-.]/', $update_value) !== 1){
                    return true;
                } else {
                    return false;
                }
            }]);
    }

    /**
     * email型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     */
    protected static function email_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        if ($update_value !== '') {
            $fieldset_field->add_rule('required')
                ->add_rule('valid_email')
                ->add_rule('min_length', $receive_order_column->input_min_length)
                ->add_rule('max_length', $receive_order_column->input_max_length);
        }
    }

    /**
     * ブール型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     */
    protected static function bool_validation(\Fieldset_Field $fieldset_field) {
        $fieldset_field->add_rule('required')
            ->add_rule('match_collection', ['0', '1']);
    }

    /**
     * タグ型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     */
    protected static function tag_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        if ($update_value !== '') {
            $fieldset_field->add_rule('required')
                ->add_rule('min_length', $receive_order_column->input_min_length)
                ->add_rule('max_length', $receive_order_column->input_max_length)
                ->add_rule(['tag_format' => function () use ($update_value) {
                    preg_match_all('/\[.*?\]/', $update_value, $m);
                    $check_text = '';
                    foreach ($m[0] as $value) {
                        if ($value === '[]') {
                            return false;
                        }
                        $check_text .= $value;
                    }
                    return $update_value === $check_text &&
                        substr_count($update_value, '[') === substr_count($update_value, ']') && // 括弧の数をチェックする
                        substr_count($update_value, '[') === count($m[0]); // 括弧の数とタグの数が一致しているかチェックする
                }])
                ->add_rule(['tag_length' => function () use ($update_value) {
                    preg_match_all('/\[(.*?)\]/', $update_value, $m);
                    foreach ($m[1] as $tag_name) {
                        if (mb_strlen($tag_name) > self::TAG_NAME_MAX_LENGTH) {
                            return false;
                        }
                    }
                }], self::TAG_NAME_MAX_LENGTH)
                ->add_rule(['tag_match_pattern' => function () use ($update_value) {
                    preg_match_all('/\[(.*?)\]/', $update_value, $m);
                    foreach ($m[1] as $tag_name) {
                        if (!preg_match("/^(?!.*(\'|\"|<|\[|\])).*$/", $tag_name)) {
                            return false;
                        }
                    }
                }]);
        }
    }

    /**
     * 電話番号型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     */
    protected static function telephone_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        if ($update_value !== '') {
            $fieldset_field->add_rule('required')
                ->add_rule('min_length', $receive_order_column->input_min_length)
                ->add_rule('max_length', $receive_order_column->input_max_length)
                ->add_rule('valid_string', 'numeric', 'ハイフン無しの電話番号');
        }
    }

    /**
     * 郵便番号型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     */
    protected static function zip_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        if ($update_value !== '') {
            $fieldset_field->add_rule('required')
                ->add_rule('min_length', $receive_order_column->input_min_length)
                ->add_rule('max_length', $receive_order_column->input_max_length)
                ->add_rule('valid_string', 'numeric', 'ハイフン無しの郵便番号');
        }
    }

    /**
     * 日付型のバリデーション
     *
     * @param Fieldset_Field $fieldset_field フィールド情報
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     */
    protected static function date_validation(\Fieldset_Field $fieldset_field, Model_Receiveordercolumn $receive_order_column, string $update_value) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        // ただし受注日は空更新を許可していないので受注日は必須バリデーションとする
        if ($update_value !== '' || $receive_order_column->is_order_date()) {
            $fieldset_field->add_rule('required')
                ->add_rule('valid_date', 'Y/m/d');
        }
    }

    /**
     * 発送方法別区分のバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param string $validation_name バリデーションを行うname
     * @param Model_Receiveordercolumn $receive_order_column 項目情報オブジェクト
     * @param string $update_value 更新値
     * @param Utility_Master $master マスタデータオブジェクト
     * @param string $delivery_id 発送方法ID
     */
    protected static function forwarding_agent_validation(\Validation $validation, string $validation_name, Model_Receiveordercolumn $receive_order_column, string $update_value, Utility_Master $master, string $delivery_id) {
        // 更新値が空文字を許可しているのでその場合はバリデーション不要
        if ($update_value !== '') {
            $validation->add($validation_name, $receive_order_column->logical_name)
                ->add_rule('required')
                ->add_rule(['forwarding_agent' => function () use ($delivery_id) {
                    // 発送方法別区分を選択している場合は必ず発送方法も選択しているはず
                    return !empty($delivery_id);
                }])
                ->add_rule(['master' => function() use ($update_value, $receive_order_column, $delivery_id, $master) {
                    // マスタにない値を選択している場合はバリデーションエラー
                    if (empty($delivery_id)) {
                        return false;
                    }
                    $master_datas = $master->get_forwarding_agent(true, $delivery_id, $receive_order_column->master_name);
                    foreach ($master_datas as $master_data) {
                        if ($master_data->get_id() === $update_value) {
                            return true;
                        }
                    }
                    return false;
                }]);
        }
    }

    /**
     * 発送方法関連のシールの重複チェックバリデーション
     *
     * @param Validation $validation バリデーションオブジェクト
     * @param array $receive_order_columns 全カラム情報の配列
     * @param array $update_values 入力している更新値一覧
     */
    protected static function forwarding_agent_seal_validation(\Validation $validation, array $receive_order_columns, array $update_values) {
        $validation->add('seal')
            ->add_rule(['seal' => function() use ($receive_order_columns, $update_values) {
            $seal_values = [];
            foreach ($update_values as $column_id => $value) {
                if (isset($receive_order_columns[$column_id]) &&
                    $receive_order_columns[$column_id]->is_seal() &&
                    $value !== ''
                ) {
                    if (in_array($value, $seal_values)) {
                        return false;
                    }
                    $seal_values[] = $value;
                }
            }
            return true;
        }]);
    }

}