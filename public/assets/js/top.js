/**
 * TOP画面用js
 */
$(function(){
    /**
     * 複製モーダル表示
     */
    $('a.copy').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $.show_text_box_modal(
            {
                action_url: '/updatesetting/copy',
                title: '設定複製',
                description: '設定名「' + name + '」を複製します',
                label: '複製後の設定名',
                text_maxlength: EXT_BUO.TOP.setting_name_max_length,
                done_button_name: '複製',
                default_value: name,
                add_hidden_data: {bulk_update_setting_id: id},
                callback: save
            }
        );
    });
    
    /**
     * 削除モーダル表示
     */
    $('a.delete').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $.show_modal(
            {
                action_url: '/updatesetting/delete',
                title: '設定削除',
                description: '設定名「' + name + '」を削除しますか？',
                done_button_name: '削除',
                done_button_class: 'btn-danger',
                add_hidden_data: {bulk_update_setting_id: id}
            }
        );
    });

    /**
     * 設定名称変更モーダル
     */
    $('a.update-name').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $.show_text_box_modal(
            {
                action_url: '/updatesetting/updatename',
                title: '設定名称変更',
                description: '保存している設定の名称を変更します',
                label: '設定名',
                text_maxlength: EXT_BUO.TOP.setting_name_max_length,
                done_button_name: '保存',
                default_value: name,
                add_hidden_data: {bulk_update_setting_id: id},
                callback: save
            }
        );
    });

    /**
     * テキストボックス付きモーダルsubmit時に値が入力されているかチェックし、値が入力されていない場合submitを中止する
     *
     * @return {boolean}
     */
    function save() {
        if ($('#text-box-modal .text-box').val().length === 0) {
            $('#text-box-modal .modal-body .alert').html('設定名を入力してください');
            $('#text-box-modal .modal-body .alert').show();
            return false;
        }
    }

    /**
     * 実行ボタンの送信
     */
    $('a.execution').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#top-execution-form').append('<input name="bulk_update_setting_id" value="'+id+'" type="hidden">');
        $('#top-execution-form').submit();
    });
});
