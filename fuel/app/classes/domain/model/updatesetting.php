<?php
/**
 * 更新設定に関するロジッククラス
 */
class Domain_Model_Updatesetting{

    // とりあえずのリトライ回数なので適宜変更してよい
    const MAX_RETRY_COUNT = 3;

    // 画面で選択した「選択する項目」の値のPOSTデータ名
    const SELECT_COLUMN_ELEMENT_NAME                = 'select_column';
    // 画面で選択したマスタデータの値のPOSTデータ名
    const SELECT_MASTER_ELEMENT_NAME                = 'select_master';
    // 画面で選択した更新方法の値のPOSTデータ名
    const SELECT_UPDATE_ELEMENT_NAME                = 'select_update';
    // 画面で入力した更新値のPOSTデータ名
    const UPDATE_VALUE_ELEMENT_NAME                 = 'update_value';
    // 画面でチェックした「出荷確定済の伝票を更新対象にする」のPOSTデータ名
    const UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME    = 'allow_update_shipment_confirmed';
    // 画面でチェックした「Yahoo!ショッピング]伝票のキャンセル区分を更新対象にする」のPOSTデータ名
    const UPDATE_YAHOO_CANCEL_ELEMENT_NAME          = 'allow_update_yahoo_cancel';
    // 画面でチェックした「一括更新処理中に別担当者が更新した伝票へ再実行を行う」のPOSTデータ名
    const OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME       = 'allow_optimistic_lock_update_retry';
    // 画面でチェックした「受注金額関連項目更新値の総合計への反映をする」のPOSTデータ名
    const REFLECT_ORDER_AMOUNT                      = 'allow_reflect_order_amount';

    // 発送関連の項目の値
    const SELECT_COLUMN_DELIVERY_VALUE = 'delivery';

    // メモリ上限値
    // デフォルトのメモリ上限では不足する箇所に対して限定的にメモリ上限を上げる際に使用
    // NOTE: 暫定値なので都度調整して良い
    const MEMORY_LIMIT = '512M';

    /**
     * 特定のcodeの場合にメッセージを定義した別のメッセージに変更する
     *
     * @var array
     */
    protected static $notice_change_message_for_code = [
        Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE => "別担当者によって受注情報が変更されております。お手数ですが、再度実行してください。\n更新設定の「伝票に関する高度な更新設定」の「一括更新処理中に別担当者が更新した伝票へ再実行を行う」にチェックを入れて実行すると、自動的に再度実行が行われます。",
        Client_Neapi::ERROR_CODE_STATUS_MAIL_IMPORTED => "受注状態が受注メール取込済の伝票は[receive_order_cancel_type_id][receive_order_worker_text]以外は更新できません。",
    ];

    /**
     * 一括更新設定を論理削除する
     * bulk_update_columnsやoriginal_bulk_update_setting_idで参照されている部分に関しては何もしない
     *
     * @param string $company_id 一括更新設定を所有する企業ID
     * @param string $bulk_update_setting_id 一括更新設定ID
     * @throws Exception
     */
    public static function delete(string $company_id, string $bulk_update_setting_id) {

        $bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);

        if(is_null($bulk_update_setting)) {
            // 削除しようとした対象がすでに存在しなかった場合に、
            // エラーを通知する用途がないため、処理終了
            return;
        }

        // 論理削除する
        $bulk_update_setting->delete();
    }

    /**
     * 一括更新設定を物理削除する
     * bulk_update_columnsで参照されている場合は該当columnsを削除
     * original_bulk_update_setting_idで参照されている場合はそのidをnullで上書き
     * 主にtemporary=1の仮レコードを削除する用
     *
     * @param string $company_id 一括更新設定を所有する企業ID
     * @param string $bulk_update_setting_id 一括更新設定ID
     * @throws Exception
     */
    public static function hard_delete(string $company_id, string $bulk_update_setting_id) {

        $bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);

        if(is_null($bulk_update_setting)) {
            // 削除しようとした対象がすでに存在しなかった場合に、
            // エラーを通知する用途がないため、処理終了
            return;
        }

        $db = Database_Connection::instance();
        $db->start_transaction();

        try {
            // 一括更新設定の更新項目を削除
            // （外部キー制約があるため先に削除する必要がある）
            Model_Bulkupdatecolumn::delete_by_bulk_update_setting_id($bulk_update_setting_id);

            // 削除しようとしているidがoriginal_bulk_update_setting_idに入っていると外部キー制約で削除できないためそれらをまとめてnullにすることで対応する
            // null更新に失敗した場合外部キー制約で例外になりcatchで処理をする
            Model_Bulkupdatesetting::update_null_original_bulk_update_setting_id($company_id, $bulk_update_setting_id);

            $bulk_update_setting->hard_delete();

            $db->commit_transaction();

        } catch (Exception $e) {
            $db->rollback_transaction();
            Log::exception($e);
            throw $e;
        }
    }


    /**
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param string $bulk_update_setting_id 更新設定ID
     * @param string $name 複製時の設定名
     * @return array [bool:成功/失敗, string:成功/失敗メッセージ]
     */
    public static function copy(string $company_id, string $user_id, string $bulk_update_setting_id, string $name): array {
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id($company_id, $bulk_update_setting_id);
        // 指定の更新設定が存在しない場合はemptyのエラーを返す
        if(empty($params)) return [false, __em('bulk_update_setting_empty')];

        $params['bulk_update_setting_id'] = 0;
        $params['name'] = $name;
        $bulk_update_setting_id = self::save($company_id, $user_id, $params);
        if ($bulk_update_setting_id === '0') {
            return [false, __em('copy')];
        } else {
            return [true, __sm('copy')];
        }
    }

    /**
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param string $bulk_update_setting_id 更新設定ID
     * @param string $name 設定名
     * @return array [bool:成功/失敗, string:成功/失敗メッセージ]
     */
    public static function update_name(string $company_id, string $user_id, string $bulk_update_setting_id, string $name): array {
        $model_bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);
        // レコードが見つからない場合はエラー（別画面で該当設定が既に削除されていた場合など）
        if(is_null($model_bulk_update_setting)) return [false, __em('bulk_update_setting_empty')];

        $before_name = $model_bulk_update_setting->name;
        $model_bulk_update_setting->name = $name;
        $model_bulk_update_setting->last_updated_user_id = $user_id;
        $model_bulk_update_setting->updated_at = date('Y-m-d H:i:s');
        if($model_bulk_update_setting->save()){
            return [true, __sm('name_update', [$before_name, $name])];
        } else {
            return [false, __em('name_update')];
        }
    }

    /**
     * バリデーションエラーがあった場合の画面再描画用のオブジェクトを取得する
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param array $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatesetting
     * @throws FuelException
     */
    public static function get_setting_for_validation_error(string $company_id, string $user_id, array $post_params) : Model_Bulkupdatesetting {

        $error_setting = self::_create_setting($company_id, $user_id, $post_params, false, false);

        // 一括更新設定の別名保存のときのバリデーションエラーでは元の設定の作成日時、最終更新日、作成者、最終更新者を入れる
        // 一括更新の実行ではbuld_update_setting_idは空の文字列が返ってくる
        if (!empty($post_params['bulk_update_setting_id'])) {
            $setting = Model_Bulkupdatesetting::get_setting($company_id, $post_params['bulk_update_setting_id']);
            $error_setting->name = $setting->name;
            $error_setting->updated_at = $setting->updated_at;
            $error_setting->created_at = $setting->created_at;
            $error_setting->created_user = $setting->created_user;
            $error_setting->last_updated_user = $setting->last_updated_user;
        }
        return $error_setting;
    }

    /**
     * 更新設定情報を保存し、そのIDを返す
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param array $post_params 更新時のPOSTパラメータ
     * @param bool $is_temporary 一時データかどうか(temporary=1で保存するかどうか)
     * @return string 保存したbulk_update_settings.id
     * @throws FuelException
     */
    public static function save(string $company_id, string $user_id, array $post_params, bool $is_temporary = false) : string {

        Log::notice_ex('start bulk_update_setting save.', ['company_id' => $company_id, 'user_id' => $user_id, 'post_params' => $post_params, 'is_temporary' => $is_temporary]);

        $db = Database_Connection::instance();
        $db->start_transaction();

        try {
            $bulk_update_setting = self::_create_setting($company_id, $user_id, $post_params, $is_temporary);

            $db->commit_transaction();
            $execute_id = $bulk_update_setting->id;
            Log::notice_ex('end bulk_update_setting save.', ['execute_id' => $execute_id, 'company_id' => $company_id, 'user_id' => $user_id, 'post_params' => $post_params, 'is_temporary' => $is_temporary]);
            return $execute_id;
        } catch (Exception $e) {
            $db->rollback_transaction();
            Log::exception($e);
            throw $e;
        }
    }

    /**
     * 更新設定情報オブジェクトを作成しそれを返す
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param array $post_params 更新時のPOSTパラメータ
     * @param bool $is_temporary 一時データかどうか
     * @param bool $is_save 作成したオブジェクトを保存するかどうか
     * @return Model_Bulkupdatesetting 作成した更新設定情報オブジェクト
     * @throws Exception
     * @throws FuelException
     * @throws InvalidArgumentException
     */
    private static function _create_setting(string $company_id, string $user_id, array $post_params, bool $is_temporary = false, $is_save = true) : Model_Bulkupdatesetting {
        if (!isset($post_params[self::SELECT_COLUMN_ELEMENT_NAME])) {
            throw new InvalidArgumentException(__em('update_column_empty'));
        }
        if (!isset($post_params[self::SELECT_MASTER_ELEMENT_NAME]) && !isset($post_params[self::UPDATE_VALUE_ELEMENT_NAME])) {
            throw new InvalidArgumentException(__em('update_value_empty'));
        }
        if (!isset($post_params[self::SELECT_UPDATE_ELEMENT_NAME])) {
            throw new InvalidArgumentException(__em('update_method_empty'));
        }

        $post_select_columns = $post_params[self::SELECT_COLUMN_ELEMENT_NAME];
        $bulk_update_setting_id = !empty($post_params[BULK_UPDATE_SETTING_ID]) ? $post_params[BULK_UPDATE_SETTING_ID] : 0;
        $setting_name = isset($post_params['name']) ? $post_params['name'] : '';

        // create=1であれば$bulk_update_setting_idがあっても新規作成する
        $is_create = isset($post_params['create']) && $post_params['create'] === '1' ? true : false;

        if ($bulk_update_setting_id === 0 || $is_temporary || $is_create) {
            /**
             * 新規登録処理
             */
            Log::notice_ex('new bulk_update_setting.', ['company_id' => $company_id, 'user_id' => $user_id, 'post_params' => $post_params, 'is_temporary' => $is_temporary, 'is_save' => $is_save]);

            // 更新設定情報オブジェクトを取得
            $bulk_update_setting = self::_get_insert_bulk_update_setting($company_id, $user_id, $setting_name, $post_params);
            if ($is_temporary) {
                $bulk_update_setting->temporary = '1';
                if ($bulk_update_setting_id !== 0) {
                    $bulk_update_setting->original_bulk_update_setting_id = $bulk_update_setting_id;
                }
            }

            // 更新項目情報オブジェクトを設定
            $bulkupdate_columns = [];
            foreach ($post_select_columns as $select_colum_id) {
                $bulkupdate_columns[] = self::_get_create_bulk_update_column($select_colum_id, $post_params);
            }
            $bulk_update_setting->bulk_update_columns = $bulkupdate_columns;
            if ($is_save) {
                $bulk_update_setting->save();
            }
        }else{
            /**
             * 更新処理
             */
            Log::notice_ex('update bulk_update_setting.', ['company_id' => $company_id, 'user_id' => $user_id, 'post_params' => $post_params, 'is_temporary' => $is_temporary, 'is_save' => $is_save]);

            // 更新設定情報オブジェクトを取得
            $bulk_update_setting = self::_get_update_bulk_update_setting($company_id, $user_id, $setting_name, $bulk_update_setting_id, $post_params);
            if ($is_save) {
                // cascade_save=trueになっている場合、bulk_update_columnを削除した後にsaveを実行すると、削除したレコードが復活してしまうため、bulk_update_settingsだけ先にupdateする
                $bulk_update_setting->updated_at = date('Y-m-d H:i:s');
                $bulk_update_setting->save();
            }

            // 更新項目情報オブジェクトを設定
            $select_columns = [];
            foreach ($post_select_columns as $select_column_id) {
                // array_searchは遅いので、issetで検索できるように整形する
                $select_columns[$select_column_id] = '';
            }
            foreach ($bulk_update_setting->bulk_update_columns as $index => $bulk_update_column) {
                $select_colum_id = $bulk_update_column->receive_order_column_id;
                if (isset($select_columns[$select_colum_id])) {
                    // すでにある項目
                    $update_bulk_update_column = self::_get_update_bulk_update_column($bulk_update_column, $post_params);
                    unset($select_columns[$select_colum_id]);
                    if ($is_save) {
                        $update_bulk_update_column->save();
                    }
                }else{
                    // なくなった項目
                    unset($bulk_update_setting->bulk_update_columns[$index]);
                    if ($is_save) {
                        $bulk_update_column->delete();
                    }
                }
            }
            foreach (array_keys($select_columns) as $select_colum_id) {
                // 新規追加された項目
                $new_bulk_update_column = self::_get_create_bulk_update_column($select_colum_id, $post_params, $bulk_update_setting->id);
                $bulk_update_setting->bulk_update_columns[] = $new_bulk_update_column;
                if ($is_save) {
                    $new_bulk_update_column->save();
                }
            }
        }
        return $bulk_update_setting;
    }

    /**
     * insert用のModel_Bulkupdatesettingオブジェクトを作成して返す
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param string $name 更新設定名
     * @param array $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatesetting
     */
    private static function _get_insert_bulk_update_setting(string $company_id, string $user_id, string $name, array $post_params) : Model_Bulkupdatesetting {
        $bulk_update_setting = new Model_Bulkupdatesetting();
        $bulk_update_setting->company_id = $company_id;
        $bulk_update_setting->created_user_id = $user_id;
        $bulk_update_setting->temporary = '0';
        return self::_get_bulk_update_setting_and_set_params($bulk_update_setting, $name, $user_id, $post_params);
    }

    /**
     * update用のModel_Bulkupdatesettingオブジェクトを取得して返す
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param string $name 更新設定名
     * @param string $bulk_update_setting_id 取得するbulk_update_settings.id
     * @param array $post_params $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatesetting
     * @throws FuelException
     */
    private static function _get_update_bulk_update_setting(string $company_id, string $user_id, string $name, string $bulk_update_setting_id, array $post_params) : Model_Bulkupdatesetting {
        $bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);
        return self::_get_bulk_update_setting_and_set_params($bulk_update_setting, $name, $user_id, $post_params);
    }

    /**
     * Model_Bulkupdatesettingに、insert/updateどちらの場合でも更新できる共通データを設定して返す
     *
     * @param Model_Bulkupdatesetting $bulkupdate_setting 更新設定情報
     * @param string $name 更新設定名
     * @param string $user_id ユーザーID
     * @param array $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatesetting
     */
    private static function _get_bulk_update_setting_and_set_params(Model_Bulkupdatesetting $bulk_update_setting, string $name, string $user_id, array $post_params) : Model_Bulkupdatesetting {
        $bulk_update_setting->name = $name;

        $bulk_update_setting->allow_update_shipment_confirmed = isset($post_params[self::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME]) ? $post_params[self::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME] : '0';
        $bulk_update_setting->allow_update_yahoo_cancel = isset($post_params[self::UPDATE_YAHOO_CANCEL_ELEMENT_NAME]) ? $post_params[self::UPDATE_YAHOO_CANCEL_ELEMENT_NAME] : '0';
        $bulk_update_setting->allow_optimistic_lock_update_retry = isset($post_params[self::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME]) ? $post_params[self::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME] : '0';
        $bulk_update_setting->allow_reflect_order_amount = isset($post_params[self::REFLECT_ORDER_AMOUNT]) ? $post_params[self::REFLECT_ORDER_AMOUNT] : '0';
        $bulk_update_setting->last_updated_user_id = $user_id;
        return $bulk_update_setting;
    }

    /**
     * insert用のModel_Bulkupdatecolumnオブジェクトを作成して返す
     *
     * @param string $select_colum_id 選択した項目ID
     * @param array $post_params 更新時のPOSTパラメータ
     * @param string $bulk_update_setting_id 更新設定情報ID
     * @return Model_Bulkupdatecolumn
     */
    private static function _get_create_bulk_update_column(string $select_colum_id, array $post_params, string $bulk_update_setting_id = null) : Model_Bulkupdatecolumn {
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->bulk_update_setting_id = $bulk_update_setting_id;
        $bulk_update_column->receive_order_column_id = $select_colum_id;
        return self::_get_bulk_update_column_and_set_params($bulk_update_column, $post_params);
    }

    /**
     * update用のModel_Bulkupdatecolumnオブジェクトを取得して返す
     *
     * @param Model_Bulkupdatecolumn $bulk_update_column 更新項目情報
     * @param array $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatecolumn
     */
    private static function _get_update_bulk_update_column(Model_Bulkupdatecolumn $bulk_update_column, array $post_params) : Model_Bulkupdatecolumn {
        return self::_get_bulk_update_column_and_set_params($bulk_update_column, $post_params);
    }

    /**
     * Model_Bulkupdatecolumnに、insert/updateどちらの場合でも更新できる共通データを設定して返す
     *
     * @param Model_Bulkupdatecolumn $bulk_update_column 更新項目情報
     * @param array $post_params 更新時のPOSTパラメータ
     * @return Model_Bulkupdatecolumn
     * @throws InvalidArgumentException
     */
    private static function _get_bulk_update_column_and_set_params(Model_Bulkupdatecolumn $bulk_update_column, array $post_params) : Model_Bulkupdatecolumn {

        $post_select_update =  isset($post_params[self::SELECT_UPDATE_ELEMENT_NAME]) ? $post_params[self::SELECT_UPDATE_ELEMENT_NAME] : [];
        $post_select_master =  isset($post_params[self::SELECT_MASTER_ELEMENT_NAME]) ? $post_params[self::SELECT_MASTER_ELEMENT_NAME] : [];
        $post_update_value =  isset($post_params[self::UPDATE_VALUE_ELEMENT_NAME]) ? $post_params[self::UPDATE_VALUE_ELEMENT_NAME] : [];

        $select_colum_id = $bulk_update_column->receive_order_column_id;
        if (!isset($post_select_update[$select_colum_id])) {
            throw new InvalidArgumentException(__em('update_method_empty'));
        }
        $bulk_update_column->update_method_id = $post_select_update[$select_colum_id];
        if (isset($post_select_master[$select_colum_id])) {
            $bulk_update_column->update_value = $post_select_master[$select_colum_id];
        }else if (isset($post_update_value[$select_colum_id])) {
            $bulk_update_column->update_value = $post_update_value[$select_colum_id];
        }else{
            throw new InvalidArgumentException(__em('update_value_empty'));
        }
        return $bulk_update_column;
    }

    /**
     * キューの登録を実行する
     *
     * @param string $extension_execution_id 拡張機能実行ID
     * @param string $company_id 企業ID
     * @param string $user_id 実行者のユーザーID
     * @param array $params バリデーションを実行した設定情報
     * @param array $exclude_orders 除外対象の伝票のid一覧
     * @return Domain_Value_Enqueresult
     * @throws Exception
     * @throws FuelException
     * @throws InvalidArgumentException
     * @throws Aws\Exception\AwsException
     */
    public function execution_enque(string $extension_execution_id, string $company_id, string $user_id, array $params, array $exclude_orders) : Domain_Value_Enqueresult {
        if (!isset($params[BULK_UPDATE_SETTING_ID])) {
            throw new InvalidArgumentException(__em('bulk_update_setting_id_empty'));
        }

        if (!isset($params[self::SELECT_COLUMN_ELEMENT_NAME]) || count($params[self::SELECT_COLUMN_ELEMENT_NAME]) === 0) {
            throw new InvalidArgumentException(__em('update_value_empty'));
        }

        // タスク登録済みの実行IDは実行できない
        if (Model_Executionbulkupdatesetting::findOne(['extension_execution_id' => $extension_execution_id])) {
            return new Domain_Value_Enqueresult(false, null, __em('extension_execution_id_executed'));
        }
        // 対象となる伝票をチェックする
        $search_result = static::request_receiveorder_search($this->get_client_neapi($user_id, false), $extension_execution_id);
        if($search_result['result'] !== \Client_Neapi::RESULT_SUCCESS){
            return new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_serach_error'));
        }
        if($search_result['count'] <= 0){
            return new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_serach_empty'));
        }
        // 対象伝票数と除外伝票数が一致している場合、全件除外になっているのでエラーとして返す
        if((int)$search_result['count'] === count($exclude_orders)){
            return new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_all_exclude'));
        }
        $receive_orders = $search_result['data'];

        Log::notice_ex('execution_enque start', ['company_id' => $company_id, 'user_id' => $user_id, 'params' => $params, 'extension_execution_id' => $extension_execution_id]);
        $db = Database_Connection::instance();
        $db->start_transaction();

        try {
            // 更新設定情報オブジェクトの登録
            $execution_bulk_update_setting = $this->get_execution_bulk_update_setting(
                $company_id,
                $user_id,
                $extension_execution_id,
                $params,
                $receive_orders);
            // 除外レコードの登録するにはidが必要なため、saveを行う
            $execution_bulk_update_setting->save();

            // 除外レコードの登録
            // キューを入れる前にexcludeのレコードを入れないとタスクレコード→キュー登録→キュー監視が先に走ってしまうと除外されずに実行されてしまうのでこのタイミング
            if(!empty($exclude_orders)){
                $exclude_result = static::bulk_insert_excluded_receive_orders($execution_bulk_update_setting, $exclude_orders);
                if(!$exclude_result){
                    $db->rollback_transaction();
                    return new Domain_Value_Enqueresult(false, null, __em('exclude_receiveorder'));
                }
            }

            // 対象伝票数を取得するため変換処理を行う（除外設定の適用、更新設定の適用）
            $convert_result = $this->convert($execution_bulk_update_setting, $receive_orders);
            $target_order_count = count($convert_result->get_update_target_orders());
            if ($target_order_count === 0) {
                // 対象が1件もなかった
                $db->rollback_transaction();
                return new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_empty'));
            }
            $execution_bulk_update_setting->target_order_count = $target_order_count;
            $execution_bulk_update_setting->save();

            $db->commit_transaction();

            // キュー登録
            // NOTE: 必ずexecution_bulk_update_settingのレコードが登録されてからキューを入れること
            // そうしないとレコードが入る前にキュー登録→キュー監視→該当のexecution_bulk_update_settingのレコードが見つからず不正なキュー扱いになってしまう可能性があるため
            static::enque_sqs($company_id, $execution_bulk_update_setting->id);

            Log::notice_ex('execution_enque end', ['company_id' => $company_id, 'user_id' => $user_id, 'params' => $params, 'extension_execution_id' => $extension_execution_id]);

            return new Domain_Value_Enqueresult(true, $execution_bulk_update_setting);

        } catch (Aws\Exception\AwsException $e) {
            // SQSで例外が発生した場合、既にexecution_bulkupdate_settingのレコードはコミット済みのためrollbackが効かない
            // そのためexecution_bulkupdate_settingの後処理をここで行う
            // NOTE: レコードを削除しようと思ったが外部キー制約の関係で簡単に消せないためとりあえず実行済みにする
            // 本当の実行済みと区別がつかなくなるので区別をつけたい場合はexecutedに新しい区分値を用意するなどの対応が必要
            $execution_bulk_update_setting->executed = true;
            $execution_bulk_update_setting->save();
            Log::notice_ex('SQSで例外が発生したため不要になったexecution_bulkupdate_settingのレコードを実行済みにしました', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting->id]);
            Log::exception($e);
            throw $e;
        } catch (Exception $e) {
            $db->rollback_transaction();
            Log::exception($e);
            throw $e;
        }
    }

    /**
     * リクエストキーを作成する
     *
     * @param string $company_id 企業ID
     * @return string
     * @throws ErrorException
     * @throws Exception
     * @throws FuelException
     */
    protected static function get_task_id(string $company_id) : string {
        $request_key = Model_Requestkey::create_request_key_object($company_id);
        return $request_key->get_task_id();
    }

    /**
     * sqsにキュー登録を行う
     *
     * @param string $company_id 企業コード
     * @param string $execution_bulk_update_setting_id 実行ID
     * @throws FuelException
     */
    protected static function enque_sqs(string $company_id, string $execution_bulk_update_setting_id) {
        $sqs = new \Domain_Model_Sqs();
        $company = \Model_Company::findOne(['id' => $company_id]);
        $sqs->enque($execution_bulk_update_setting_id, $company->main_function_id);
    }

    /**
     * 更新設定情報オブジェクトを取得する
     *
     * @param string $company_id 企業ID
     * @param string $user_id ユーザーID
     * @param string $extension_execution_id 拡張機能実行ID
     * @param array $params バリデーションを実行した設定情報
     * @return Model_Executionbulkupdatesetting 更新設定情報オブジェクト
     * @throws ErrorException
     * @throws Exception
     * @throws FuelException
     * @throws InvalidArgumentException
     */
    protected function get_execution_bulk_update_setting(string $company_id, string $user_id, string $extension_execution_id, array $params) : Model_Executionbulkupdatesetting {
        // 更新設定情報オブジェクトを取得
        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->request_key = static::get_task_id($company_id);
        $execution_bulk_update_setting->user_id = $user_id;
        $execution_bulk_update_setting->company_id = $company_id;
        $execution_bulk_update_setting->extension_execution_id = $extension_execution_id;
        $execution_bulk_update_setting->name = $params['name'];
        $execution_bulk_update_setting->allow_update_shipment_confirmed = $params[self::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_update_yahoo_cancel = $params[self::UPDATE_YAHOO_CANCEL_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = $params[self::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_reflect_order_amount = $params[self::REFLECT_ORDER_AMOUNT];

        // 更新項目情報オブジェクトを設定
        $execution_bulk_update_columns = [];
        foreach ($params[self::SELECT_COLUMN_ELEMENT_NAME] as $select_colum_id) {
            $execution_bulk_update_column = new Model_Executionbulkupdatecolumn();
            $execution_bulk_update_column->receive_order_column_id = $select_colum_id;

            $post_select_update =  isset($params[self::SELECT_UPDATE_ELEMENT_NAME]) ? $params[self::SELECT_UPDATE_ELEMENT_NAME] : [];
            $post_select_master =  isset($params[self::SELECT_MASTER_ELEMENT_NAME]) ? $params[self::SELECT_MASTER_ELEMENT_NAME] : [];
            $post_update_value =  isset($params[self::UPDATE_VALUE_ELEMENT_NAME]) ? $params[self::UPDATE_VALUE_ELEMENT_NAME] : [];

            if (!isset($post_select_update[$select_colum_id]) ||
                (!isset($post_select_master[$select_colum_id]) && !isset($post_update_value[$select_colum_id]))) {
                throw new InvalidArgumentException(__em('params'));
            }

            $execution_bulk_update_column->update_method_id = $post_select_update[$select_colum_id];
            if (isset($post_select_master[$select_colum_id])) {
                $execution_bulk_update_column->update_value = $post_select_master[$select_colum_id];
            }else{
                $execution_bulk_update_column->update_value = $post_update_value[$select_colum_id];
            }

            $execution_bulk_update_columns[] = $execution_bulk_update_column;
        }
        $execution_bulk_update_setting->execution_bulk_update_columns = $execution_bulk_update_columns;

        return $execution_bulk_update_setting;
    }

    /**
     * 除外伝票をバルクインサートする
     * @param Model_Executionbulkupdatesetting $execution_bulkupdate_setting 登録後のexecution_bulkupdate_settingのオブジェクト
     * @param array $exclude_orders 除外伝票ID一覧
     * @return bool  インサートに成功したかどうか
     */
    protected static function bulk_insert_excluded_receive_orders(Model_Executionbulkupdatesetting $execution_bulkupdate_setting, array $exclude_orders) : bool {
        $params = [];
        foreach($exclude_orders as $exclude_order){
            $params[] = ['execution_bulk_update_setting_id' => $execution_bulkupdate_setting->id, 'receive_order_id' => $exclude_order];
        }
        if(empty($params)) return false;

        $exclude_result = Model_Excludedreceiveorder::bulk_insert($params);
        return $exclude_result;
    }


    /**
     * execution_bulk_update_settingから対象伝票の取得、更新内容の取得適用、伝票一括更新までを扱う
     * @param  Model_Executionbulkupdatesetting $execution_bulk_update_setting
     * @return Domain_Value_Executionresult 実行結果の各値を持つdomain_valueオブジェクト
     * プロパティ [bool: 処理結果, array: 一括更新APIのレスポンス, int: 一括更新に送った総数, array: 除外した伝票番号とその理由一覧]
     * 例:
     * bool 処理結果 true: 処理成功, false: 処理失敗
     * ["result"]=>
     *  string(5) "error"
     *  ["code"]=>
     *  string(6) "020500"
     *  ["message"]=>
     *  array(1) {
     *    [0]=>
     *    array(3) {
     *      ["receive_order_id"]=>
     *      string(1) "2"
     *      ["code"]=>
     *      string(6) "020015"
     *      ["message"]=>
     *      string(64) "[receive_order_total_amount]半角数字ではありません。"
     *    }
     *  }
     *  ["request_id"]=>
     *  string(0) ""
     * NOTE: 全件更新に失敗したとしても処理自体は最後までいったためtrueを返す仕様となっている
     * 途中の伝票検索などで失敗した場合にfalseが返る
     */
    public function execution(\Model_Executionbulkupdatesetting $execution_bulk_update_setting): \Domain_Value_Executionresult  {
        // PF側で保持している「拡張機能実行ID」
        // これを元に受注伝票検索を行い、対象の伝票を取得する
        $extension_execution_id = $execution_bulk_update_setting->extension_execution_id;
        $execution_bulk_update_setting_id = $execution_bulk_update_setting->id;
        $user_id = $execution_bulk_update_setting->user_id;
        $client_neapi = $this->get_client_neapi($user_id);
        // 出荷確定済みでも更新するかどうかフラグ
        $allow_update_shipment_confirmed = $execution_bulk_update_setting->allow_update_shipment_confirmed;

        $search_result = self::request_receiveorder_search($client_neapi, $extension_execution_id);
        if($search_result['result'] !== \Client_Neapi::RESULT_SUCCESS){
            Log::info_ex('受注伝票検索に失敗したため一括更新処理中止', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
            $result = [];
            $result['code'] = $search_result['code'];
            $result['message'] = $search_result['message'];
            $execution_result = new \Domain_Value_Executionresult($result, 0, []);
            return $execution_result;
        }
        if($search_result['count'] <= 0){
            Log::warning_ex('有効な受注伝票が1件もないため一括更新処理中止', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
            $result = [];
            $result['code'] = \Client_Neapi::ERROR_CODE_EXCEPTION;
            $result['message'] = '有効な受注伝票が1件もありませんでした';
            $execution_result = new \Domain_Value_Executionresult($result, 0, []);
            return $execution_result;
        }
        $receive_orders = $search_result['data'];

        // 変換する（除外設定の適用、更新設定の適用）
        $convert_result = $this->convert($execution_bulk_update_setting, $receive_orders);
        $update_target_orders = $convert_result->get_update_target_orders();
        $excluded_id_and_reason = $convert_result->get_excluded_id_and_reason();
        // NOTE: 送る対象が0件の場合も後続処理をそのまま実行しているがあまりないケースなので特に気にしない
        // 0件のケースが想定以上にある場合は0件ならAPIを実行しないなどの考慮をしてください
        $sent_count = count($update_target_orders);

        // 受注伝票一括更新APIを実行する
        $bulkupdate_response =
            self::_request_receive_order_bulk_update(
                $client_neapi,
                $update_target_orders,
                $allow_update_shipment_confirmed
            );

        // リトライ機構
        $retry_codes = self::_get_retry_codes($execution_bulk_update_setting);
        $retry_bulkupdate_response = $bulkupdate_response;
        for ($count = 1; $count <= self::MAX_RETRY_COUNT; $count++) {
            // リトライ対象（失敗伝票）の取得
            $failure_orders = self::_get_retry_orders($retry_bulkupdate_response, $retry_codes);
            \Log::info_ex('リトライ対象伝票（失敗伝票）', ['failure_orders' => $failure_orders]);
            // リトライ対象がなければ処理終了
            if(empty($failure_orders)) break;

            \Log::info_ex("リトライ{$count}回目");
            // リトライ対象があった場合はそれらの伝票を再検索してもう1度処理
            $retry_search_params = ['fields' => self::_get_receiveorder_search_fields(), 'receive_order_id-in' => implode($failure_orders, ',')];
            $retry_search_result = $client_neapi->apiExecute(\Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH, $retry_search_params);

            // リトライ後の受注伝票検索APIに失敗した場合はリトライを諦め、リトライ前のレスポンスを返す
            if($retry_search_result['result'] !== \Client_Neapi::RESULT_SUCCESS){
                Log::info_ex('受注伝票検索に失敗したため一括更新処理を中止しリトライ前のレスポンスを返します', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                $execution_result = new \Domain_Value_Executionresult($bulkupdate_response, $sent_count, $excluded_id_and_reason);
                return $execution_result;
            }

            if($retry_search_result['count'] <= 0){
                Log::warning_ex('有効な受注伝票が1件もないため一括更新処理を中止しリトライ前のレスポンスを返します', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                $execution_result = new \Domain_Value_Executionresult($bulkupdate_response, $sent_count, $excluded_id_and_reason);
                return $execution_result;
            }

            $retry_receive_orders = $retry_search_result['data'];
            $retry_convert_result = self::convert($execution_bulk_update_setting, $retry_receive_orders);
            $retry_update_target_orders = $retry_convert_result->get_update_target_orders();
            $retry_excluded_id_and_reason = $retry_convert_result->get_excluded_id_and_reason();
            // リトライ後に除外したものをAPIとして送った総数から省く
            $sent_count = $sent_count - count($retry_excluded_id_and_reason);

            // リトライ対象があったはずなのにretry_update_target_ordersが0件の場合（リトライ対象のものが全て除外の場合など）
            // APIを実行すると「対象が1件もありません」というAPIエラーになってしまうため実行しない
            $retry_sent_count = count($retry_update_target_orders);
            if($retry_sent_count === 0){
                // 全件成功したとみなすレスポンスにする
                // NOTE: ここを成功のレスポンスにすることで_merge_retry_bulkupdate_response内で
                // 失敗理由をunsetして正しいレスポンスとなる
                // _merge_retry_bulkupdate_responseのロジックを修正する場合はここの擬似レスポンスの処理も依存している点に注意
                // OPTIMIZE: 擬似レスポンスを作らず「除外されているものを失敗理由からunsetする」ロジックの実現
                // _merge_retry_bulkupdate_responseとほぼ同じものを作ることになる, 計算コストを考え今はこのような実装になっている
                $retry_bulkupdate_response = [
                    'result' => \Client_Neapi::RESULT_SUCCESS,
                    'message' => '',
                ];
            } else {
                // 受注伝票一括更新APIを実行する
                $retry_bulkupdate_response =
                    self::_request_receive_order_bulk_update(
                        $client_neapi,
                        $retry_update_target_orders,
                        $allow_update_shipment_confirmed
                    );
            }

            // NOTE: 返却用の$bulkupdate_responseを更新する
            $bulkupdate_response = self::_merge_retry_bulkupdate_response($bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
            // NOTE: $excluded_id_and_reasonにリトライ後に除外した結果をマージしていく
            $excluded_id_and_reason = $excluded_id_and_reason + $retry_excluded_id_and_reason;
        }

        // 戻り値の型が複雑になってしまうためdomain_valueオブジェクトを生成して返却する
        $execution_result = new \Domain_Value_Executionresult($bulkupdate_response, $sent_count, $excluded_id_and_reason);
        return $execution_result;
    }

    /**
     * 実行IDで伝票検索APIを実行する
     *
     * @param Client_Neapi $client_neapi APIクライアント
     * @param string $extension_execution_id 実行ID
     * @return array APIレスポンス
     */
    public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
        // 伝票の件数・伝票内の内容によってはメモリーが足りなくなるケースがあるため暫定的にメモリ上限を上げる
        // OPTIMIZE: 根本処理でメモリをそこまで使わないようにする
        ini_set('memory_limit', self::MEMORY_LIMIT);

        // 伝票番号と最終更新日と受注状態区分が欲しいのでここでAPIのfieldsに追加する
        $search_params = ['fields' => self::_get_receiveorder_search_fields(), 'extension_execution_id' => $extension_execution_id];
        return $client_neapi->apiExecute(\Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH, $search_params);
    }

    /**
     * 伝票検索時に設定するフィールドを取得する
     *
     * @return string
     */
    private static function _get_receiveorder_search_fields() : string {
        // ユーザーが任意で更新可能な項目以外で必要なカラムを取得するためにAPIのfieldsに追加する
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $additional_columns = implode(array_keys(\Model_Receiveordercolumn::get_additional_columns()), ',');
        return $fields . ',' . $additional_columns;
    }

    /**
     * 受注伝票一括更新APIを実行する
     *
     * @param Client_Neapi $client_neapi APIクライアント
     * @param array $update_target_orders 更新対象の受注伝票（_get_receive_order_bulkupdate_xmlの引数説明参照）
     * @param string $allow_update_shipment_confirmed 出荷確定済みの受注伝票の更新許可情報
     * @return array 受注伝票一括更新APIの実行結果レスポンス
     */
    private static function _request_receive_order_bulk_update(
        Client_Neapi $client_neapi,
        array $update_target_orders,
        string $allow_update_shipment_confirmed
    ) : array {

        // パラメータ指定用のXML形式データの生成
        $receive_order_bulkupdate_xml = self::_get_receive_order_bulkupdate_xml($update_target_orders);

        // gzipで圧縮
        $gzip_data = gzencode($receive_order_bulkupdate_xml);
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => $gzip_data,
            'receive_order_shipped_update_flag' => $allow_update_shipment_confirmed
        ];

        // 受注伝票一括更新APIを実行する
        $bulkupdate_response = $client_neapi->apiExecute(\Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE, $bulkupdate_params);

        return $bulkupdate_response;
    }

    /**
     * 受注伝票の変換処理
     * ・除外対象のものは除外し除外伝票一覧にまとめて返す
     * 　・除外テーブルにあるもの
     * 　・出荷確定済みのもの
     * 　・既に同じ一括更新が実行済みのもの
     * 　・yahoo店舗の受注キャンセル
     * ・更新設定を適用する
     * 　・上書き、追記、四則演算など
     * @param  Model_Executionbulkupdatesetting|Model_Bulkupdatesetting $setting 更新設定オブジェクト。実行時とプレビュー時で扱う対象のオブジェクトが異なる
     * @param  array $receive_orders NEAPIで取得した受注伝票の配列
     * @return Domain_Value_Convertresult 変換結果を表したdomain_valueオブジェクト
     * プロパティ: [array: 変換後の受注伝票一覧, array: 除外した伝票番号とその理由をまとめた配列]
     */
    public function convert($setting, array $receive_orders) : \Domain_Value_Convertresult {
        $setting_id = $setting->id;
        // 実行時に適用する更新内容を取得する
        if(self::_is_execution_setting($setting)){
            $update_columns = Model_Executionbulkupdatecolumn::findAll(['execution_bulk_update_setting_id' => $setting_id], ['related' => ['receive_order_column']]);
            // 実行時に除外する伝票一覧を取得する
            $excluded_receive_orders = Model_Excludedreceiveorder::get_excluded_receive_orders($setting_id);
        } else {
            $update_columns = Model_Bulkupdatecolumn::findAll(['bulk_update_setting_id' => $setting_id], ['related' => ['receive_order_column']]);
            $excluded_receive_orders = [];
        }
        // 実行済みを表す受注分類タグ
        // 実行名は空文字の場合もある→空文字の場合は実行名なしの実行のためexecuted_tagを空文字にする
        $setting_name = $setting->name;
        $executed_tag = self::get_executed_tag($setting_name);
        // 出荷確定済みでも更新するかどうかフラグ
        $allow_update_shipment_confirmed = $setting->allow_update_shipment_confirmed;

        $update_target_orders = [];
        $excluded_id_and_reason = [];
        $yahoo_shop_ids = null;
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        // 伝票全ての受注状態が「受注メール取込済」になっているかどうか
        $is_all_mail_captured_for_preview = true;
        // 受注分類タグが更新する項目になっているかどうか
        $is_update_gruoping_tag_for_preview = true;
        /*** メイン処理ループ ***/
        foreach($receive_orders as $receive_order){
            // 除外リストに入ってる伝票は除外する
            if(in_array($receive_order['receive_order_id'], $excluded_receive_orders, true)){
                // 除外理由は1伝票1理由とする（先勝ち）
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.user_selection')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    // 除外した場合これ以降の処理は不要なのでここで処理を切り上げて次の伝票にいく
                    // タスクではなく画面の場合は更新設定を反映した結果も欲しいので処理を切り上げず進む
                    continue;
                }
            }

            // 出荷確定済みのものを除外する
            if(
                $allow_update_shipment_confirmed !== \Client_Neapi::RECEIVE_ORDER_SHIPPED_UPDATE_FLAG_TRUE &&
                $receive_order['receive_order_order_status_id'] === \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED
            ){
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.shipped')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    // 除外した場合これ以降の処理は不要なのでここで処理を切り上げて次の伝票にいく
                    continue;
                }
            }

            // 実行済みの受注分類タグがついているものはこのタイミングで除外する
            // 空文字の場合は除外しない
            if($executed_tag !== '' && strpos($receive_order['receive_order_gruoping_tag'], $executed_tag) !== false){
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.duplicate_execution')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    // 除外した場合これ以降の処理は不要なのでここで処理を切り上げて次の伝票にいく
                    continue;
                }
            }

            // 受注伝票の各項目に対して更新設定がある場合には更新内容の反映を行う
            $update_target_order = $this->_convert($update_columns, $receive_order, $setting, $yahoo_shop_ids, $japanese_yen_shop_ids, $fraction_id, $excluded_id_and_reason);
            // 更新内容の反映で失敗した場合は空配列が返ってくるため処理を切り上げて次の伝票にいく
            if(empty($update_target_order) && self::_is_execution_setting($setting)){
                continue;
            }

            // 伝票番号と最終更新日は更新対象ではないが更新時にパラメータ指定が必要なためここで配列に入れておく
            $update_target_order['receive_order_id'] = $receive_order['receive_order_id'];
            $update_target_order['receive_order_last_modified_date'] = $receive_order['receive_order_last_modified_date'];
            /*
             受注分類タグはユーザーが設定したものを反映した後に末尾に実行済みを表すタグをつける必要がある
             但し、以下の場合は実行済みの受注分類タグをつけない
             ・設定名が空文字の場合
             ・受注状態が「受注メール取込済」の場合
            */
            if (self::_is_execution_setting($setting)) {
                // 実行時
                if ($executed_tag !== '' && $receive_order['receive_order_order_status_id'] !== \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_MAIL_CAPTURED) {
                    // 更新設定がなくupdate_target_orderに受注分類タグの要素がない場合には現在の値を入れておく
                    if(!isset($update_target_order['receive_order_gruoping_tag'])){
                        $update_target_order['receive_order_gruoping_tag'] = $receive_order['receive_order_gruoping_tag'];
                    }
                    $update_target_order['receive_order_gruoping_tag'] .= $executed_tag;
                }
            }else{
                // プレビュー時
                // プレビュー時は全ての伝票の表示項目は同一という前提で作られているため、受注メール取込済の伝票だけ受注分類タグの表示なしにはできない、そのため項目だけは追加する
                if ($executed_tag !== '') {
                    // 更新設定がなくupdate_target_orderに受注分類タグの要素がない場合には現在の値を入れておく
                    if(!isset($update_target_order['receive_order_gruoping_tag'])){
                        $update_target_order['receive_order_gruoping_tag'] = $receive_order['receive_order_gruoping_tag'];
                        $is_update_gruoping_tag_for_preview = false;
                    }
                    if ($receive_order['receive_order_order_status_id'] !== \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_MAIL_CAPTURED) {
                        // 受注状態が「受注メール取込済」の場合は実行済みの受注分類タグをつけない
                        $update_target_order['receive_order_gruoping_tag'] .= $executed_tag;
                        $is_all_mail_captured_for_preview = false;
                    }
                }
            }
            $update_target_orders[$update_target_order['receive_order_id']] = $update_target_order;
        }

        // すべての伝票の受注状態が受注メール取込済でかつ、受注分類タグの項目が無い場合は受注分類タグを更新する項目から削除する
        if ($is_all_mail_captured_for_preview && !$is_update_gruoping_tag_for_preview) {
            foreach ($update_target_orders as $receive_order_id => $update_target_order) {
                unset($update_target_order['receive_order_gruoping_tag']);
                $update_target_orders[$receive_order_id] = $update_target_order;
            }
        }

        \Log::info_ex('除外対象の伝票', ['setting_id' => $setting_id, 'excluded_id_and_reason' => $excluded_id_and_reason, 'object' => get_class($setting)]);

        $convert_result = new \Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        return $convert_result;
    }

    /**
     * 受注伝票の各項目に対して更新設定がある場合には更新内容の反映を行う
     * 更新内容を配列に入れて返す
     * Yahooなどの理由で更新対象外となる場合には空配列を返す
     *
     * @param array $update_columns 更新内容
     * @param array $receive_order 受注伝票
     * @param Model_Executionbulkupdatesetting|Model_Bulkupdatesetting $setting
     * @param null|array $yahoo_shop_ids Yahoo店舗ID一覧（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @param null|array $japanese_yen_shop_ids 通貨単位区分が「円」の店舗一覧（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @param null|string $fraction_id 端数処理区分（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @param array $excluded_id_and_reason 除外対象の伝票番号とその理由、アドレス参照で元の配列を更新していく形になっている
     * @return array $update_target_order 更新対象の伝票の更新情報、なければ空配列
     */
    private function _convert(array $update_columns, array $receive_order, $setting, ?array &$yahoo_shop_ids, ?array &$japanese_yen_shop_ids, ?string &$fraction_id, array &$excluded_id_and_reason) : array {
        $user_id = $setting->get_execution_user_id();

        // 更新項目として支払方法の選択有無
        $is_selected_payment_method_id = false;
        // 更新項目として選択された受注金額関連の項目名を格納
        // （受注金額関連の更新時の備考欄追記のメッセージとする）
        $selected_order_amount_names = [];
        // 総合計に反映する値
        $reflect_order_amount = 0;

        $update_target_order = [];
        // 端数処理をする場合、店舗が変わる可能性があるので実行後の店舗を指定する必要がある
        $after_shop_id = $receive_order['receive_order_shop_id'];
        foreach($update_columns as $update_column){
            if($update_column['receive_order_column_id'] === Model_Receiveordercolumn::COLUMN_ID_SHOP){
                $after_shop_id = $update_column->update_value;
            }
        }

        foreach($update_columns as $update_column){
            $physical_name    = $update_column->receive_order_column->physical_name;
            $update_method_id = $update_column->update_method_id;
            $update_value     = $update_column->update_value;
            $ne_value         = $receive_order[$physical_name];

            if(!self::_can_update_payment_method($receive_order, $update_column)) {
                // 更新対象の受注伝票において、支払方法の項目が更新不可の場合、更新対象から除外する
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.payment_method_update_condition')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    return [];
                }
            }

            if(!self::_can_update_order_amount($receive_order, $update_column)) {
                // 更新対象の受注伝票において、受注金額関連の項目が更新不可の場合、更新対象から除外する
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.order_amount_update_condition')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    return [];
                }
            }

            // Yahoo!ショッピングの受注キャンセルを行うとモール側の注文ステータスも連動して変更してしまう可能性があるため制限を加える
            if(!$this->_can_update_yahoo_cancel($after_shop_id, $physical_name, $update_value, $setting, $yahoo_shop_ids)){
                if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                    $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                        'receive_order_id' => $receive_order['receive_order_id'],
                        'excluded_reason' => __em('excluded_reason.yahoo_cancel')
                    ];
                }
                if(self::_is_execution_setting($setting)){
                    return [];
                }
            }

            // 更新設定を適用させる
            $evaluated_value = Model_Updatemethod::evaluate($update_method_id, $ne_value, $update_value);

            // 数値型であれば端数処理を行う
            if($update_column->receive_order_column->column_type_id === Model_Columntype::NUMBER){
                $fraction_id = $this->_get_fraction_id($user_id, $fraction_id);
                $valid_scale = $this->_get_valid_scale($user_id, $after_shop_id, $japanese_yen_shop_ids);
                $evaluated_value = Utility_Calculator::get_variable_round($evaluated_value, $fraction_id, $valid_scale);

                // 最大値・最小値を超えるような値の場合は除外対象にする
                if($evaluated_value < \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN || \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX < $evaluated_value){
                    if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                        $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                            'receive_order_id' => $receive_order['receive_order_id'],
                            'excluded_reason' => __em('excluded_reason.numeric_out_of_range')
                        ];
                    }
                    if(self::_is_execution_setting($setting)){
                        return [];
                    }
                }
            }

            // 上記除外設定・更新設定の適用を経たものが更新対象となる
            $update_target_order[$physical_name] = $evaluated_value;

            if($update_column->receive_order_column->is_payment_method_id()) {
                // 除外されずに更新対象となった受注伝票において更新項目が支払方法の場合

                $is_selected_payment_method_id = true;
            }

            if($update_column->receive_order_column->is_order_amount()) {
                // 除外されずに更新対象となった受注伝票において更新項目が受注金額関連項目の場合

                // 受注金額関連の項目が選択がされていると判定
                $selected_order_amount_names[] = $update_column->receive_order_column->logical_name;
                if(!$update_column->receive_order_column->is_total_amount()) {
                    // 総合計以外の場合

                    // 総合計に反映する値 = (元の値 - 更新値)
                    // （すでに端数処理がされているためここではBCMathライブラリによる演算は行わず、単純な四則演算とする）
                    $diff_value = ($ne_value - $evaluated_value);

                    if($update_column->receive_order_column->is_point_amount()) {
                        // ポイント数の場合は実質割引額なので、総合計にはマイナス値として反映する
                        $reflect_order_amount -= $diff_value;
                    } else {
                        $reflect_order_amount += $diff_value;
                    }
                }
            }

        }

        if($is_selected_payment_method_id) {
            // 更新項目として支払方法の選択されている場合

            // 備考欄に追記する情報
            $add_receive_order_note = '支払方法が更新されています。';
            // 更新対象の受注伝票が確認待ちの状態となるように、確認チェック、確認内容、備考欄への反映をする
            $update_target_order =
                self::_set_wait_for_confirmation(
                    $receive_order,
                    $update_target_order,
                    $add_receive_order_note
                );

            // 入金状況を未入金に設定
            $update_target_order['receive_order_deposit_type_id'] =
                \Client_Neapi::RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT;
            // 承認状況を未承認に設定
            $update_target_order['receive_order_credit_approval_type_id'] =
                \Client_Neapi::RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT;
        }

        // 受注金額関連項目に関する更新設定がある場合には、設定に応じた更新内容の反映を行う
        $update_target_order = self::_reflect_order_amount(
            $receive_order,
            $setting,
            $excluded_id_and_reason,
            $update_target_order,
            $selected_order_amount_names,
            $reflect_order_amount
        );

        return $update_target_order;
    }

    /**
     * 受注金額関連項目に関する更新設定がある場合には、設定に応じた更新内容の反映を行う
     *
     * ・確認チェック、確認内容、備考欄への反映をする
     * ・総合計以外の受注金額関連が更新対象 かつ 総合計への反映が許可されている場合は総合計も更新対象として値を反映する
     * 　ただし、総合計への反映値が指定可能範囲を超える場合は更新対象から除外する
     *
     * @param array $receive_order 受注伝票
     * @param Model_Executionbulkupdatesetting|Model_Bulkupdatesetting $setting 一括更新設定情報
     * @param array $excluded_id_and_reason 除外対象の伝票番号とその理由、アドレス参照で元の配列を更新していく形になっている
     * @param array $update_target_order 更新対象の伝票の更新情報
     * @param array $selected_order_amount_names 選択された受注金額関連項目の論理名の配列
     * @param mixed $reflect_order_amount 総合計に反映する値（タイプヒントをなしにしている理由は整数以外の数値を取り扱う可能性があるため）
     * @return array $update_target_order 受注金額関連項目に関する更新内容を反映した伝票の更新情報、なければ空配列
     */
    private static function _reflect_order_amount(array $receive_order, $setting, array &$excluded_id_and_reason, array $update_target_order, array $selected_order_amount_names, $reflect_order_amount) : array {

        if(!empty($selected_order_amount_names)) {
            // 更新項目に受注金額関連項目が選択がされている場合

            if($setting->allow_reflect_order_amount &&
                !isset($update_target_order['receive_order_total_amount'])) {
                // 総合計への反映が許可されている かつ 総合計が更新項目の対象となっていない場合

                $receive_order_total_amount = $receive_order['receive_order_total_amount'];
                // 総合計への反映値を減算
                $receive_order_total_amount -= $reflect_order_amount;

                // 最大値・最小値を超えるような値の場合は除外対象にする
                if($receive_order_total_amount < \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN || \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX < $receive_order_total_amount){
                    if(!isset($excluded_id_and_reason[$receive_order['receive_order_id']])){
                        $excluded_id_and_reason[$receive_order['receive_order_id']] = [
                            'receive_order_id' => $receive_order['receive_order_id'],
                            'excluded_reason' => __em('excluded_reason.total_amount_out_of_range')
                        ];
                    }
                    if(self::_is_execution_setting($setting)){
                        return [];
                    }
                }

                // 更新項目として総合計の値を設定
                $update_target_order['receive_order_total_amount'] = $receive_order_total_amount;
                $selected_order_amount_names[] = '総合計';
            }

            // 備考欄に追記する情報
            $add_receive_order_note = implode(',', $selected_order_amount_names) . 'が更新されています。';
            // 更新対象の受注伝票が確認待ちの状態となるように、確認チェック、確認内容、備考欄への反映をする
            $update_target_order =
                self::_set_wait_for_confirmation(
                    $receive_order,
                    $update_target_order,
                    $add_receive_order_note
                );

        }

        return $update_target_order;
    }

    /**
     * 更新対象の受注伝票が確認待ちの状態となるように、確認チェック、確認内容、備考欄への反映をする
     *
     * @param array $receive_order 受注伝票
     * @param array $update_target_order 更新対象の伝票の更新情報
     * @param string $add_receive_order_note 備考欄
     * @return array $update_target_order 受注金額関連項目に関する更新内容を反映した伝票の更新情報、なければ空配列
     */
    private static function _set_wait_for_confirmation(array $receive_order, array $update_target_order, string $add_receive_order_note) : array {

        // 万が一更新内容が誤りなどあることを考慮し、意図せず出荷までされないように、
        // 必ず確認待ちにして、人の目を通す運用を強制する

        // 確認チェックを外す（受注ステータスを確認待ちに止めるため）
        // 更新項目に確認チェックが指定されていたとしても、こちらの処理を優先して上書きをする
        $update_target_order['receive_order_confirm_check_id'] = '0';

        $receive_order_confirm_ids = '';
        if(isset($update_target_order['receive_order_confirm_ids'])) {
            // 確認内容が更新項目として存在する場合はその更新値を設定
            $receive_order_confirm_ids = $update_target_order['receive_order_confirm_ids'];
        } else {
            // 確認内容が更新項目として存在しない場合は現在の値を設定
            $receive_order_confirm_ids = $receive_order['receive_order_confirm_ids'];
        }

        if(strpos($receive_order_confirm_ids, \Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE) === false) {
            // 確認内容に同内容が記載されていない場合

            // 確認内容に「備考欄を見て下さい。」を追加する
            if(strlen($receive_order_confirm_ids) > 0) {
                $receive_order_confirm_ids .= ':';
            }
            $receive_order_confirm_ids .= \Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE;
            $update_target_order['receive_order_confirm_ids'] = $receive_order_confirm_ids;
        } else {
            // 確認内容に同内容が記載されている場合、追記せずそのままの値を設定
            $update_target_order['receive_order_confirm_ids'] = $receive_order_confirm_ids;
        }


        if(isset($update_target_order['receive_order_note'])) {
            // 備考欄が更新項目として存在する場合はその更新値を設定
            $receive_order_note = $update_target_order['receive_order_note'];
        } else {
            // 備考欄が更新項目として存在しない場合は現在の値を設定
            $receive_order_note = $receive_order['receive_order_note'];
        }

        if(strpos($receive_order_note, $add_receive_order_note) === false) {
            // 備考欄に同内容が記載されていない場合

            // 備考欄に追記する
            if(strlen($receive_order_note) > 0) {
                $receive_order_note .= "\n";
            }
            $receive_order_note .= $add_receive_order_note;
            $update_target_order['receive_order_note'] = $receive_order_note;
        } else {
            // 備考欄に同内容が記載されている場合、追記せずそのままの値を設定
            $update_target_order['receive_order_note'] = $receive_order_note;
        }

        return $update_target_order;
    }

    /**
     * 企業設定から端数処理の区分値を取得するメソッド
     * 内部プロパティに保持しキャッシュ化、キャッシュがあれば取得処理を走らせずにそれを返す
     *
     * @param string $user_id
     * @param null|string $fraction_id 端数処理区分（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @return string
     * @throws UnexpectedValueException
     */
    private function _get_fraction_id(string $user_id, ?string &$fraction_id) : string {
        // キャッシュがあればそれを返す
        if(!is_null($fraction_id)) return $fraction_id;

        // キャッシュがなければ取得する
        $client_neapi = $this->get_client_neapi($user_id);
        $company_info = $client_neapi->apiExecute(\Client_Neapi::PATH_LOGIN_COMPANY_INFO);
        if($company_info['result'] !== \Client_Neapi::RESULT_SUCCESS){
            throw new \UnexpectedValueException('企業情報の取得処理に失敗しました');
        }
        $fraction_id = $company_info['data'][0]['company_fraction_id'];
        return $fraction_id;
    }

    /**
     * 端数処理の小数点以下桁数を取得する
     * 店舗設定の「通貨単位区分」で判断
     * 「円」であれば小数点以下0桁
     * 「円以外」であれば小数点以下2桁
     *
     * @param string $user_id
     * @param string $shop_id 対象の店舗ID
     * @param null|array $japanese_yen_shop_ids 通貨単位区分が「円」の店舗一覧（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @return int 端数処理時の小数点以下桁数
     * @throws UnexpectedValueException
     */
    private function _get_valid_scale(string $user_id, string $shop_id, ?array &$japanese_yen_shop_ids) : int {
        // キャッシュがなければ取得する
        if(is_null($japanese_yen_shop_ids)){
            $client_neapi = $this->get_client_neapi($user_id);
            $japanese_yen_shop_ids = $client_neapi->get_shop_ids(['shop_currency_unit_id-eq' => \Client_Neapi::JAPANESE_YEN]);
        }

        // 通貨単位区分が「円」であれば小数点以下0桁
        if(in_array($shop_id, $japanese_yen_shop_ids, true)){
            return \Utility_Calculator::DIGIT_INTEGER;
        // 通貨単位区分が「円以外」であれば小数点以下2桁
        } else {
            return \Utility_Calculator::DIGIT_DECIMAL;
        }
    }

    /**
     * 一括更新の結果をお知らせ配信する
     * 更新結果によるメッセージの出し分けロジックを含むためこのクラスに配置
     * @param Model_Executionbulkupdatesetting $execution_bulk_update_setting 実行する一括更新設定情報を持ったモデル
     * @param Domain_Value_Executionresult  $execution_result 実行結果のドメインバリューオブジェクト
     * @return bool
     */
    public function add_notice_for_bulkupdate(Model_Executionbulkupdatesetting $execution_bulk_update_setting, Domain_Value_Executionresult $execution_result) : bool {
        $bulkupdate_response    = $execution_result->get_response();
        $sent_count             = $execution_result->get_sent_count();
        $excluded_id_and_reason = $execution_result->get_excluded_id_and_reason();

        $task_id = $execution_bulk_update_setting->request_key;
        $user_id = $execution_bulk_update_setting->user_id;
        $client_neapi = $this->get_client_neapi($user_id);
        $execution_name = $execution_bulk_update_setting->name;
        // nameが空だった時の対応
        if($execution_name === ''){
            $execution_name = \Lang::get('page.tasklist.no_name');
        }
        // エラーが1件もない場合、messageは空文字で来る
        // 後の処理の都合で空配列に置換する
        if($bulkupdate_response['message'] === '') $bulkupdate_response['message'] = [];
        $error_count = count($bulkupdate_response['message']);
        $success_count = $sent_count - $error_count;
        $excluded_count = count($excluded_id_and_reason);

        // 通知メッセージ内のカラムの物理名を論理名に変えるために前もってカラムリストを取得しておく
        $physical_names = \Model_Receiveordercolumn::pluck('physical_name', ['order_by' => 'id']);
        $logical_names  = \Model_Receiveordercolumn::pluck('logical_name',  ['order_by' => 'id']);
        // ユーザーが任意で更新可能な項目以外で必要なカラムを追加する
        $additional_column_physical_names = array_keys(\Model_Receiveordercolumn::get_additional_columns());
        $additional_column_logical_names = array_values(\Model_Receiveordercolumn::get_additional_columns());
        $physical_names = array_merge($physical_names, $additional_column_physical_names);
        $logical_names = array_merge($logical_names, $additional_column_logical_names);

        // メッセージ出し分け
        switch($success_count){
        // 全て成功(送った数と成功数が一致)
        case $sent_count:
            $execution_notice_success = \Client_Neapi::EXECUTION_NOTICE_SUCCESS_TRUE;
            $execution_notice_title   = '受注一括更新が成功しました';
            $execution_notice_content = "実行タスクID：{$task_id}\n一括更新設定名称：{$execution_name}\n\n更新成功受注伝票件数：{$success_count}件\n除外した受注伝票件数：{$excluded_count}件\n\n";
            break;

        // 全て失敗(成功数0)
        case 0:
            $execution_notice_success = \Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE;
            $execution_notice_title   = '受注一括更新が失敗しました';
            $execution_notice_content = "実行タスクID：{$task_id}\n一括更新設定名称：{$execution_name}\n\n更新失敗受注伝票件数：{$error_count}件\n除外した受注伝票件数：{$excluded_count}件\n\n";
            // エラー原因を原因ごとにまとめる
            $execution_notice_content .= self::_get_execution_notice_content_for_error($bulkupdate_response['message'], $physical_names, $logical_names);
            break;

        // 部分成功/失敗
        default:
            $execution_notice_success = \Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE;
            $execution_notice_title   = '受注一括更新が一部失敗しました';
            $execution_notice_content = "実行タスクID：{$task_id}\n一括更新設定名称：{$execution_name}\n\n更新成功受注伝票件数：{$success_count}件\n更新失敗受注伝票件数：{$error_count}件\n除外した受注伝票件数：{$excluded_count}件\n\n";
            // エラー原因を原因ごとにまとめる
            $execution_notice_content .= self::_get_execution_notice_content_for_error($bulkupdate_response['message'], $physical_names, $logical_names);
            break;
        }

        // 除外理由を理由ごとにまとめる
        $execution_notice_content .= self::_get_execution_notice_content_for_excluded($excluded_id_and_reason);

        $notice_add_params = [
            'execution_notice_success' => $execution_notice_success,
            'execution_notice_title'   => $execution_notice_title,
            'execution_notice_content' => trim($execution_notice_content),
        ];
        $notice_add_result = $client_neapi->apiExecute(\Client_Neapi::PATH_NOTICE_EXECUTION_ADD, $notice_add_params);

        return ($notice_add_result['result'] === \Client_Neapi::RESULT_SUCCESS);
    }

    /**
     * エラー原因を原因ごとにまとめる
     *
     * @param array $bulkupdate_response_messages 受注伝票一括更新APIレスポンスのmessageの配列
     * @param array $physical_names カラムの物理名の配列
     * @param array $logical_names カラムの論理名の配列
     * @return string 原因ごとのエラーとなった伝票番号とその原因の文字列
     */
    private static function _get_execution_notice_content_for_error(array $bulkupdate_response_messages, array $physical_names, array $logical_names) : string {
        $message_summary = [];
        foreach($bulkupdate_response_messages as $bulkupdate_response_message){
            if (isset(self::$notice_change_message_for_code[$bulkupdate_response_message['code']])) {
                $bulkupdate_response_message['message'] = self::$notice_change_message_for_code[$bulkupdate_response_message['code']];
            }

            if (!isset($message_summary[$bulkupdate_response_message['message']])) {
                $message_summary[$bulkupdate_response_message['message']] = [];
            }
            $message_summary[$bulkupdate_response_message['message']][] = $bulkupdate_response_message['receive_order_id'];
        }
        $execution_notice_content = '';
        foreach ($message_summary as $message => $receive_order_ids) {
            $execution_notice_content .= '伝票番号：'.implode(',', $receive_order_ids)."\n原因：".str_replace($physical_names, $logical_names, $message)."\n\n";
        }
        return $execution_notice_content;
    }

    /**
     * 除外理由を理由ごとにまとめる
     *
     * @param array $excluded_id_and_reason 除外理由の配列
     * @return string 理由ごとのエラーとなった伝票番号とその理由の文字列
     */
    private static function _get_execution_notice_content_for_excluded(array $excluded_id_and_reason) : string {
        $reason_summary = [];
        foreach ($excluded_id_and_reason as $excluded) {
            if (!isset($reason_summary[$excluded['excluded_reason']])) {
                $reason_summary[$excluded['excluded_reason']] = [];
            }
            $reason_summary[$excluded['excluded_reason']][] = $excluded['receive_order_id'];
        }
        $execution_notice_content = '';
        foreach ($reason_summary as $excluded_reason => $receive_order_ids) {
            $execution_notice_content .= '伝票番号：'.implode(',', $receive_order_ids)."\n理由：".$excluded_reason."\n\n";
        }
        return $execution_notice_content;
    }

    /**
     * APIクライアントを取得する
     *
     * @param string $user_id ユーザーID
     * @param bool $is_retry リトライ可能なエラーだった場合リトライするかどうか true:リトライを行う/false:リトライしない
     * @return Client_Neapi
     * @throws Exception
     * @throws FuelException
     */
    protected function get_client_neapi(string $user_id, bool $is_retry = true) : Client_Neapi {
        return new Client_Neapi($user_id, $is_retry);
    }

    /**
     * 実行済みを表す受注分類タグを返す
     * 実行名は空文字の場合もある→空文字の場合は実行名なしの実行のため空文字を返す
     *
     * @param string|null $setting_name
     * @return string
     */
    protected static function get_executed_tag(?string $setting_name) : string {
        if(empty($setting_name)){
            return '';
        } else {
            return "[【済】{$setting_name}]";
        }
    }

    /**
     * 引数で渡された設定のオブジェクトが実行時のもの（Model_Executionbulkupdatesetting）かどうかを返す
     * Model_Executionbulkupdatesettingならtrue
     * Model_Bulkupdatesettingならfalse
     * それ以外なら例外とする
     *
     * @param  Model_Executionbulkupdatesetting|Model_Bulkupdatesetting $setting 更新設定オブジェクト。実行時とプレビュー時で扱う対象のオブジェクトが異なる
     * @return bool
     * @throws UnexpectedValueException
     */
    private static function _is_execution_setting($setting) : bool {
        if(get_class($setting) === 'Model_Executionbulkupdatesetting'){
            return true;
        } else if(get_class($setting) === 'Model_Bulkupdatesetting') {
            return false;
        } else {
            throw new \UnexpectedValueException('更新設定以外のオブジェクトが指定されました');
        }
    }

    /**
     * 支払方法を更新して良いかどうかの判定関数
     *
     * @param array $receive_order 受注伝票
     * @param Model_Executionbulkupdatecolumn|Model_Bulkupdatecolumn $update_column 更新項目の情報
     * @return bool true:更新可、false:更新不可
     */
    private static function _can_update_payment_method(array $receive_order, $update_column) : bool {

        if(!$update_column->receive_order_column->is_payment_method_id()) {
            // 更新項目が支払方法以外の場合
            return true;
        }

        if($receive_order['receive_order_order_status_id'] === \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED) {
            // 更新対象の受注伝票の受注状態が起票済(CSV/手入力)の場合
            return true;
        }

        if($receive_order['receive_order_order_status_id'] === \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_WAIT_FOR_PRINT) {
            // 更新対象の受注伝票の受注状態が納品書印刷待ちの場合
            return true;
        }

        // 上記条件にマッチしない場合は更新してはいけない
        return false;
    }

    /**
     * 受注金額関連項目を更新して良いかどうかの判定関数
     *
     * @param array $receive_order 受注伝票
     * @param Model_Executionbulkupdatecolumn|Model_Bulkupdatecolumn $update_column 更新項目の情報
     * @return bool true:更新可、false:更新不可
     */
    private static function _can_update_order_amount(array $receive_order, $update_column) : bool {

        if(!$update_column->receive_order_column->is_order_amount()) {
            // 更新項目が受注金額関連項目以外の場合
            return true;
        }

        if($receive_order['receive_order_order_status_id'] === \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED) {
            // 更新対象の受注伝票の受注状態が起票済(CSV/手入力)の場合
            return true;
        }

        // 上記条件にマッチしない場合は更新してはいけない
        return false;
    }

    /**
     * Yahooの受注キャンセルを更新して良いかどうかの判定関数
     * true:  更新して良い
     * false: 更新してはいけない
     * @param string $shop_id 対象の店舗ID
     * @param string $physical_name カラムの物理名
     * @param string $update_value 更新しようとしてる値
     * @param Model_Executionbulkupdatesetting|Model_Bulkupdatesetting $setting
     * @param null|array $yahoo_shop_ids Yahoo店舗ID一覧（キャッシュ用）、アドレス参照をしていてnullであれば取得してキャッシュ化、次回以降はそれを使うようになっています
     * @return bool
     */
    private function _can_update_yahoo_cancel(string $shop_id, string $physical_name, string $update_value, $setting, ?array &$yahoo_shop_ids) : bool {
        // Yahoo!ショッピングの受注キャンセルを更新するかどうかのフラグ
        $allow_update_yahoo_cancel = $setting->allow_update_yahoo_cancel;

        // Yahoo受注キャンセル更新許可フラグがtrueであれば更新して良い
        if($allow_update_yahoo_cancel === '1'){
            return true;
        }

        // 受注キャンセルに関する設定でなければ更新して良い
        if($physical_name !== 'receive_order_cancel_type_id'){
            return true;
        }

        // 更新する値が0であれば更新して良い
        if($update_value === '0'){
            return true;
        }

        // Yahooの店舗でなければ更新して良い
        // 店舗一覧が未取得である場合は取得してから判定する
        if(is_null($yahoo_shop_ids)){
            $user_id = $setting->get_execution_user_id();
            $client_neapi = $this->get_client_neapi($user_id);
            $yahoo_shop_ids = $client_neapi->get_shop_ids(['shop_mall_id-eq' => \Client_Neapi::MALL_CODE_YAHOO]);
        }
        if(!in_array($shop_id, $yahoo_shop_ids, true)){
            return true;
        }

        // 上記条件にマッチしない場合は更新してはいけない
        return false;
    }

    /**
     * リトライ対象伝票の取得処理
     * @param array $bulkupdate_response 一括更新APIのレスポンス配列
     * 例:
     * {
     * "result": "error",
     * "code":"020500",
     * "message":[
     *     {
     *      "receive_order_id":"7",
     *      "code":"020006",
     *       "message":"receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。"
     *     }
     * }
     * @param array $retry_codes リトライ対象のコード一覧
     * @return array 失敗伝票のid配列
     */
    private static function _get_retry_orders(array $bulkupdate_response, array $retry_codes) : array {
        // 失敗伝票がない場合messageは空文字で来るため空なら失敗伝票なしと判断し空配列を返して終了
        // またそれ以外にも一括更新処理に失敗した際にmessageに配列ではなく文字列でエラー文言が入ったりするので、配列でない限りこの配列は空配列を返す
        if(!is_array($bulkupdate_response['message'])) return [];

        $failure_orders = [];
        foreach($bulkupdate_response['message'] as $receive_order){
            if(in_array($receive_order['code'], $retry_codes, true)){
                $failure_orders[] = $receive_order['receive_order_id'];
            }
        }
        return $failure_orders;
    }

    /**
     * リトライ対象のコードを返す
     *
     * @param  Model_Executionbulkupdatesetting $execution_bulk_update_setting
     * @return array リトライ対象のコード一覧
     */
    private static function _get_retry_codes(\Model_Executionbulkupdatesetting $execution_bulk_update_setting) : array {
        // フラグなしでデフォルトでリトライ対象のものをここに記述する
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING];
        // 他者更新伝票のリトライonの場合にはリトライ対象コードを追加
        if($execution_bulk_update_setting->allow_optimistic_lock_update_retry){
            $retry_codes[] = \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE;
        }
        return $retry_codes;
    }

    /**
     * 一括更新のレスポンスとリトライ後のレスポンスをいい感じに統合するためのメソッド
     * ロジック: リトライ対象のmessageを全て除去、その後リトライ後のresponseをマージ、これでリトライ後の結果が正しく反映されるはず
     * @param  array $bulkupdate_response
     * 例:
     *  ["message"]=>
     *  array(2) {
     *    [0]=>
     *    array(3) {
     *      ["receive_order_id"]=>
     *      string(1) "7"
     *      ["code"]=>
     *      string(6) "020006"
     *      ["message"]=>
     *      string(125) "receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。"
     *    }
     *    [1]=>
     *    array(3) {
     *      ["receive_order_id"]=>
     *      string(1) "8"
     *      ["code"]=>
     *      string(6) "020015"
     *      ["message"]=>
     *      string(59) "[receive_order_date]日付の形式ではありません。"
     *    }
     *  }
     * @param  array $retry_bulkupdate_response
     * ["message"]=>
     * array(1) {
     *   [0]=>
     *   array(3) {
     *     ["receive_order_id"]=>
     *     string(1) "7"
     *     ["code"]=>
     *     string(6) "020015"
     *     ["message"]=>
     *     string(59) "[receive_order_date]日付の形式ではありません。"
     *   }
     * }
     * @param  array $failure_orders 失敗伝票番号一覧
     * @return array マージ処理を行った後の結果配列
     * ["message"]=>
     * array(2) {
     *   [0]=>
     *   array(3) {
     *     ["receive_order_id"]=>
     *     string(1) "8"
     *     ["code"]=>
     *     string(6) "020015"
     *     ["message"]=>
     *     string(59) "[receive_order_date]日付の形式ではありません。"
     *   }
     *   [1]=>
     *   array(3) {
     *     ["receive_order_id"]=>
     *     string(1) "7"
     *     ["code"]=>
     *     string(6) "020015"
     *     ["message"]=>
     *     string(59) "[receive_order_date]日付の形式ではありません。"
     *   }
     * }
     */
    private static function _merge_retry_bulkupdate_response(array $bulkupdate_response, array $retry_bulkupdate_response, array $failure_orders) : array {
        if(!is_array($bulkupdate_response['message'])){
            if($bulkupdate_response['message'] === ''){
                // messageが空文字の場合は処理の都合で空配列にする
                $bulkupdate_response['message'] = [];
            } else {
                // 空文字でない場合は一括更新で何らかのエラーになっているためそのままのレスポンスを返す
                return $bulkupdate_response;
            }
        }

        if(!is_array($retry_bulkupdate_response['message'])){
            if($retry_bulkupdate_response['message'] === ''){
                // messageが空文字の場合は処理の都合で空配列にする
                $retry_bulkupdate_response['message'] = [];
            } else {
                \Log::info_ex("リトライ後のレスポンスがエラーになっているのでリトライ前のレスポンスを返します", ['message' => $retry_bulkupdate_response['message']]);
                return $bulkupdate_response;
            }
        }

        // 失敗伝票のリトライ前のレスポンスを一旦unsetする
        // こうしてリセットしリトライ後のレスポンスとマージすることでリトライ後のレスポンスを反映していく
        foreach($bulkupdate_response['message'] as $index =>$message){
            if(in_array($message['receive_order_id'], $failure_orders, true)){
                unset($bulkupdate_response['message'][$index]);
            }
        }
        $retry_bulkupdate_response['message'] = array_merge($retry_bulkupdate_response['message'], $bulkupdate_response['message']);

        if(empty($retry_bulkupdate_response['message'])){
            // messageが1つもない時は成功とみなす
            $retry_bulkupdate_response['result'] = \Client_Neapi::RESULT_SUCCESS;
            $retry_bulkupdate_response['message'] = '';
            unset($retry_bulkupdate_response['code']);
        } else {
            // messageが1つでもあればERROR_CODE_BULKUPDATEと判断する
            $retry_bulkupdate_response['result'] = \Client_Neapi::RESULT_ERROR;
            $retry_bulkupdate_response['code'] = \Client_Neapi::ERROR_CODE_BULKUPDATE;
        }

        return $retry_bulkupdate_response;
    }

    /**
     * 受注伝票一括更新用のxmlを取得する
     * privateにしているが他の箇所でも使いたくなった場合はアクセスレベルをあげても良い
     * 今回はxmlにattributeをつける必要があり、FuelPHPのto_xmlではattributeをつけるのが難しいためSimpleXMLを利用することにした
     * @param array $receive_orders
     * 例:
     * array(2) {
     *   [0]=>
     *   array(6) {
     *     ["receive_order_total_amount"]=>float(102400)
     *     ["receive_order_note"]=>string(74) "備考欄を上書きする設定
     * 上記は総合計を1倍にする設定"
     *     ["receive_order_worker_text"]=>string(37) "作業用欄に適当なテキスト3"
     *     ["receive_order_option_noshi"]=>string(34) "のし欄に適当なテキスト3"
     *     ["receive_order_id"]=>string(1) "1"
     *     ["receive_order_last_modified_date"]=>string(19) "2018-04-13 10:38:28"
     *   }
     *   [1]=>{...}
     * }
     * @return string $xml
     * 例:
     * <?xml version="1.0" encoding="utf-8"?>
     *   <root>
     *     <receiveorder receive_order_id="1" receive_order_last_modified_date="2018-04-13 10:38:28">
     *       <receiveorder_base>
     *         <receive_order_total_amount>102400</receive_order_total_amount>
     *         <receive_order_note>備考欄を上書きする設定
     *   上記は総合計を1倍にする設定</receive_order_note>
     *         <receive_order_worker_text>作業用欄に適当なテキスト3</receive_order_worker_text>
     *       </receiveorder_base>
     *       <receiveorder_option>
     *         <receive_order_option_noshi>のし欄に適当なテキスト3</receive_order_option_noshi>
     *       </receiveorder_option>
     *     </receiveorder>
     *     <receiveorder receive_order_id="2" receive_order_last_modified_date="2018-04-12 16:47:14">
     *       ...
     *     </receiveorder>
     *   </root>
     */
    private static function _get_receive_order_bulkupdate_xml(array $receive_orders) : string {
        // 更新対象のフィールド
        $base_update_fields   = \Model_Receiveordercolumn::get_physical_names(false, \Model_Receiveordersection::RECEIVE_ORDER_BASE);
        $option_update_fields = \Model_Receiveordercolumn::get_physical_names(false, \Model_Receiveordersection::RECEIVE_ORDER_OPTION);

        // 更新対象のフィールドに含まれていないが、システムで自動で更新する対象となる項目の追加
        $base_update_fields[] = 'receive_order_confirm_ids';

        $root_node = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><root></root>");
        foreach($receive_orders as $receive_order){
            $receiveorder_node = $root_node->addChild('receiveorder');
            $receiveorder_node->addAttribute('receive_order_id', $receive_order['receive_order_id']);
            $receiveorder_node->addAttribute('receive_order_last_modified_date', $receive_order['receive_order_last_modified_date']);
            $receiveorder_base_node   = $receiveorder_node->addChild('receiveorder_base');
            $receiveorder_option_node = $receiveorder_node->addChild('receiveorder_option');

            // XMLを生成する際に特殊文字があるとエラーになるケースがあるためhtmlspecialcharsをはさむ
            // @see http://weble.org/2011/06/19/xml-escape
            foreach($receive_order as $key => $value){
                if(in_array($key, $base_update_fields, true)){
                    $receiveorder_base_node->addChild($key, htmlspecialchars($value));
                }

                if(in_array($key, $option_update_fields, true)){
                    $receiveorder_option_node->addChild($key, htmlspecialchars($value));
                }
            }
        }
        $xml = $root_node->asXML();
        return $xml;
    }
}