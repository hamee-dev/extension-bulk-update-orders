/**
 * 全ての画面で呼ばれる共通のjs
 */
$(function(){
    init();

    function init() {
        var execution_method_name = $('#add-params-1').attr('name');
        var execution_method_value = $('#add-params-1').val();
        var extension_execution_id_name = null;
        var extension_execution_id_value = null;
        if ( $('#add-params-2').val() !== '') {
            extension_execution_id_name = $('#add-params-2').attr('name');
            extension_execution_id_value = $('#add-params-2').val();
        }
        var csrf_token_name = $('input[name=token_name]').val();
        var csrf_token_value = $('input[name=' + csrf_token_name + ']').val();

        // aタグのhrefにexecution_methodとextension_execution_idとcsrfトークンを追加する
        var add_params = execution_method_name + '=' + execution_method_value + '&' + csrf_token_name + '=' + csrf_token_value;
        if (extension_execution_id_name !== null) {
            add_params += '&' + extension_execution_id_name + '=' + extension_execution_id_value;
        }
        $('a.add-params').each(function(index, element){
            var href = $(element).attr('href');
            if (href.indexOf('?') != -1) {
                href += '&';
            }else{
                href += '?';
            }
            href += add_params;
            $(element).attr('href', href);
        });

        // formタグの中にcsrfトークン、execution_method、extension_execution_idを追加する
        $('form.add-params').each(function(index, element){
            $(this).append(
                $('<input>', {type: 'hidden', name: csrf_token_name, value: csrf_token_value}));
            $(this).append(
                $('<input>', {type: 'hidden', name: execution_method_name, value: execution_method_value}));
            if (extension_execution_id_name !== null) {
                $(this).append(
                    $('<input>', {type: 'hidden', name: extension_execution_id_name, value: extension_execution_id_value}));
            }
        });

        // ツールチップを有効にする
        $('[data-toggle="tooltip"]').tooltip()

        // 最大文字数制限を有効にする
        $('[data-maxlength="true"]').maxlength({
            alwaysShow: true,
            appendToParent: true,
        });
    }

    /**
     * アラート（メッセージのみのモーダル）を表示する
     *
     * @param string alert_message
     */
    $.show_alert = function (alert_message) {
        var modal = $('#alert-modal');
        modal.find('.modal-body-description').html(alert_message);
        modal.modal();
    }

    /**
     * タイトルとメッセージのみの簡単なモーダルを表示する
     *
     * @param array data
     *  {
     *      title: モーダルタイトル
     *      action_url: formのアクションのurl
     *      description: モーダルの説明
     *      done_button_name: 確定ボタン名
     *      done_button_class: 確定ボタンに追加するクラス、色を指定したい場合などに使う、何も指定しない場合「btn-success」(緑色)
     *      callback: 確定ボタンクリックでコールバックしたい場合設定する
     *      add_hidden_data: formにhiddenのinputタグを追加したい場合、nameとvalueの連想配列で渡す
     *  }
     */
    $.show_modal = function (data) {
        var modal = $('#basic-modal');

        if ('title' in data) {
            modal.find('.modal-title').html(data['title']);
        }

        if ('action_url' in data) {
            modal.find('form').attr('action', data['action_url']);
        }

        if ('description' in data) {
            modal.find('.modal-body-description').html(data['description']);
        }

        if ('done_button_name' in data) {
            var done_button = modal.find('.done_button');
            done_button.html(data['done_button_name']);
            done_button.off('click');

            if ('done_button_class' in data) {
                done_button.addClass(data['done_button_class']);
            }else{
                done_button.addClass('btn-success');
            }

            if ('callback' in data) {
                modal.find('.done_button').on('click', data['callback']);
            }
        }

        if ('add_hidden_data' in data) {
            var form = modal.find('form');
            form.find('.add_hidden_data').remove();
            $.each(data['add_hidden_data'],function(name, value){
                form.append($('<input>', {type : 'hidden', name : name, value : value, class : 'add_hidden_data'}));
            });
        }

        modal.modal();
    }

    /**
     * テキストボックスつきモーダルを表示する
     *
     * @param array data
     *  {
     *      title: モーダルタイトル
     *      action_url: formのアクションのurl
     *      description: モーダルの説明
     *      label: テキストボックスの説明
     *      default_value: テキストボックスの初期値
     *      text_maxlength: テキストボックスの最大入力文字数
     *      done_button_name: 確定ボタン名
     *      callback: 確定ボタンクリックでコールバックしたい場合設定する
     *      add_hidden_data: formにhiddenのinputタグを追加したい場合、nameとvalueの連想配列で渡す
     *  }
     */
    $.show_text_box_modal = function (data) {
        var modal = $('#text-box-modal');

        if ('title' in data) {
            modal.find('.modal-title').html(data['title']);
        }

        if ('action_url' in data) {
            modal.find('form').attr('action', data['action_url']);
        }

        if ('description' in data) {
            modal.find('.modal-body-description').html(data['description']);
        }

        if ('label' in data) {
            modal.find('.modal-body-label').html(data['label']);
        }

        if ('default_value' in data) {
            modal.find('.text-box').val(data['default_value']);
        }

        if ('text_maxlength' in data) {
            var text_box = modal.find('.text-box');
            text_box.attr('maxlength', data['text_maxlength']);
            text_box.maxlength({
                alwaysShow: true,
                appendToParent: true,
            });
        }

        if ('done_button_name' in data) {
            var done_button = modal.find('.done_button');
            done_button.html(data['done_button_name']);
            done_button.off('click');

            if ('callback' in data) {
                modal.find('.done_button').on('click', data['callback']);
            }
        }

        if ('add_hidden_data' in data) {
            var form = modal.find('form');
            form.find('.add_hidden_data').remove();
            $.each(data['add_hidden_data'],function(name, value){
                form.append($('<input>', {type : 'hidden', name : name, value : value, class : 'add_hidden_data'}));
            });
        }

        modal.find('.modal-body .alert').hide();

        modal.modal();
    }

    /**
     * ajaxリクエストを行う
     *
     * @param options
     * @returns {*}
     */
    $.execute_ajax = function (options) {

        var execution_method_name = $('#add-params-1').attr('name');
        var execution_method_value = $('#add-params-1').val();
        options['data'][execution_method_name] = execution_method_value;

        if ($('#add-params-2').val() !== '') {
            var extension_execution_id_name = $('#add-params-2').attr('name');
            var extension_execution_id_value = $('#add-params-2').val();
            options['data'][extension_execution_id_name] = extension_execution_id_value;
        }

        var defer = $.Deferred();
        options['success'] = defer.resolve;
        options['error'] = defer.reject;
        options['cache'] = false;

        $.ajax(options);

        return defer.promise();
    }

    /**
     * 画面全体を覆って他のボタンをクリックさせないようにするためのビューを表示する
     */
    $.display_block = function () {
        $('#display-block-view').show();
    }

    /**
     * フォームのサブミットイベント
     * 二重送信を防止する
     */
    $('form').on('submit', function(){
        $.display_block();
    });

    /**
     * テキストボックスでリターンキーを押すとsubmitされてしまうのを防ぐ
     */
    $(document).on('keypress', 'input', function (e) {
        // リターンキーはkeyCode=13
        if(e.keyCode === 13){
            e.preventDefault();

            // 現在フォーカスしている入力欄からフォーカスを外す
            $('input').blur();
        }
    });
});
