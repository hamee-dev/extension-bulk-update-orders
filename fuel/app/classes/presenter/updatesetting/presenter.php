<?php
/**
 * 更新設定新規作成画面・更新設定編集画面の共通viewモデル
 *
 * Class Presenter_Updatesetting_Presenter
 */

class Presenter_Updatesetting_Presenter extends Presenter_Base
{
    public function before()
    {
        parent::before();

        $setting = $this->setting;

        // 設定情報がある場合は取得して、画面生成用のデータをviewに渡す
        $updatesetting_by_setting = self::_get_view_info_by_setting($this->company_id, $this->user_id, $setting);

        // テンプレートファイル情報の配列
        $this->get_view()->set_global('template_file_list', $updatesetting_by_setting->get_template_file_list());

        // 更新項目の注意文言テンプレートファイル情報の配列
        $this->get_view()->set_global('caution_template_file_list', $updatesetting_by_setting->get_caution_template_file_list());

        // 設定しているマスタデータの配列
        $this->get_view()->set_global('master_options_list', $updatesetting_by_setting->get_master_options_list());

        // 発送方法タイプ別区分の配列
        $this->get_view()->set_global('forwarding_agent_options', $updatesetting_by_setting->get_forwarding_agent_options());

        // 受注分類タグの配列（受注分類タグを設定していない場合は空配列）
        $this->get_view()->set_global('tag_list', $updatesetting_by_setting->get_tag_list());

        // 伝票に関する高度な更新設定が初期表示で開いているかどうか
        $this->get_view()->set_global('is_open_option', $updatesetting_by_setting->is_open_option());

        // カラム情報をすべて取得し、画面生成用のデータをviewに渡す
        $updatesetting_by_column = self::_get_view_info_by_columns($setting);

        // jsの変数として埋め込むための全カラム情報の配列
        $this->get_view()->set_global('js_columns', $updatesetting_by_column->get_columns());

        // 各カラムごとの更新方法の配列
        $this->get_view()->set_global('update_mehod_options_list', $updatesetting_by_column->get_update_mehod_options_list());

        // jsの変数として埋め込むための発送関連情報のタイプ情報の配列
        $this->get_view()->set_global('js_delivery_column_ids', $updatesetting_by_column->get_delivery_column_ids());

        // jsの変数として埋め込むための支払関連情報のタイプ情報の配列
        $this->get_view()->set_global('js_payment_column_ids', $updatesetting_by_column->get_payment_column_ids());

        // jsの変数として埋め込むための受注金額関連情報のタイプ情報の配列
        $this->get_view()->set_global('js_order_amount_column_ids', $updatesetting_by_column->get_order_amount_column_ids());

        // jsの変数として埋め込むための発送方法別タイプの配列
        $this->get_view()->set_global('js_forwarding_agent_types', $updatesetting_by_column->get_forwarding_agent_types());

        // 更新する項目一覧
        $this->get_view()->set_global('target_list', $updatesetting_by_column->get_target_list());

        // 発送方法関連の連想配列
        $this->get_view()->set_global('forwarding_agent_column_list', $updatesetting_by_column->get_forwarding_agent_column_list());

        // jsの変数として埋め込むためのマスタを取得する(キャッシュのみを取得する)
        $this->get_view()->set_global('js_master_list', self::_get_cache_master_list($this->company_id));

        // 高度な設定
        $option_list = [
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT,
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME,
        ];
        $this->get_view()->set_global('option_list', $option_list);
    }

    /**
     * 設定情報から画面生成用のデータを取得する
     *
     * @param string $company_id
     * @param string $user_id
     * @param Model_Bulkupdatesetting $setting 設定情報
     * @return Domain_Value_Updatesettingbysetting
     */
    private static function _get_view_info_by_setting(string $company_id, string $user_id, Model_Bulkupdatesetting $setting) : Domain_Value_Updatesettingbysetting {
        $master = new Utility_Master($company_id, $user_id);

        $template_file_list = [];
        $caution_template_file_list = [];
        $master_options_list = [];
        $forwarding_agent_names = [];
        $delivery_id = null;

        foreach ($setting->bulk_update_columns as $index => $bulk_update_column) {

            $template_file_list[$index] = static::_get_view_path($bulk_update_column);

            $caution_template_file_list[$index] = static::_get_caution_view_path($bulk_update_column->receive_order_column);

            $column_type = $bulk_update_column->receive_order_column->column_type;
            if ($column_type->is_master()) {
                $master_name = $bulk_update_column->receive_order_column->master_name;
                if (!Utility_Master::is_forwarding_agent($master_name)) {
                    // マスタの選択肢（selectのoption）用の連想配列を作成する
                    $master_options_list[$master_name] = static::_get_master_for_options($master, $master_name);

                    // 発送方法の場合、発送方法IDを保持しておき、発送方法別タイプの取得に使用する
                    if ($master_name === Utility_Master::MASTER_NAME_DELIVERY) {
                        $delivery_id = $bulk_update_column->update_value;
                    }
                }else{
                    $forwarding_agent_names[] = $master_name;
                }
            }

        }

        return new Domain_Value_Updatesettingbysetting(
            $template_file_list,
            $caution_template_file_list,
            $master_options_list,
            $delivery_id && count($forwarding_agent_names) > 0 ? static::_get_forwarding_agent_options($delivery_id, $forwarding_agent_names, $master) : [],
            $setting->is_selected_type_tag() ? static::_get_tag_list($master) : [],
            $setting->is_selected_option()
        );
    }

    /**
     * カラム情報から画面生成用のデータを取得する
     *
     * @param Model_Bulkupdatesetting $setting
     * @return Domain_Value_Updatesettingbycolumn
     */
    private static function _get_view_info_by_columns(Model_Bulkupdatesetting $setting) : Domain_Value_Updatesettingbycolumn {
        $receive_order_columns = Model_Receiveordercolumn::get_all_columns(false);

        $columns = [];
        $forwarding_agent_types = [];
        $delivery_column_ids = [];
        $payment_column_ids = [];
        $order_amount_column_ids = [];
        $update_mehod_options_list = [];
        foreach ($receive_order_columns as $receive_order_column) {

            $columns[$receive_order_column->id] = static::_get_receive_order_column_for_array($receive_order_column);

            $update_mehod_options_list[$receive_order_column->id] = static::_get_update_mehod_options($receive_order_column->column_type->column_types_update_methods);

            if ($receive_order_column->is_delivery()) {
                $delivery_column_ids[] = $receive_order_column->id;

                if (static::_is_forwarding_agent_column($receive_order_column)) {
                    $forwarding_agent_types[Utility_Master::get_forwarding_agent_type($receive_order_column->master_name)]
                        = ['id' => $receive_order_column->id, 'name' => $receive_order_column->logical_name];
                }
            }
            if ($receive_order_column->is_payment()) {
                $payment_column_ids[] = $receive_order_column->id;
            }
            if ($receive_order_column->is_order_amount()) {
                $order_amount_column_ids[] = $receive_order_column->id;
            }
        }

        return new Domain_Value_Updatesettingbycolumn(
            $columns,
            $update_mehod_options_list,
            $delivery_column_ids,
            $payment_column_ids,
            $order_amount_column_ids,
            $forwarding_agent_types,
            static::_get_target_list($receive_order_columns, $setting),
            static::_get_forwarding_agent_column_list($receive_order_columns, $setting));
    }

    /**
     * 発送方法別タイプのセレクトボックス作成用のoption情報を配列で取得する
     *
     * @param string $delivery_id 発送方法のID(無い場合はnull)
     * @param array $forwarding_agent_names 発送方法別タイプ名の配列
     * @param Utility_Master $master マスタオブジェクト
     * @return array option情報の配列
     */
    private static function _get_forwarding_agent_options(string $delivery_id, array $forwarding_agent_names, Utility_Master $master) : array {
        $forwarding_agent_options = [];
        foreach ($forwarding_agent_names as $forwarding_agent_name) {
            $forwarding_agent_master = $master->get_forwarding_agent(true, $delivery_id, $forwarding_agent_name);
            if (!empty($forwarding_agent_master)) {
                $forwarding_agent_option = ['' => ''];
                foreach ($forwarding_agent_master as $value => $data) {
                    $forwarding_agent_option[$data->get_id()] = $data->get_name();
                }
                $forwarding_agent_options[$forwarding_agent_name] = $forwarding_agent_option;
            }
        }
        return $forwarding_agent_options;
    }

    /**
     * Model_Receiveordercolumnを配列にして返す
     * また、カラムの情報名、使うテンプレート名の情報を追加する
     *
     * @param Model_Receiveordercolumn $receive_order_column カラム情報
     * @return array Model_Receiveordercolumnを配列にしたもの
     */
    private static function _get_receive_order_column_for_array(Model_Receiveordercolumn $receive_order_column) : array {
        $data = $receive_order_column->to_array();
        $data['template_name'] = static::_get_template_name($receive_order_column);
        $data['caution_template_name'] = static::_get_caution_template_name($receive_order_column);
        $data['column_type_name'] = static::_get_column_type_name($receive_order_column);
        return $data;
    }

    /**
     * 使用するテンプレート名を取得する
     *
     * @param Model_Receiveordercolumn $receive_order_column カラム情報
     * @return string テンプレート名
     */
    private static function _get_template_name(Model_Receiveordercolumn $receive_order_column) : string {
        $column_type = $receive_order_column->column_type;
        if ($column_type->is_master()) {
            if ($receive_order_column->is_delivery()) {
                $template_name = 'delivery';
            } else {
                $template_name = 'master';
            }
        } else if ($column_type->is_textarea()) {
            $template_name = 'textarea';
        } else if ($column_type->is_bool()) {
            $template_name = 'bool';
        } else if ($column_type->is_date()) {
            $template_name = 'date';
        } else if ($column_type->is_tag()) {
            $template_name = 'tag';
        } else {
            $template_name = 'textbox';
        }

        return $template_name;
    }

    /**
     * 更新項目の注意文言テンプレート名を取得する
     *
     * @param Model_Receiveordercolumn $receive_order_column カラム情報
     * @return string テンプレート名
     */
    private static function _get_caution_template_name(Model_Receiveordercolumn $receive_order_column) : ?string {

        if($receive_order_column->is_payment_method_id()) {
            // 支払方法の場合

            return 'caution_payment';
        }

        if($receive_order_column->is_order_amount()) {
            // 受注金額関連項目の場合

            if($receive_order_column->is_total_amount()) {
                // 総合計の場合

                return 'caution_order_amount_total';
            }

            return 'caution_order_amount_detail';
        }

        return null;
    }

    /**
     * カラムの情報名を取得する
     *
     * @param Model_Receiveordercolumn $receive_order_column カラム情報
     * @return string カラムの情報名
     */
    private static function _get_column_type_name(Model_Receiveordercolumn $receive_order_column) : string {
        $column_type = $receive_order_column->column_type;
        $column_type_name = '';
        if ($column_type->is_master() && !$receive_order_column->is_delivery()) {
            $column_type_name = 'master';
        } else if ($column_type->is_textarea()) {
            $column_type_name = 'textarea';
        } else if ($column_type->is_bool()) {
            $column_type_name = 'bool';
        } else if ($column_type->is_date()) {
            $column_type_name = 'date';
        } else if ($column_type->is_tag()) {
            $column_type_name = 'tag';
        } else {
            if ($column_type->is_string()) {
                $column_type_name = 'string';
            }else if ($column_type->is_email()) {
                $column_type_name = 'email';
            }else if ($column_type->is_number()) {
                $column_type_name = 'number';
            }else if ($column_type->is_telephone()) {
                $column_type_name = 'telephone';
            }else if ($column_type->is_zip()) {
                $column_type_name = 'zip';
            }
        }
        return $column_type_name;
    }

    /**
     * 更新方法のセレクトボックス作成用のoption情報を配列で取得する
     *
     * @param array $column_types_update_methods Model_Columntypesupdatemethodの配列
     * @return array option情報の配列
     */
    private static function _get_update_mehod_options(array $column_types_update_methods) : array {
        $options = [];
        foreach ($column_types_update_methods as $column_types_update_method) {
            $update_method = $column_types_update_method->update_method;
            $options[$update_method->id] = $update_method->name;
        }
        return $options;
    }

    /**
     * 発送方法別タイプのカラムかどうか
     *
     * @param Model_Receiveordercolumn $receive_order_column カラム情報
     * @return bool true:発送方法別タイプのカラム/false:発送方法別タイプのカラムではない
     */
    private static function _is_forwarding_agent_column(Model_Receiveordercolumn $receive_order_column) : bool {
        if ($receive_order_column->is_delivery() &&
            Utility_Master::is_forwarding_agent($receive_order_column->master_name)) {
                return true;
        }
        return false;
    }

    /**
     * 利用するテンプレートのファイルパスを取得する
     *
     * @param Model_Bulkupdatecolumn $bulk_update_column カラム情報
     * @return string ファイルパス
     */
    private static function _get_view_path(Model_Bulkupdatecolumn $bulk_update_column) : string {
        $column_type = $bulk_update_column->receive_order_column->column_type;
        $view_path = '';
        if ($column_type->is_bool()) {
            $view_path .= '_bool';
        }else if ($column_type->is_date()) {
            $view_path .= '_date';
        }else if ($column_type->is_master()) {
            $master_name = $bulk_update_column->receive_order_column->master_name;
            if (!Utility_Master::is_forwarding_agent($master_name)) {
                if ($master_name === Utility_Master::MASTER_NAME_DELIVERY) {
                    // 発送方法のマスタ
                    $view_path .= '_delivery';
                }else{
                    $view_path .= '_master';
                }
            }else{
                // 発送方法別区分は発送方法に含まれるため$view_pathは設定しない
            }
        }else if ($column_type->is_textarea()) {
            $view_path .= '_text_area';
        }else if ($column_type->is_tag()) {
            $view_path .= '_tag';
        }else{
            $view_path .= '_text_box';
        }
        return $view_path !== '' ? 'updatesetting/templates/' . $view_path : '';
    }

    /**
     * 更新項目の注意文言テンプレートのファイルパスを取得する
     *
     * @param Model_Receiveordercolumn $receive_order_column 更新対象の受注伝票カラム情報
     * @return string ファイルパス
     */
    private static function _get_caution_view_path(Model_Receiveordercolumn $receive_order_column) : ?string {

        $template_name = static::_get_caution_template_name($receive_order_column);

        if(is_null($template_name)) {
            return null;
        }

        // テンプレートファイルパス
        return 'updatesetting/templates/_' . $template_name;
    }

    /**
     * マスター情報のセレクトボックス作成用のoption情報を配列で取得する
     *
     * @param Utility_Master $master マスタオブジェクト
     * @param string $master_name マスタ名
     * @return array option情報の配列
     */
    private static function _get_master_for_options(Utility_Master $master, string $master_name) : array {
        $master_data = $master->get($master_name);
        $options = [];
        foreach ($master_data as $value => $data) {
            $options[$value] = $data->get_name();
        }
        return $options;
    }

    /**
     * タグ一覧を表示するための情報を配列で取得する
     *
     * @param Utility_Master $master マスタオブジェクト
     * @return array タグ一覧の配列
     */
    private static function _get_tag_list(Utility_Master $master) : array {
        $tag_master_data = $master->get(Utility_Master::MASTER_NAME_TAG);
        $tag_list = [];
        foreach ($tag_master_data as $value => $data) {
            $params = $data->get_params();
            $style = 'background-color: ' . $params['grouping_tag_color'] . '; ';
            $style .= 'color: ' . $params['grouping_tag_str_color'] . '; ';
            $style .= 'border: 1px solid ' . $params['grouping_tag_color'] . '; ';
            $tag_list[] = ['name' => $data->get_name(), 'style' => $style];
        }
        return $tag_list;
    }

    /**
     * キャッシュからマスタ情報を取得する
     *
     * @param string $company_id
     * @return array マスタ情報の配列
     */
    private static function _get_cache_master_list(string $company_id) : array {
        $master = new Utility_Master($company_id);
        $master_list = [];
        foreach (Utility_Master::get_master_names() as $master_name) {
            foreach ($master->get($master_name) as $data) {
                if ($data instanceof Domain_Value_Master) {
                    if (!isset($master_list[$master_name])) {
                        $master_list[$master_name] = [];
                    }
                    $master_list[$master_name][] = $data->to_array();
                }
            }
        }
        return $master_list;
    }

    /**
     * 発送方法別タイプのカラム情報の一覧を取得する
     *
     * @param array $receive_order_columns カラム情報
     * @param Model_Bulkupdatesetting $setting 設定情報
     * @return array カラム情報の一覧
     * @throws ErrorException
     */
    private static function _get_forwarding_agent_column_list(array $receive_order_columns, Model_Bulkupdatesetting $setting) : array {

        $forwarding_agent_column_list = [];
        foreach ($receive_order_columns as $receive_order_column) {
            if ($receive_order_column->is_delivery() && self::_is_forwarding_agent_column($receive_order_column)) {
                $forwarding_agent_column = null;
                // 設定済みの情報を取得する
                foreach ($setting->bulk_update_columns as $check_column) {
                    if ($check_column->receive_order_column_id === $receive_order_column->id) {
                        $forwarding_agent_column = $check_column;
                        break;
                    }
                }
                if (!$forwarding_agent_column) {
                    // 設定済みの情報がない場合は、画面表示用にオブジェクトを作成する
                    $forwarding_agent_column = new Model_Bulkupdatecolumn();
                    $forwarding_agent_column->receive_order_column = $receive_order_column;
                    $forwarding_agent_column->receive_order_column_id = $receive_order_column->id;
                    $forwarding_agent_column->update_method_id = current($receive_order_column->column_type->column_types_update_methods)->update_method_id;
                }
                $forwarding_agent_column_list[] = $forwarding_agent_column;
            }
        }
        return $forwarding_agent_column_list;
    }

    /**
     * 更新する項目一覧を取得する
     *
     * @param array $columns 設定可能な更新項目の配列（Model_Receiveordercolumnの配列）
     * @param Model_Bulkupdatesetting $setting 設定情報
     * @return array 更新する項目の配列
     *  $target_list[receive_order_columnのid] = ['name' => '項目名', 'is_display' => 'true:表示/false:非表示'];
     *  の形
     */
    private static function _get_target_list(array $columns, Model_Bulkupdatesetting $setting) : array {
        $target_list = ['' => ['name' => __('page.updatesetting.no_select_value'), 'is_display' => true]]; // 未選択時の項目

        // 選択している項目のIDを取得する
        $select_receive_order_column_ids =
            array_column($setting->bulk_update_columns, 'receive_order_column_id');

        // 特殊制御が必要な項目の選択状態の情報を取得
        // （is_selected_xxxxはメソッド内で更新項目数分のループ処理があるので事前に保持しておく）
        $is_selected_delivery = $setting->is_selected_delivery();
        $is_selected_payment_method_id = $setting->is_selected_payment_method_id();
        $is_selected_payment = $setting->is_selected_payment();
        $is_selected_total_amount = $setting->is_selected_total_amount();
        $is_selected_order_amount = $setting->is_selected_order_amount();

        foreach ($columns as $receive_order_column) {
            /**
             * 項目を非表示にする
             *
             * 非表示にする条件
             * ・選択している項目の場合
             * ・発送関連項目を選択している場合（発送関連の項目を非表示にする）
             * ・支払方法が選択されている場合（支払関連項目を非表示にする）
             * ・支払関連項目が選択されている場合（支払方法を非表示にする）
             * ・総合計が選択されている場合（受注金額関連項目を非表示にする）
             * ・受注金額関連項目が選択されている場合（総合計を非表示にする）
             */
            $is_display = true;
            if (in_array($receive_order_column->id, $select_receive_order_column_ids) ||
                $receive_order_column->is_delivery() && $is_selected_delivery ||
                $receive_order_column->is_payment() && $is_selected_payment_method_id ||
                $receive_order_column->is_payment_method_id() && $is_selected_payment ||
                $receive_order_column->is_order_amount() && $is_selected_total_amount ||
                $receive_order_column->is_total_amount() && $is_selected_order_amount
            ) {
                $is_display = false;
            }
            $target_list[$receive_order_column->id] = ['name' => $receive_order_column->logical_name, 'is_display' => $is_display];
        }

        // 最後に発送方法関連の項目を追加する
        $target_list[Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE] =
            ['name' => __('page.updatesetting.delivery_column_name'), 'is_display' => false];

        return $target_list;
    }
}