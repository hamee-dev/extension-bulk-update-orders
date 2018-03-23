<?php
class Model_Basedataprovider {
    /**
     * モデルに定義された比較対象としての除外カラムと各リレーション定義の情報の応じた、
     * 比較除外カラム情報の取得パターンを返す
     */
    public function data_provider_exclude_comparison_columns() {
        $data_provider = [];

        $case_desc = '定義された除外カラムの情報が取得できるか';
        $exclude_comparison_columns = [
            'id',
            'created_at',
            'updated_at',
        ];
        $has_one = null;
        $belongs_to = null;
        $has_many = null;
        $many_many = null;
        $eav = null;
        $expected = [
            'id',
            'created_at',
            'updated_at',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義されたhas_oneの情報が取得できるか';
        $exclude_comparison_columns = null;
        $has_one = [
            'has_one_test1' => [],
            'has_one_test2' => [],
        ];
        $belongs_to = null;
        $has_many = null;
        $many_many = null;
        $eav = null;
        $expected = [
            'has_one_test1',
            'has_one_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義されたbelongs_toの情報が取得できるか';
        $exclude_comparison_columns = null;
        $has_one = null;
        $belongs_to = [
            'belongs_to_test1' => [],
            'belongs_to_test2' => [],
        ];
        $has_many = null;
        $many_many = null;
        $eav = null;
        $expected = [
            'belongs_to_test1',
            'belongs_to_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義されたhas_manyの情報が取得できるか';
        $exclude_comparison_columns = null;
        $has_one = null;
        $belongs_to = null;
        $has_many = [
            'has_many_test1' => [],
            'has_many_test2' => [],
        ];
        $many_many = null;
        $eav = null;
        $expected = [
            'has_many_test1',
            'has_many_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義されたmany_manyの情報が取得できるか';
        $exclude_comparison_columns = null;
        $has_one = null;
        $belongs_to = null;
        $has_many = null;
        $many_many = [
            'many_many_test1' => [],
            'many_many_test2' => [],
        ];
        $eav = null;
        $expected = [
            'many_many_test1',
            'many_many_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義されたeavの情報が取得できるか';
        $exclude_comparison_columns = null;
        $has_one = null;
        $belongs_to = null;
        $has_many = null;
        $many_many = null;
        $eav = [
            'eav_test1' => [],
            'eav_test2' => [],
        ];
        $expected = [
            'eav_test1',
            'eav_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        $case_desc = '定義された除外カラムとリレーション定義の情報がすべて取得できるか';
        $exclude_comparison_columns = [
            'id',
            'created_at',
            'updated_at',
        ];
        $has_one = [
            'has_one_test1' => [],
            'has_one_test2' => [],
        ];
        $belongs_to = [
            'belongs_to_test1' => [],
            'belongs_to_test2' => [],
        ];
        $has_many = [
            'has_many_test1' => [],
            'has_many_test2' => [],
        ];
        $many_many = [
            'many_many_test1' => [],
            'many_many_test2' => [],
        ];
        $eav = [
            'eav_test1' => [],
            'eav_test2' => [],
        ];
        $expected = [
            'id',
            'created_at',
            'updated_at',
            'has_one_test1',
            'has_one_test2',
            'belongs_to_test1',
            'belongs_to_test2',
            'has_many_test1',
            'has_many_test2',
            'many_many_test1',
            'many_many_test2',
            'eav_test1',
            'eav_test2',
        ];
        $data_provider[$case_desc] = [
            $exclude_comparison_columns,
            $has_one,
            $belongs_to,
            $has_many,
            $many_many,
            $eav,
            $expected
        ];

        return $data_provider;
    }
}