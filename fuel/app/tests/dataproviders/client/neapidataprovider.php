<?php

class Client_Neapidataprovider
{
    public function data_provider_apiExecute() {
        return [
            [
                // リトライしない場合（成功した場合）
                'is_retry' => false,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value']
                ],
                'count' => 1
            ],
            [
                // リトライしない場合（レスポンスがエラーだった場合）
                'is_retry' => false,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                ],
                'count' => 1
            ],
            [
                // リトライする場合（1回目のリトライで成功した場合）
                'is_retry' => true,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value']
                ],
                'count' => 2
            ],
            [
                // リトライする場合（2回目のリトライで成功した場合）
                'is_retry' => true,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value']
                ],
                'count' => 3
            ],
            [
                // リトライする場合（3回目のリトライで成功した場合）
                'is_retry' => true,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value']
                ],
                'count' => 4
            ],
            [
                // リトライする場合（リトライ3回目も失敗した場合）
                'is_retry' => true,
                'api_response_list' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001']
                ],
                'count' => 4
            ],
            [
                // is_retry=trueだが、リトライしないエラーコードだった場合
                'is_retry' => true,
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000002']
                ],
                'count' => 1
            ],
        ];
    }

    public function data_provider_apiExecute_token() {

        return [
            [
                // リクエスト成功時にアクセストークンの有効期限が新しい場合、DBのアクセストークンとリフレッシュトークンが更新されること
                'request_api_params_list' => [
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token'],
                ],
                'api_response_list' => [
                    [
                        'result' => Client_Neapi::RESULT_SUCCESS,
                        'access_token' => 'new_access_token',
                        'access_token_end_date' => '2050-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token',
                        'refresh_token_end_date' => '2050-01-01 00:00:00'
                    ]
                ],
                'is_update' => true,
                'user_token_data' => [
                    'access_token' => 'new_access_token',
                    'access_token_end_date' => '2050-01-01 00:00:00',
                    'refresh_token' => 'new_refresh_token',
                    'refresh_token_end_date' => '2050-01-01 00:00:00'
                ],
                'count' => 1
            ],
            [
                // リクエスト成功時にアクセストークンの有効期限が古いもしくは変わっていない場合、DBのアクセストークンとリフレッシュトークンが更新されないこと
                'request_api_params_list' => [
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token'],
                ],
                'api_response_list' => [
                    [
                        'result' => Client_Neapi::RESULT_SUCCESS,
                        'access_token' => 'new_access_token',
                        'access_token_end_date' => '1980-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token',
                        'refresh_token_end_date' => '1980-01-01 00:00:00'
                    ]
                ],
                'is_update' => false,
                'user_token_data' => [],
                'count' => 1
            ],
            [
                // リトライ時にアクセストークンの有効期限が新しい場合、DBのアクセストークンとリフレッシュトークンが更新されること(最初のリクエストでアクセストークンが変わっている場合)
                'request_api_params_list' => [
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token'],
                    ['access_token' => 'new_access_token1', 'refresh_token' => 'new_refresh_token1']
                ],
                'api_response_list' => [
                    [
                        'result' => Client_Neapi::RESULT_ERROR,
                        'code' => '000001',
                        'access_token' => 'new_access_token1',
                        'access_token_end_date' => '2050-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token1',
                        'refresh_token_end_date' => '2050-01-01 00:00:00'
                    ],
                    [
                        'result' => Client_Neapi::RESULT_SUCCESS,
                        'access_token' => 'new_access_token2',
                        'access_token_end_date' => '2050-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token2',
                        'refresh_token_end_date' => '2050-01-01 00:00:00'
                    ]
                ],
                'is_update' => true,
                'user_token_data' => [
                    'access_token' => 'new_access_token1',
                    'access_token_end_date' => '2050-01-01 00:00:00',
                    'refresh_token' => 'new_refresh_token1',
                    'refresh_token_end_date' => '2050-01-01 00:00:00'
                ],
                'count' => 2
            ],
            [
                // リトライ時にアクセストークンの有効期限が新しい場合、DBのアクセストークンとリフレッシュトークンが更新されること(最初のリクエストではアクセストークンが変わっていないが、二回目のリクエストで変わっている場合)
                'request_api_params_list' => [
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token'],
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token']
                ],
                'api_response_list' => [
                    [
                        'result' => Client_Neapi::RESULT_ERROR,
                        'code' => '000001',
                        'access_token' => 'new_access_token1',
                        'access_token_end_date' => '1980-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token1',
                        'refresh_token_end_date' => '1980-01-01 00:00:00'
                    ],
                    [
                        'result' => Client_Neapi::RESULT_SUCCESS,
                        'access_token' => 'new_access_token2',
                        'access_token_end_date' => '2050-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token2',
                        'refresh_token_end_date' => '2050-01-01 00:00:00'
                    ]
                ],
                'is_update' => true,
                'user_token_data' => [
                    'access_token' => 'new_access_token2',
                    'access_token_end_date' => '2050-01-01 00:00:00',
                    'refresh_token' => 'new_refresh_token2',
                    'refresh_token_end_date' => '2050-01-01 00:00:00'
                ],
                'count' => 2
            ],
            [
                // リトライ時にアクセストークンの有効期限が古いもしくは変わっていない場合、DBのアクセストークンとリフレッシュトークンが更新されないこと
                'request_api_params_list' => [
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token'],
                    ['access_token' => 'dummy_access_token', 'refresh_token' => 'dummy_refresh_token']
                ],
                'api_response_list' => [
                    [
                        'result' => Client_Neapi::RESULT_ERROR,
                        'code' => '000001',
                        'access_token' => 'new_access_token',
                        'access_token_end_date' => '1980-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token',
                        'refresh_token_end_date' => '1980-01-01 00:00:00'
                    ],
                    [
                        'result' => Client_Neapi::RESULT_SUCCESS,
                        'access_token' => 'new_access_token',
                        'access_token_end_date' => '1980-01-01 00:00:00',
                        'refresh_token' => 'new_refresh_token',
                        'refresh_token_end_date' => '1980-01-01 00:00:00'
                    ]
                ],
                'is_update' => false,
                'user_token_data' => [],
                'count' => 2
            ],
        ];
    }

    public function data_provider_apiExecuteNoRequiredLogin() {
        return [
            [
                // リトライしない場合（成功した場合）
                'is_retry' => false,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value']
                ],
                'count' => 1,
            ],
            [
                // リトライしない場合（レスポンスがエラーだった場合）
                'is_retry' => false,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001']
                ],
                'count' => 1,
            ],
            [
                // リトライする場合（1回目のリトライで成功した場合）
                'is_retry' => true,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value'],
                ],
                'count' => 2,
            ],
            [
                // リトライする場合（2回目のリトライで成功した場合）
                'is_retry' => true,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value'],
                ],
                'count' => 3,
            ],
            [
                // リトライする場合（3回目のリトライで成功した場合）
                'is_retry' => true,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_SUCCESS, 'data' => 'value'],
                ],
                'count' => 4,
            ],
            [
                // リトライする場合（3回目のリトライでも失敗した場合）
                'is_retry' => true,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                ],
                'count' => 4,
            ],
            [
                // is_retry=trueだが、リトライしないエラーコードだった場合
                'is_retry' => true,
                'api_params' => ['data1' => 'value1', 'data2' => 'value2'],
                'api_response' => [
                    ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000002']
                ],
                'count' => 1,
            ],
        ];
    }

    public function data_provider_is_execute_retry() {
        return [
            [
                // is_retry=falseであればリトライしない
                'is_retry' => false,
                'response' => ['result' => Client_Neapi::RESULT_SUCCESS],
                'is_execute_retry' => false
            ],
            [
                // is_retry=falseであればリトライしない
                'is_retry' => false,
                'response' => ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                'is_execute_retry' => false
            ],
            [
                // is_retry=falseであればリトライしない
                'is_retry' => false,
                'response' => ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000002'],
                'is_execute_retry' => false
            ],
            [
                // is_retry=trueでもレスポンスが成功していればリトライしない
                'is_retry' => true,
                'response' => ['result' => Client_Neapi::RESULT_SUCCESS],
                'is_execute_retry' => false
            ],
            [
                // is_retry=trueでリトライできるエラーコードの場合はリトライする(000001)
                'is_retry' => true,
                'response' => ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000001'],
                'is_execute_retry' => true
            ],
            [
                // is_retry=trueでリトライできるエラーコードの場合はリトライする(002002)
                'is_retry' => true,
                'response' => ['result' => Client_Neapi::RESULT_ERROR, 'code' => '002002'],
                'is_execute_retry' => true
            ],
            [
                // is_retry=trueでもリトライできないエラーコードの場合はリトライしない
                'is_retry' => true,
                'response' => ['result' => Client_Neapi::RESULT_ERROR, 'code' => '000002'],
                'is_execute_retry' => false
            ],
        ];
    }

    public function data_provider_update_user_access_token() {
        // $this->user=nullの条件もあったほうがいいが、その場合に確認する術がないため除外する
        return [
            [
                // access_token_end_dateが古い場合、アクセストークンとリフレッシュトークンが更新されないこと
                'response' => [
                    'access_token' => 'new_access_token',
                    'access_token_end_date' => '1980-01-01 00:00:00',
                    'refresh_token' => 'new_refresh_token',
                    'refresh_token_end_date' => '1980-01-01 00:00:00'
                ],
                'is_update' => false,
            ],
            [
                // access_token_end_dateが新しい場合、アクセストークンとリフレッシュトークンが更新されること
                'response' => [
                    'access_token' => 'new_access_token',
                    'access_token_end_date' => '2050-01-01 00:00:00',
                    'refresh_token' => 'new_refresh_token',
                    'refresh_token_end_date' => '2050-01-01 00:00:00'
                ],
                'is_update' => true,
            ],
        ];
    }

    public function data_provider_set_access_token() {
        return [
            [
                // コンストラクタにユーザーIDを渡していて、set_access_tokenの引数がnullの場合、コンストラクタに渡したユーザーIDのユーザーのアクセストークンとリフレッシュトークンがセットされていること
                'neapi_user_id' => Test_Client_Neapi::DUMMY_USER_ID1,
                'set_access_token_user_id' => null,
                'access_token' => 'dummy_access_token',
                'refresh_token' => 'dummy_refresh_token',
            ],
            [
                // コンストラクタに存在しないユーザーIDを渡していて、set_access_tokenの引数がnullの場合、アクセストークンとリフレッシュトークンはnullになっていること
                'neapi_user_id' => Test_Client_Neapi::DUMMY_USER_ID3,
                'set_access_token_user_id' => null,
                'access_token' => null,
                'refresh_token' => null,
            ],
            [
                // コンストラクタにユーザーIDを渡していて、set_access_tokenの引数でもユーザーIDを渡している場合、set_access_tokenの引数のユーザーIDのユーザーのアクセストークンとリフレッシュトークンがセットされていること
                'neapi_user_id' => Test_Client_Neapi::DUMMY_USER_ID1,
                'set_access_token_user_id' => Test_Client_Neapi::DUMMY_USER_ID2,
                'access_token' => 'dummy_access_token2',
                'refresh_token' => 'dummy_refresh_token2',
            ],
            [
                // コンストラクタにユーザーIDを渡しておらず、set_access_tokenの引数もnullの場合、アクセストークンとリフレッシュトークンはnullになっていること
                'neapi_user_id' => null,
                'set_access_token_user_id' => null,
                'access_token' => null,
                'refresh_token' => null,
            ],
            [
                // コンストラクタにユーザーIDを渡しておらず、set_access_tokenの引数にユーザーIDを渡している場合、set_access_tokenの引数のユーザーIDのユーザーのアクセストークンとリフレッシュトークンがセットされていること
                'neapi_user_id' => null,
                'set_access_token_user_id' => Test_Client_Neapi::DUMMY_USER_ID1,
                'access_token' => 'dummy_access_token',
                'refresh_token' => 'dummy_refresh_token',
            ],
            [
                // コンストラクタにユーザーIDを渡しておらず、set_access_tokenの引数に存在しないユーザーIDを渡している場合、アクセストークンとリフレッシュトークンはnullになっていること
                'neapi_user_id' => null,
                'set_access_token_user_id' => Test_Client_Neapi::DUMMY_USER_ID3,
                'access_token' => null,
                'refresh_token' => null,
            ],
            [
                // コンストラクタにユーザーIDを渡していて、set_access_tokenの引数に存在しないユーザーIDを渡している場合、コンストラクタに渡したユーザーIDのユーザーのアクセストークンとリフレッシュトークンがセットされていること
                'neapi_user_id' => Test_Client_Neapi::DUMMY_USER_ID1,
                'set_access_token_user_id' => Test_Client_Neapi::DUMMY_USER_ID3,
                'access_token' => 'dummy_access_token',
                'refresh_token' => 'dummy_refresh_token',
            ],
        ];
    }
}