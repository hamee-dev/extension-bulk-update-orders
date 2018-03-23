<?php
/**
 * request_keys
 * 一括更新の実行リクエストキー情報
 *
 * Class Model_Requestkey
 */
class Model_Requestkey extends Model_Base
{
    protected static $_table_name = 'request_keys' ;

    protected static $_properties = [
        'id',
        'company_id',
        'request_date',
        'request_number',
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'company' => [
            'model_to'       => 'Model_Company',
            'key_from'       => 'company_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    /**
     * タスクIDを取得する
     *
     * @return string タスクID
     */
    public function get_task_id() : string {
        return date('Ymd', strtotime($this->request_date)) . '-' . $this->request_number;
    }

    /**
     * リクエストキーオブジェクトを作成する
     * このメソッドを呼ぶたびにrequest_numberがインクリメントするため注意
     *
     * @param string $company_id 企業ID
     * @return Model_Requestkey
     * @throws ErrorException
     * @throws Exception
     * @throws FuelException
     */
    public static function create_request_key_object(string $company_id) : Model_Requestkey {
        $request_key = Model_Requestkey::findOne(['company_id' => $company_id]);
        $now_date = date("Y-m-d");
        if (!$request_key) {
            // レコードがないため作成する
            $request_key = new Model_Requestkey();
            $request_key->request_number = 1;
        }else if ($request_key->request_date !== $now_date) {
            // レコードはあるが日付が変わったためrequest_numberを1から開始する
            $request_key->request_number = 1;
        }else{
            // レコードがあり、日付も変わってないためrequest_numberをインクリメントする
            $request_key->request_number = (int)$request_key->request_number + 1;
        }
        $request_key->company_id = $company_id;
        $request_key->request_date = $now_date;
        $request_key->save();

        return $request_key;
    }
}