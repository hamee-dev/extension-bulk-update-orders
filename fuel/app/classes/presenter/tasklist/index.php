<?php
/**
 * タスク一覧画面のviewモデル
 *
 * Class Presenter_Tasklist_Index
 */
class Presenter_Tasklist_Index extends Presenter_Base
{
    public function view()
    {
        // 更新設定一覧を取得する
        $tasklist = Model_Executionbulkupdatesetting::get_tasklist($this->company_id);
        foreach ($tasklist as $index => $task) {
            // 名称が空の場合は表示用の値を入れておく
            if($task->name === ''){
                $tasklist[$index]->name = __p('no_name');
            }
        }
        $this->tasklist = $tasklist;

        // トップへ遷移するurl
        $query = [
            PARAM_EXECUTION_METHOD => EXECUTION_METHOD_CONFIG,
            \Config::get('security.csrf_token_key') => \Security::fetch_token()
        ];
        $this->top_url = '/top?' . http_build_query($query);
    }
}