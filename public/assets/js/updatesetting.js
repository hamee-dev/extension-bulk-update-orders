/**
 * 設定新規作成・編集画面用js
 */
$(function(){

    // 数値型のカラムタイプ
    var COLUMN_TYPE_NUMBER = 'number';

    // タグ情報を取得するためのマスタ名
    var MASTER_NAME_TAG = 'tag'

    // 発送方法を取得するためのマスタ名
    var MASTER_NAME_DELIVERY = 'delivery';

    // 選択できる受注分類タグを表示しているエリアを非表示にするタイマー
    var tag_field_timer = null;

    // 選択できる受注分類タグを表示しているエリアからマウスカーソルが離れて何ミリ秒後に非表示にするか
    var tag_field_timeout = 300;

    // 初期表示時の一括更新設定の内容（FORM）の情報
    // 一括更新設定の内容変更を判定するために用いる
    var original_setting_values = JSON.stringify($('#setting-form').serializeArray());

    /**
     * 更新する項目の選択
     */
    $(document).on('change', '.select_column', function () {
        var column_id = $(this).val();

        if (column_id === '') {
            // 初期表示（項目を選択）に戻す
            var element = $('#setting-list-tr-template .add').clone();
            var parent = $(this).parents('.form-group');
            parent.before(element);
            parent.remove();

            show_delete_button();
            update_select_columns();
            return;
        }

        var column = EXT_BUO.UPDATESETTING.columns[column_id];
        if (!column) {
            $.show_alert('不正な値を選択しました');
            return;
        }

        // テンプレートからcloneして利用する
        var element = $('#setting-list-tr-template .' + column['template_name']).clone();
        element.find('select.select_column').val(column_id);

        if(column['caution_template_name']) {
            // 注意文言テンプレートの取得
            var caution_element = $('#setting-list-data-caution-template .' + column['caution_template_name']).clone();
            // 注意文言の挿入
            $(element).find('.setting-list-data').append(caution_element);
        }

        if ($(element).find('.select-delivery').length === 1) {
            // 発送方法関連の場合
            create_delivery_element(element);
        }else{
            // 更新方法が選べる場合は選択した項目に対応する更新方法を設定する
            create_select_update_element(
                $(element).find('.select-update'),
                column_id,
                column['column_type']['column_types_update_methods']);

            if ($(element).find('.select-master').length === 1) {
                // マスタ型の場合は、対応するマスタデータを設定する
                create_master_element(element, column_id, column['master_name'])
            }else if ($(element).find('.tag-list').length === 1) {
                // タグ型の場合
                create_tag_element(element);
            }else if ($(element).find('.calendar-select').length === 1) {
                // 日付型の場合
                select_date_method(element.find('.date-input'));
            }
        }

        // 最大入力文字数の表示
        var maxlength_input = $(element).find('.maxlength');
        if (maxlength_input.length === 1 && parseInt(column['input_max_length']) > 0) {
            maxlength_input.attr('maxlength', column['input_max_length']);
            maxlength_input.maxlength({
                alwaysShow: true,
                appendToParent: true,
            });
        }

        if (column['column_type_name'] === COLUMN_TYPE_NUMBER) {
            // 数値型の場合はtype=numberにする
            $(element).find('input.text').attr('type', 'number');
            // NOTE: maxとminに小数の値を指定するとテキストボックス右の増減ボタンをクリックした時に少数第2位まで表示されてしまうため整数値とした
            $(element).find('input.text').attr('max', '999999999');
            $(element).find('input.text').attr('min', '-999999999');
        }

        // inputタグのnameにcolumn_idを追加する
        add_column_id($(element).find('input[name=update_value\\[\\]]'), column_id);
        add_column_id($(element).find('textarea[name=update_value\\[\\]]'), column_id);

        // 項目の追加
        var parent = $(this).parents('.form-group');
        parent.before(element);
        parent.remove();

        show_add_button();

        show_delete_button();
        update_select_columns();
    });

    /**
     * 「項目を追加」ボタンの表示・非表示のだしわけ
     *
     */
    function show_add_button() {
        var item_cnt = $('form [name="select_column[]"]').length;

        // 「発送方法」が項目に存在したときに項目数が一つ多くなってしまうので減らす
        if ($('form tr.delivery').length > 0) {
            item_cnt--;
        }

        // 「項目を追加」ボタンの表示
        if ($('#setting-list-table .add').length === 0 && item_cnt < 20) {
            $('#add-button').show();
        }
    }

    /**
     * name属性が「hogehoge[]」となっていた場合「hogehoge[column_id]」と変更する
     *
     * @param element 対象の要素
     * @param string column_id 変更するカラムID
     */
    function add_column_id(element, column_id) {
        if (element) {
            if (element.attr('name')) {
                element.attr('name', element.attr('name').replace('[]', '['+column_id+']'));
            }
            if (element.attr('id')) {
                element.attr('id', element.attr('id').replace('[]', '['+column_id+']'));
            }
        }
    }

    /**
     * 更新方法を更新する
     *
     * @param element 対象の要素
     * @param string column_id 対象のカラムID
     * @param array column_types_update_methods 更新方法の配列
     */
    function create_select_update_element(element, column_id, column_types_update_methods) {
        var select_element = element.find('select');

        // 初期化
        select_element.empty();

        $.each(column_types_update_methods,function(index, value){
            select_element.append($('<option>', {value : value['update_method']['id'], html : value['update_method']['name']}));
        });
        add_column_id(select_element, column_id);

        if (Object.keys(column_types_update_methods).length === 1) {
            element.hide();
        }
    }

    /**
     * マスタ型の要素を作成する
     *
     * @param parent_element 対象の要素
     * @param string column_id 対象のカラムID
     * @param string master_name マスタ名
     */
    function create_master_element(parent_element, column_id, master_name) {
        var select_master = $(parent_element).find('.select-master select');

        // 初期化
        select_master.empty();

        // マスタのoption要素を追加
        var master = get_master(master_name);
        $.each(master,function(index, value){
            select_master.append('<option value="' + value['id'] + '">' + value['name'] + '</option>');
        });
        add_column_id(select_master, column_id);
    }

    /**
     * タグ型の要素を作成する
     *
     * @param parent_element 対象の要素
     */
    function create_tag_element(parent_element) {
        var tag_list = $(parent_element).find('.tag-list');

        // タグ選択フィールドの初期化
        tag_list.empty();

        // タグ選択フィールドを作成
        var master = get_master(MASTER_NAME_TAG);
        $.each(master,function(index, value){
            var style ='background-color: ' + value['params']['grouping_tag_color'] + '; ';
            style += 'color: ' + value['params']['grouping_tag_str_color'] + '; ';
            style += 'border: 1px solid ' + value['params']['grouping_tag_color'] + '; '
            tag_list.append($('<a>', {
                href : 'javascript:void(0);',
                class : 'btn tag_icon',
                'data-tag-value' : value['name'],
                'style' : style,
                html : value['name'],
            }));
        });
    }

    /**
     * 発送関連情報の要素を作成する
     *
     * @param parent_element 対象の要素
     */
    function create_delivery_element(parent_element) {
        // 初期化
        $(parent_element).find('.forwarding-agent-list').empty();

        // 発送方法
        var select_delivery = $(parent_element).find('.select-delivery select');
        var delivery_master = get_master(MASTER_NAME_DELIVERY);
        select_delivery.append('<option>' + $('#no-select-value').val() + '</option>');
        $.each(delivery_master,function(index, value){
            select_delivery.append('<option value="' + value['id'] + '">' + value['name'] + '</option>');
        });

        // 発送方法別区分タイプをいったん空で作成する
        var forwarding_agent_list = $(parent_element).find('.forwarding-agent-list');
        $.each(EXT_BUO.UPDATESETTING.forwarding_agent_types,function(type, data){
            var forwarding_agent_element = $('#setting-list-forwarding-agent-template .forwarding-agent').clone();
            forwarding_agent_element.find('label').html(data['name']);
            forwarding_agent_element.find('input[name=select_column\\[\\]]').val(data['id']);

            var forwarding_agent_element_master = forwarding_agent_element.find('select[name=select_master\\[\\]]');
            add_column_id(forwarding_agent_element_master, data['id']);
            add_column_id(forwarding_agent_element.find('input[name=select_master\\[\\]]'), data['id']);
            forwarding_agent_element_master.attr('data-type', type);
            forwarding_agent_element_master.attr('data-column-id', data['id']);

            var forwarding_agent_select_update = forwarding_agent_element.find('.select-update select');
            add_column_id(forwarding_agent_select_update, data['id']);
            add_column_id(forwarding_agent_element.find('input[name=select_update\\[\\]]'), data['id']);
            forwarding_agent_select_update.hide();

            forwarding_agent_list.append(forwarding_agent_element);
        });
    }

    /**
     * マスタデータを取得する
     * キャッシュにあればキャッシュから取得し、なければAPIで取得する
     *
     * @param string master_name
     * @return array
     */
    function get_master(master_name) {
        var master = EXT_BUO.UPDATESETTING.master_list[master_name];
        // キャッシュにマスタデータがない場合はAPIから取得する
        if (!master) {
            // NOTE: マスタを取得しないと選択できないため、あえて同期通信にしているが、一瞬固まってしまうので非同期のほうがいいかもしれない
            var csrf_token_name = $('input[name=token_name]').val();
            var csrf_token_value = $('input[name=' + csrf_token_name + ']').val();
            var ajax_data = {'name' : master_name};
            ajax_data[csrf_token_name] = csrf_token_value;

            $.execute_ajax({
                url: '/master/data.json',
                type: 'GET',
                data: ajax_data,
                async: false
            }).done(function (data) {
                master = data[master_name];
                EXT_BUO.UPDATESETTING.master_list[master_name] = master;
            }).fail(function(data){
                // エラー処理
                $('#message').empty();
                $('#message').append('<div class="alert alert-danger" role="alert"><div>エラーが発生しました。お手数ですが操作をはじめからやり直してください。</div></div>');
            });
        }
        return master;
    }

    /**
     * 受注分類タグのテキストエリアをクリックした場合、選択できる受注分類タグ一覧を表示させる
     */
    $(document).on('click', '.tag-text-area', function () {
        clearTimeout(tag_field_timer);
        $('.tag-list').show();
        $(this).off('mouseleave');

        $(this).mouseleave(function () {
            tag_field_timer = setTimeout(function(){
                $('.tag-list').hide();
            },tag_field_timeout);
        });
    });

    /**
     * 受注分類タグ一覧、テキストエリア以外にマウスオーバーした場合、受注分類タグ一覧を非表示にする
     */
    $(document).on('mouseenter', '.tag-list', function () {
        clearTimeout(tag_field_timer);
        $(this).off('mouseleave');

        $(this).mouseleave(function () {
            tag_field_timer = setTimeout(function(){
                $('.tag-list').hide();
            },tag_field_timeout);
        });
    });


    /**
     * タグの追加
     */
    $(document).on('click', '.tag_icon', function () {
        var tag_value = '[' + $(this).data('tag-value') + ']';
        var text = $('#setting-list-table .tag-text-area').val();
        if (text.indexOf(tag_value) === -1) {
            $('.tag textarea').val(text + tag_value);
        }
    });

    /**
     * 日付入力用のカレンダー表示
     */
    $(document).on('click', '.calendar-select', function () {
        $(this).datepicker({
            format: "yyyy/mm/dd",
            language: "ja"
        });
        $(this).datepicker("show");
        if ($(this).val() === '') {
            // 未入力であれば今日を選択する
            $(this).datepicker('setDate', 'today');
        }
    });

    /**
     * 項目の追加
     */
    $('#add-button a').on('click', function () {
        $('#setting-list-table').append($('#setting-list-tr-template .add').clone());
        $('#add-button').hide();

        show_delete_button();
        update_select_columns();
    });

    /**
     * 項目の削除
     */
    $(document).on('click', '.setting-list-button a', function () {
        var parent = $(this).parents('.form-group');
        parent.remove();

        show_add_button();
        show_delete_button();
        update_select_columns();
    });

    /**
     * 設定保存ボタン
     */
    $('#setting-save').on('click', function () {
        if (get_select_column_values().length > 0) {
            $('#setting-form').attr('action', '/updatesetting/save');
            $('#setting-form').submit();
        }else{
            $.show_alert('更新する項目を設定してください');
        }
    });

    /**
     * 別名で保存ボタン
     */
    $('#setting-save-new').on('click', function () {
        if (get_select_column_values().length > 0) {
            $.show_text_box_modal(
                {
                    title: '設定を保存',
                    description: '一括更新設定を新規で保存します',
                    label: '設定名',
                    text_maxlength: EXT_BUO.UPDATESETTING.setting_name_max_length,
                    done_button_name: '保存',
                    callback: save_new,
                    default_value: $('input[name=name]').val()
                }
            );
        }else{
            $.show_alert('更新する項目を設定してください');
        }
    });

    /**
     * 実行ボタンクリック
     */
    $('#setting-execution').on('click', function () {
        if (get_select_column_values().length > 0) {
            // 実行
            exec();
        }else{
            $.show_alert('更新する項目を設定してください');
        }
    });

    /**
     * 一括更新設定内容の保存状態を判断し、
     * 未保存の場合は確認モーダルを表示を実行、
     * 保存済みの場合は一括更新設定内容を送信する
     */
    function exec() {
        // 実行
        if(is_unsaved()) {
            // 設定内容が未保存の場合

            // 未保存時の確認
            confirm_unsaved();
        } else {
            // 設定内容が保存済みの場合

            // 一括更新設定を送信
            submit_update_setting();
        }
    }

    /**
     * 設定内容が未保存であるかの判定
     *
     * @return {boolean} true:未保存、false:保存済み
     */
    function is_unsaved() {
        // 登録されている設定内容とプレビュー時の一時的な設定内容との差分あり判定
        // 入力フォーム変更の変更あり判定
        return is_different_original() || is_changed_form();
    }

    /**
     * 「登録されている一括更新設定の内容」と「プレビュー時の一時的な一括更新設定の内容」が異なるかを判定
     *
     * @return {boolean} true:異なる、false:同じ
     */
    function is_different_original() {
        return EXT_BUO.UPDATESETTING.is_different_original;
    }

    /**
     * 一括更新設定の内容（FORM）が変更されているかを判定
     *
     * @return {boolean} true:変更あり、false:変更なし
     */
    function is_changed_form() {
        var current_setting_values = JSON.stringify($('#setting-form').serializeArray());
        return original_setting_values !== current_setting_values;
    }

    /**
     * 設定内容が未保存時の確認を行う
     *
     */
    function confirm_unsaved() {
        $.show_modal(
            {
                title: '実行',
                description: '<p>設定内容が保存されておりません。保存せずに実行しますか？</p>',
                done_button_name: '保存せずに実行',
                done_button_class: 'btn-success',
                callback: submit_update_setting
            }
        );
    }

    /**
     * 一括更新設定を送信する
     *
     * @return {boolean} false
     */
    function submit_update_setting() {
        $('#setting-form').attr('action', '/updatesetting/execution');
        $('#setting-form').submit();

        // モーダルダイアログからのボタンクリック時にコールされた場合に、
        // ボタンクリックイベントを親要素へ伝えないために戻り値をfalseとする
        return false;
    }

    /**
     * 新規保存を行う
     *
     * @return {boolean}
     */
    function save_new() {
        var name = $('#text-box-modal .text-box').val();
        if (name.length === 0) {
            $('#text-box-modal .modal-body .alert').html('設定名を入力してください');
            $('#text-box-modal .modal-body .alert').show();
        }else{
            $('input[name=name]').val(name);
            $('#setting-form').attr('action', '/updatesetting/save');
            $('#setting-form').append($('<input>', {type : 'hidden', name : 'create', value : '1', class : 'is-create'}));
            $('#setting-form').submit();
        }
        return false;
    }


    /**
     * 削除ボタンの出しわけ処理
     * 項目が２つ以上になった場合は削除ボタンを表示し、１つの場合は非表示にする
     */
    function show_delete_button() {
        if ($('#setting-list-table .setting-data').length <= 1) {
            $('.setting-list-button a').css('visibility', 'hidden');
        }else{
            $('.setting-list-button a').css('visibility', 'visible');
        }
    }

    /**
     * 更新する項目のセレクトボックスの項目更新
     * すでに選択済みの項目は選べないようにする
     */
    function update_select_columns() {
        // 選択している全ての更新する項目の値を取得する
        var select_column_values = get_select_column_values();

        // 発送関連の項目を選択しているか
        var is_delivery_select = false;
        $.each(select_column_values,function(index, select_column_value){
            if ($.inArray(select_column_value, EXT_BUO.UPDATESETTING.delivery_column_ids) !== -1) {
                is_delivery_select = true;
                return false;
            }
        });

        // 支払関連の項目を選択しているか
        var is_payment_select = false;
        $.each(select_column_values,function(index, select_column_value){
            if ($.inArray(select_column_value, EXT_BUO.UPDATESETTING.payment_column_ids) !== -1) {
                is_payment_select = true;
                return false;
            }
        });

        // 支払方法の項目を選択しているか
        var is_payment_method_select = false;
        $.each(select_column_values,function(index, select_column_value){
            if (select_column_value === EXT_BUO.UPDATESETTING.payment_method_column_id) {
                is_payment_method_select = true;
                return false;
            }
        });

        // 受注金額関連の項目を選択しているか
        var is_order_amount_select = false;
        $.each(select_column_values,function(index, select_column_value){
            if ($.inArray(select_column_value, EXT_BUO.UPDATESETTING.order_amount_column_ids) !== -1) {
                is_order_amount_select = true;
                return false;
            }
        });

        // 総合計の項目を選択しているか
        var is_total_amount_select = false;
        $.each(select_column_values,function(index, select_column_value){
            if (select_column_value === EXT_BUO.UPDATESETTING.total_amount_column_id) {
                is_total_amount_select = true;
                return false;
            }
        });


        // 選択済みの項目は選べないようにする
        $.each($('#setting-list-table select.select_column'), function(select_column_index, select_column){

            // この行で選択している項目値
            var select_column_value = $(select_column).val();
            // 更新する項目を初期化する
            var new_select_column = $('#setting-list-tr-template .add .select_column').clone();
            $(this).html(new_select_column.children());
            $(this).val(select_column_value);

            // 発送関連の項目を選択している場合は「発送関連」を選択させる
            if (is_delivery_select && $.inArray(select_column_value, EXT_BUO.UPDATESETTING.delivery_column_ids) !== -1) {
                $(this).val('delivery');
            }

            // option要素を１つずつ見ていって、表示もしくは非表示にする
            $.each($(this).children(),function(option_index, option){
                var value = $(option).val();

                // optionのvalueがこの行で選択している値ではない、かつvalueは他の行で選択されている(他の行で選択されていても、この行で選択している値であれば表示させる)
                if ((select_column_value !== value && $.inArray(value, select_column_values) !== -1) ||
                    // 発送関連の項目を選択していて、かつvalueが発送関連の値だった場合は非表示にする
                    (is_delivery_select && $.inArray(value, EXT_BUO.UPDATESETTING.delivery_column_ids) !== -1) ||
                    // 発送関連の項目を選択していない場合「発送関連項目」は非表示にする
                    (!is_delivery_select && value === 'delivery') ||
                    // 支払関連の項目が選択されている かつ optionのvalueが支払方法 かつ optionのvalueがこの行で選択している値ではない場合に支払方法は非表示にする
                    (is_payment_select && value === EXT_BUO.UPDATESETTING.payment_method_column_id && select_column_value !== value) ||
                    // 支払方法を選択していて、かつ valueが支払関連の項目 かつ  optionのvalueがこの行で選択している値ではない場合に支払関連項目は非表示にする
                    (is_payment_method_select && $.inArray(value, EXT_BUO.UPDATESETTING.payment_column_ids) !== -1 && select_column_value !== value) ||
                    // 受注金額関連の項目が選択されている かつ optionのvalueが総合計 かつ optionのvalueがこの行で選択している値ではない場合に総合計は非表示にする
                    (is_order_amount_select && value === EXT_BUO.UPDATESETTING.total_amount_column_id && select_column_value !== value) ||
                    // 総合計を選択していて、かつ valueが受注金額関連の項目 かつ  optionのvalueがこの行で選択している値ではない場合に受注金額関連項目は非表示にする
                    (is_total_amount_select && $.inArray(value, EXT_BUO.UPDATESETTING.order_amount_column_ids) !== -1 && select_column_value !== value)
                ) {
                    $(option).remove();
                }
            });
        });
    }

    /**
     * 選択しているを全ての更新する項目の値を取得する
     *
     * @returns {Array}
     */
    function get_select_column_values() {
        var select_column_values = [];
        $.each($('#setting-list-table .select_column'), function(select_column_index, select_column){
            var value = $(select_column).val();
            if (value !== '') {
                select_column_values.push(value);
            }
        });
        return select_column_values;
    }

    /**
     * 発送方法の選択
     */
    $(document).on('change', '.select-delivery select', function () {
        var delivery_id = $(this).val();
        if (delivery_id === $('#no-select-value').val()) {
            // 「項目を選択」を選んだ場合は初期表示に戻す
            create_delivery_element($(this).parents('.delivery'));
            return;
        }
        var forwarding_agent_list = $(this).parents('.delivery').find('.forwarding-agent-list .forwarding-agent');
        var master = get_master('forwarding_agent');

        // 発送方法別区分タイプの要素を作成する
        $.each(forwarding_agent_list, function(index, forwarding_agent){
            var select_forwarding_agent = $(forwarding_agent).find('.select-forwarding-agent');
            var type = select_forwarding_agent.data('type');
            var column_id = select_forwarding_agent.data('column-id');
            var count = 0;
            select_forwarding_agent.empty();
            // 選択肢の作成
            $.each(master,function(index, value){
                if (value['params']['forwarding_agent_id'] === delivery_id && value['params']['forwarding_agent_type'] === type) {
                    if (count === 0) {
                        select_forwarding_agent.append($('<option>', {html : ''}));
                    }
                    select_forwarding_agent.append($('<option>', {value : value['id'], html : value['name']}));
                    count++;
                }
            });
            // 選択できない項目(count=0)の場合はdisabledにする
            if (count > 0) {
                select_forwarding_agent.prop("disabled", false);
            }else{
                select_forwarding_agent.prop("disabled", true);
            }

            add_column_id($(forwarding_agent).find('input[name=select_master\\[\\]]'), column_id);

            // 更新方法の設定（selectになっているが「上書き」しかありえないはず）
            var column = EXT_BUO.UPDATESETTING.columns[column_id];
            var forwarding_agent_select_update = $(forwarding_agent).find('.select-update select');
            forwarding_agent_select_update.empty();
            $.each(column['column_type']['column_types_update_methods'],function(index, value){
                forwarding_agent_select_update.append('<option value="' + value['update_method']['id'] + '">' + value['update_method']['name'] + '</option>');
            });
            forwarding_agent_select_update.attr('name', forwarding_agent_select_update.attr('name').replace('[]', '[' + column_id + ']'));

            if (Object.keys(column['column_type']['column_types_update_methods']).length === 1) {
                forwarding_agent_select_update.hide();
            }
        });
    });

    /**
     * 発送方法関連項目のシールに同じ値を選択できないように制御する
     */
    $(document).on('change', 'select.select-forwarding-agent', function () {
        var type = $(this).data('type');
        // data-typeがシールかどうか
        if (type && type.match(/seal\d_kbn/) !== null) {
            // シールで選択している値一覧を取得する
            var seal_values = [];
            $.each($('select.select-forwarding-agent'), function(index, forwarding_agent_seal){
                var type = $(forwarding_agent_seal).data('type')
                var value = $(forwarding_agent_seal).val();
                if (type && value !== '' && type.match(/seal\d_kbn/) !== null) {
                    seal_values.push(value);
                }
            });
            // 重複チェック
            var duplication = seal_values.filter(function (x, i, self) {
                return self.indexOf(x) !== self.lastIndexOf(x);
            });
            if (duplication.length > 0) {
                $.show_alert('シールは同じ値を選択できません');
                $(this).val('');
            }
        }
    });

    /**
     * 日付の入力方法のセレクトボックスの変更イベント
     */
    $(document).on('change', 'select.date-select', function () {
        select_date_method($(this).parents('.setting-list-data').find('.date-input'), $(this).val());
    })

    /**
     * 日付の項目の表示を更新する
     * 「日付を入力」を選択した場合、テキストボックスを表示し
     * 「今日」「明日」「明後日」を選択した場合は、テキストボックスを非表示にする
     *
     * @param element 日付の項目の更新内容の要素
     * @param select_value 日付の入力方法のセレクトボックスの値
     */
    function select_date_method(element, select_value) {

        if (!select_value) {
            // select_valueが無い場合は「日付を入力」にする
            select_value = EXT_BUO.UPDATESETTING.date_select_type_input;
        }

        if (select_value === EXT_BUO.UPDATESETTING.date_select_type_input) {
            // 「日付を入力」の場合
            element.show();
            element.addClass('.calendar-select');
            element.val('');
        }else if (select_value in EXT_BUO.UPDATESETTING.date_select_type_relative_list) {
            // 「今日」「明日」「明後日」の場合
            element.hide();
            element.removeClass('.calendar-select');
            element.val(select_value);
        }
    }
});