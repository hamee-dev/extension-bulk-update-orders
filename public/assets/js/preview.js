/**
 * プレビュー画面用js
 */
$(function(){
    /**
     * 実行ボタンクリック
     */
    $('#preview-execution').on('click', function () {
        var id = $('#form_bulk_update_setting_id').val();
        var path = $('#form_transition_path').val();

        // 実行時の注意事項
        var execute_caution_html = '';
        var execute_cautions = $(this).data('execute_cautions');
        $.each(execute_cautions, function(index, caution) {
            execute_caution_html += '<p class="text-danger">' + caution + '</p>';
        });

        $.show_modal(
            {
                action_url: '/preview/execution',
                title: '実行してもよろしいですか？',
                description: '<p>※ 実行には5~10分かかる場合があります</p>' + execute_caution_html,
                done_button_name: '実行',
                add_hidden_data: {bulk_update_setting_id: id, transition_path: path}
            }
        );
    });

    /**
     * 「さらに表示」ボタンアクション
     * 「伝票ごと」タブの表示
     * NOTE: DOM要素を生成しています。view側の構造が変更する場合にはこちらの処理も追随すること
     */
    var display_each_order = function(receive_order_id, display_value){
        var receive_order_columns_json = EXT_BUO.PREVIEW.receive_order_columns_json;

        var append_dom = $('#order_data_dummy > div').clone();
        append_dom.attr('id', 'receive_order_id_'+receive_order_id);

        if(display_value.excluded_reason === ''){
            append_dom.find('.overlay').addClass('overlay_toggle display-none');
            append_dom.find('.each_order_exclude_button_position > a').attr('data-receive_order_id', receive_order_id)
        } else {
            append_dom.find('.excluded_reason').html(display_value.excluded_reason);
            append_dom.find('.each_order_exclude_button_position').remove();
        }

        append_dom.find('.receive_order_id_strong').html(receive_order_id);
        var update_result_table = append_dom.find('table.update-result-table');
        $.each(receive_order_columns_json, function(index, receive_order_column){
            var physical_name = receive_order_column.physical_name;
            update_result_table.find('.before_value.'+physical_name).html(display_value[physical_name]['before_value']);
            update_result_table.find('.after_value.'+physical_name).html(display_value[physical_name]['after_value']);
        });

        $('#order_data_last_div').before(append_dom);
    };

    /**
     * 「さらに表示」ボタンアクション
     * 「項目ごと」タブの表示
     * NOTE: DOM要素を生成しています。view側の構造が変更する場合にはこちらの処理も追随すること
     */
    var display_each_column = function(receive_order_id, display_value){
        var receive_order_columns_json = EXT_BUO.PREVIEW.receive_order_columns_json;
        var dummy_tr = $('#column_data_dummy').find('tr');
        $.each(receive_order_columns_json, function(index, receive_order_column){
            var physical_name = receive_order_column.physical_name;

            var append_dom = dummy_tr.clone();
            append_dom.addClass('receive_order_id_'+receive_order_id);
            append_dom.find('td.each_column_receive_order_id > div.preview-value').html(receive_order_id);
            append_dom.find('td.each_column_before_value > div.preview-value').html(display_value[physical_name]['before_value']);
            append_dom.find('td.each_column_after_value > div.preview-value').html(display_value[physical_name]['after_value']);
            if(display_value.excluded_reason === ''){
                append_dom.find('td.each_column_exclude > a').attr('data-receive_order_id', receive_order_id)
                append_dom.removeClass('system_exclude');
            } else {
                var excluded_reason = append_dom.find('td.excluded_reason');
                excluded_reason.show();
                excluded_reason.html(display_value.excluded_reason);
                append_dom.find('td.each_column_exclude > a').css('visibility', 'hidden');
            }

            $('#'+physical_name+'_last_tr').before(append_dom);
        });
    };

    /**
     * 「さらに表示」ボタン
     */
    $('.more_display_button').on('click', function(){
        // 100件ずつ表示する
        var per_display = 100;

        // 100件追加で表示する
        var i = 0;
        $.each(EXT_BUO.PREVIEW.display_values_json, function(receive_order_id, display_value){
            if(i >= per_display){
              return false;
            }
            i = i + 1;
            display_each_order(receive_order_id, display_value);
            display_each_column(receive_order_id, display_value);
            // 表示した分をjsonから除去する
            delete EXT_BUO.PREVIEW.display_values_json[receive_order_id];
        });

        // 残りxxx件の表示を更新する
        var value = $('.more_display_other').attr('data-value');
        var next_value = value - per_display;
        if(next_value > 0){
          $('.more_display_other').attr('data-value', next_value);
          $('.more_display_other').text('残り'+next_value+'件');
        } else {
          // 残り表示件数がなくなったら「さらに表示」の領域ごと非表示
          $('.more_display_button_area').remove();
        }

        toggle_execution_button();
    });

    /**
     * 「項目ごと」の除外処理
     * user_excludeのクラスがない場合（除外表示になっていない場合）はuser_excludeクラスを追加して除外表示にする
     *
     * @param selector
     */
    var display_restore_for_each_column = function(selector){
        if ($(selector).find('tr.user_exclude').length === 0) {
            $(selector).addClass('user_exclude');
            $(selector + ' a').html('<div class="btn btn-back each_column_restore_btn">復元</div>');
        }
    };

    /**
     * 「項目ごと」の除外復元処理
     * user_excludeのクラスがある場合（除外表示になっている場合）はuser_excludeクラスを削除して除外表示を外す
     *
     * @param selector
     */
    var display_exclude_for_each_column = function(selector){
        $.each($(selector + '.user_exclude'), function(){
            $(this).removeClass('user_exclude');
            $(this).find('a').html('<span class="glyphicon glyphicon-remove-circle exclude_button each_column_exclude_button">');
        });

    };

    /**
     * 「伝票ごと」の除外処理
     * display-noneのクラスがある場合（除外表示が非表示になっている場合）はdisplay-noneを外して除外表示にする
     *
     * @param selector
     */
    var display_restore_for_each_order = function(selector){
        $.each($(selector + ' div.overlay_toggle.display-none'), function(){
            $(this).removeClass('display-none');
            $(this).parent().find('a').html('<div class="btn btn-back each_order_restore_btn">復元</div>');
        });
    };

    /**
     * 「伝票ごと」の除外復元処理
     * display-noneのクラスがない場合（除外表示になっている場合）はdisplay-noneを追加して除外表示を非表示にする
     *
     * @param selector
     */
    var display_exclude_for_each_order = function(selector){
        $.each($(selector + ' div.overlay_toggle'), function(){
            if ($(this).find('.display-none').length === 0) {
                $(this).addClass('display-none');
                $(this).parent().find('a').html('<span class="glyphicon glyphicon-remove-circle exclude_button each_order_exclude_button">');
            }
        });
    };

    /**
     * 「項目ごと」除外ボタンクリック
     */
    $('.tab-content').on('click', '.each_column_exclude_button',function () {

        // 除外伝票としてポストするためにhiddenに仕込む
        var receive_order_id = $(this).closest('a').attr('data-receive_order_id');
        var modal = $('#basic-modal');
        var form = modal.find('form');
        form.append('<input name="exclude_orders[]" value="'+receive_order_id+'" type="hidden" id="form_exclude_order_'+receive_order_id+'" class="exclude_orders">');
        display_restore_for_each_column('tr.receive_order_id_'+receive_order_id);
        toggle_execution_button();
    });

    /**
     * 「項目ごと」復元ボタンクリック
     */
    $('.tab-content').on('click', '.each_column_restore_btn',function () {
        // 除外伝票としてポストするためのhiddenから消す
        var receive_order_id = $(this).closest('a').attr('data-receive_order_id');
        $('#form_exclude_order_'+receive_order_id).remove();
        display_exclude_for_each_column('tr.receive_order_id_'+receive_order_id);
        toggle_execution_button();
    });

    /**
     * 「伝票ごと」除外ボタンクリック
     */
    $('.tab-content').on('click', '.each_order_exclude_button',function () {
        // 除外伝票としてポストするためにhiddenに仕込む
        var receive_order_id = $(this).closest('a').attr('data-receive_order_id');
        var modal = $('#basic-modal');
        var form = modal.find('form');
        form.append('<input name="exclude_orders[]" value="'+receive_order_id+'" type="hidden" id="form_exclude_order_'+receive_order_id+'" class="exclude_orders">');
        display_restore_for_each_order('#receive_order_id_'+receive_order_id);
        toggle_execution_button();
    });

    /**
     * 「伝票ごと」復元ボタンクリック
     */
    $('.tab-content').on('click', '.each_order_restore_btn',function () {
        // 除外伝票としてポストするためのhiddenから消す
        var receive_order_id = $(this).closest('a').attr('data-receive_order_id');
        $('#form_exclude_order_'+receive_order_id).remove();
        display_exclude_for_each_order('#receive_order_id_'+receive_order_id);
        toggle_execution_button();
    });

    /**
     * 「項目ごと」タブ切り替え時の挙動
     */
    $('#column-list-tab').on('click', function () {
        // 一旦全要素リセット
        display_exclude_for_each_column('tr.receive_order_id');

        // 除外対象のみにスタイルを適用
        $.each($('.exclude_orders'), function(){
            var receive_order_id = $(this).val();
            display_restore_for_each_column('tr.receive_order_id_'+receive_order_id);
        })
    });

    /**
     * 「伝票ごと」タブ切り替え時の挙動
     */
    $('#order-list-tab').on('click', function () {
        // 一旦全要素リセット
        display_exclude_for_each_order('div.update-result-list');

        // 除外対象のみにスタイルを適用
        $.each($('.exclude_orders'), function(){
            var receive_order_id = $(this).val();
            display_restore_for_each_order('#receive_order_id_'+receive_order_id);
        })
    });

    /**
     * 全件除外の状態だと実行ボタンを押せなくする
     */
    var toggle_execution_button = function(){
        var overlay_toggle_count = $('div.panel.panel-default.update-result-list .overlay_toggle').length;
        var exclude_count = $('input.exclude_orders').length;
        if(overlay_toggle_count === exclude_count){
            $("#preview-execution").attr('disabled', true)
        } else {
            $("#preview-execution").attr('disabled', false)
        }
    };
    // 初回表示用として実行しておく
    toggle_execution_button();

    /**
     * 戻るボタンのクリックイベント
     * 二重送信を防止する
     */
    $('a.preview-back').on('click', function () {
        $.display_block();
    });
});