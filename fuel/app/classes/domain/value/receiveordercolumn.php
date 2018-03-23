<?php
/**
 * 受注伝票項目の画面生成用のデータ
 *
 * Class Domain_Value_Receiveordercolumn
 */
class Domain_Value_Receiveordercolumn
{
    /**
     * 設定名称の最大表示文字数
     */
    const SETTING_NAME_DISPLAY_MAX_LENGTH = 30;

    /**
     * 一括更新の内容の最大表示件数
     */
    const UPDATE_COLUMNS_DISPLAY_MAX_COUNT = 3;

    /**
     * 一括更新の内容の最大表示文字数
     */
    const UPDATE_COLUMNS_DISPLAY_MAX_LENGTH = 100;

    /**
     * 一括更新の内容のタグ名の最大表示文字数
     */
    const TAG_NAME_DISPLAY_MAX_LENGTH = 20;

    /**
     * 一括更新の内容のタグの最大表示数
     */
    const TAG_DISPLAY_MAX_COUNT = 10;

    /**
     * 一括更新の内容の最大表示文字数
     * プレビュー画面はTOP画面より多めに表示
     * NOTE: 暫定値なので都度調整して良い
     */
    const PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH = 1000;

    /**
     * 一括更新の内容のタグ名の最大表示文字数
     * プレビュー画面はTOP画面より多めに表示
     * NOTE: 暫定値なので都度調整して良い
     */
    const PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH = 50;

    /**
     * 一括更新の内容のタグの最大表示数
     * プレビュー画面はTOP画面より多めに表示
     * NOTE: 暫定値なので都度調整して良い
     */
    const PREVIEW_TAG_DISPLAY_MAX_COUNT = 30;

    /**
     * プレビュー画面で初回に表示できる最大伝票数
     * 「もっと見る」でこれ以降を読み込む
     */
    const PREVIEW_DISPLAY_INITIAL_MAX_ORDER_COUNT = 100;

    /**
     * 画面表示用の値を取得する
     * この処理内にサニタイズする処理を内包している
     * 元の値をサニタイズし、サニタイズしたくない画面にそのままHTMLタグとして表示したいものを別途付与している
     *
     * @param Model_Receiveordercolumn $receive_order_column 受注伝票項目のオブジェクト
     * @param Utility_Master $master マスタデータオブジェクト(マスタ型の場合必要になる)
     * @param array $bulk_update_columns 同じ設定の全ての項目情報の連想配列(発送方法別項目タイプマスタの場合必要となる)
     * @param null|string $original_value 表示したい元の値
     * @param bool $is_order_value 伝票の値かどうか(確認チェックの値は伝票の値は0,1,2、設定値は0,1のためこのフラグで分岐している)
     * @param bool $is_preview プレビュー画面かどうか trimする最大長がtop画面と異なるのでその分岐に使う
     * @return string
     */
    public static function get_display_value(Model_Receiveordercolumn $receive_order_column, Utility_Master $master, array $bulk_update_columns, ?string $original_value, bool $is_order_value = false, bool $is_preview = false) : string {
        $value = __em('invalid_value');
        $tag_max_count = $is_preview ? self::PREVIEW_TAG_DISPLAY_MAX_COUNT : self::TAG_DISPLAY_MAX_COUNT;
        $tag_name_max_length = $is_preview ? self::PREVIEW_TAG_NAME_DISPLAY_MAX_LENGTH : self::TAG_NAME_DISPLAY_MAX_LENGTH;
        $max_length = $is_preview ? self::PREVIEW_UPDATE_COLUMNS_DISPLAY_MAX_LENGTH : self::UPDATE_COLUMNS_DISPLAY_MAX_LENGTH;

        // ここまでの値はサニタイズする
        // これ以降の処理で付与したhtmlタグは生で画面に渡される
        $original_value = Security::htmlentities($original_value);

        $column_type = $receive_order_column->column_type;
        if ($column_type->is_master()) {
            // マスタ型の場合はマスタから値を取得
            $master_value = self::_get_master_value(
                $master,
                $bulk_update_columns,
                $receive_order_column->master_name,
                $original_value);
            $value = !is_null($master_value) ? $master_value : $value;
        }else if ($column_type->is_tag()) {
            // タグ型の場合、タグ一覧を配列で取得する
            if(empty($original_value)){
                $value = '';
            } else if (preg_match_all('/\[(.*?)\]/', $original_value, $m)) {
                // [タグ名] の形になっているので正規表現で取得する
                $tags = $m[1];
                // 表示用のタグを生成する
                $value = '<div class="font_bold tag-list">';
                foreach($tags as $index => $tag){
                    if ($index >= $tag_max_count) {
                        $value .= __c('other_tag_count', ['number' => count($tags) - $index]);
                        break;
                    }
                    $value .= '<span>'.trim_length($tag, $tag_name_max_length).'</span>';
                }
                $value .= '</div>';
            }
        }else if ($column_type->is_bool()) {
            // bool型の場合、0=なし, 1=ありの文言を返す
            // NOTE: ただし「確認チェック」の伝票の値のみ1=なし, 2=あり としてそれ以外は空文字とする
            if($is_order_value && (string)$receive_order_column->id === Model_Receiveordercolumn::COLUMN_ID_CONFIRM_CHECK){
                $list = ['1' => 'なし', '2' => 'あり'];
                $value = isset($list[$original_value]) ? $list[$original_value] : '';
            } else {
                $list = ['0' => 'なし', '1' => 'あり'];
                $value = isset($list[$original_value]) ? $list[$original_value] : '';
            }
        }else if ($column_type->is_date()) {
            // 日付型のカラムの場合Y/m/dの形式に変換する（一部メイン機能側の日付で'0000-00-00 00:00:00'になっている場合があるのでその場合は空にする）
            if (strtotime($original_value) === false || $original_value === '0000-00-00 00:00:00') {
                // 空文字など変換できない場合には空文字にする
                $value = '';
            } else {
                $value = date('Y/m/d', strtotime($original_value));
            }
        }else if ($column_type->is_number()) {
            // 数値型の項目で小数点が「.00」の場合、小数点を削除する
            $value = $original_value;
            if ($value == (int)$original_value) {
                $value = (int)$original_value;
            }
        }else{
            if(is_null($original_value)){
                $value = '';
            } else {
                // 元の値がnullの場合trimや改行の置換処理不要
                // その他の場合は描画用に整形する
                $value = nl2br(trim_length($original_value, $max_length));
            }
        }

        // 結果がnullの場合画面に表示する都合上空文字にして返す
        if(is_null($value)) $value = '';
        // 更新値が空文字の場合は「空欄にする」のスタイルにして返す
        if($value === '' && !$is_order_value) $value = __c('empty_update_dom');
        return $value;
    }

    /**
     * マスタの値を取得して、画面表示用の文字列を返す
     *
     * @param Utility_Master $master マスタデータオブジェクト
     * @param array $bulk_update_columns 同じ設定の全ての項目情報の連想配列(発送方法別項目タイプマスタの場合必要となる)
     * @param string $master_name 取得するマスタ名
     * @param null|string $master_id 選択しているマスタのID
     * @return null|string 画面表示用の文字列(取得できなかった場合はnull)
     */
    private static function _get_master_value(Utility_Master $master, array $bulk_update_columns, string $master_name, ?string $master_id) : ?string {
        $value = null;
        if (Utility_Master::is_forwarding_agent($master_name)) {
            // 発送方法別項目タイプマスタの場合、必ず発送方法の更新内容があるはずなので取得する
            foreach ($bulk_update_columns as $data) {
                // マスタ名がdeliveryであれば発送方法
                if ($data->receive_order_column->master_name === Utility_Master::MASTER_NAME_DELIVERY) {
                    // 発送方法別項目タイプマスタを発送方法となんの項目なのかを指定して取得する
                    $master_data = $master->get_forwarding_agent(
                        true,
                        $data->update_value,
                        $master_name);

                    // マスタの値を取得する
                    foreach ($master_data as $index => $data) {
                        if ($master_id === $data->get_id()) {
                            $value = $master_id . ' : ' .$data->get_name();
                            break;
                        }
                    }
                    // 発送方法別項目タイプマスタは空での更新があるので、見つからなければ空文字にする
                    $value = is_null($value) ? '' : $value;
                    break;
                }
            }
        }else{
            // それ以外のマスタの場合
            $master_data = $master->get($master_name);
            if (!is_null($master_data) && isset($master_data[$master_id])) {
                $value = $master_id . ' : ' . $master_data[$master_id]->get_name();
            }
        }
        return $value;
    }

    /**
     * 更新方法を表示するかどうか
     * マスタ型、ブール型、日付型以外でかつ「空欄にする」ではない場合、更新方法を表示する
     *
     * @param Model_Columntype $column_type カラムタイプオブジェクト
     * @param string $value 表示する値
     * @return bool
     */
    public static function is_show_update_method(Model_Columntype $column_type, string $value) : bool {
        if (!$column_type->is_master() && !$column_type->is_bool() && !$column_type->is_date() && $value !== __c('empty_update_dom')) {
            return true;
        }
        return false;
    }
}