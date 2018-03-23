<?php
/**
 *
 * Class Test_Model_Bulkupdatecolumn
 */
class Test_Model_Bulkupdatecolumn extends Testbase
{
    protected $dataset_filenames = ['model/bulkupdatecolumn.yml'];

    public function test_get_exclude_comparison_columns_比較対象から除外するカラム名とリレーション定義のプロパティ名が取得できること() {
        $model_mock = self::get('Model_Bulkupdatecolumn');

        $method = $this->getMethod('Model_Bulkupdatecolumn', 'get_exclude_comparison_columns');
        $result = $method->invokeArgs(null, []);
        $expect = array_merge(
            [
                'id',
                'bulk_update_setting_id',
                'created_at',
                'updated_at'
            ],
            array_keys($model_mock::getStatic('_belongs_to'))
        );

        $this->assertEquals($expect, $result);
    }

    public function test_get_comparison_columns_比較対象となるカラム配列のみが取得できること() {
        $model = Model_Bulkupdatecolumn::find(self::DUMMY_BULK_UPDATE_COLUMN_ID1);

        $result = $model->get_comparison_columns();
        $expect = [
            'receive_order_column_id' => $model->receive_order_column_id,
            'update_method_id' => $model->update_method_id,
            'update_value' => $model->update_value,
        ];
        $this->assertEquals($expect, $result);
    }
}