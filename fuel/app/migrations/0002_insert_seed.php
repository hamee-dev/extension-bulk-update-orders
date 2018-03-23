<?php
namespace Fuel\Migrations;

class Insert_seed
{

    public function up()
    {
        $column_type_sql = "INSERT INTO `column_types` (`id`, `name`)
VALUES
	(1,'テキスト型'),
	(2,'テキストエリア型'),
	(3,'Eメール型'),
	(4,'数値型'),
	(5,'日付型'),
	(6,'ブール型'),
	(7,'マスタ選択型'),
	(8,'タグ型'),
	(9,'電話番号型'),
	(10,'郵便番号型');
        ";
        \DB::query($column_type_sql)->execute();

        $receive_order_section_sql = "INSERT INTO `receive_order_sections` (`id`, `name`)
VALUES
	(1,'受注伝票'),
	(2,'受注伝票オプション');
        ";
        \DB::query($receive_order_section_sql)->execute();

        $pdate_method_sql = "INSERT INTO `update_methods` (`id`, `name`)
VALUES
	(1,'次の値で上書き'),
	(2,'次の値を追記'),
	(3,'次の値を加算[+]'),
	(4,'次の値で減算[-]'),
	(5,'次の値を乗算[x]'),
	(6,'次の値で除算[÷]');
	    ";
        \DB::query($pdate_method_sql)->execute();

        $column_types_update_method_sql = "INSERT INTO `column_types_update_methods` (`id`, `column_type_id`, `update_method_id`)
VALUES
	(1,1,1),
	(2,1,2),
	(3,2,1),
	(4,2,2),
	(5,3,1),
	(6,3,2),
	(7,4,1),
	(8,4,3),
	(9,4,4),
	(10,4,5),
	(11,4,6),
	(12,5,1),
	(13,6,1),
	(14,7,1),
	(15,8,1),
	(16,8,2),
	(17,9,1),
	(18,9,2),
	(19,10,1),
	(20,10,2);
        ";
        \DB::query($column_types_update_method_sql)->execute();

        $receive_order_column_sql = "INSERT INTO `receive_order_columns` (`id`, `receive_order_section_id`, `column_type_id`, `physical_name`, `logical_name`, `input_min_length`, `input_max_length`, `master_name`, `order_amount`, `payment`, `delivery`, `display_order`, `disabled`)
VALUES
	(1,1,7,'receive_order_shop_id','店舗',0,0,'shop',0,0,0,10,0),
	(2,1,1,'receive_order_shop_cut_form_id','受注番号',1,50,NULL,0,0,0,20,0),
	(3,1,5,'receive_order_date','受注日',0,0,NULL,0,0,0,30,0),
	(4,1,7,'receive_order_confirm_ids','確認内容',0,0,'confirm',0,0,0,40,1),
	(5,1,6,'receive_order_confirm_check_id','確認チェック',0,0,NULL,0,0,0,50,0),
	(6,1,8,'receive_order_gruoping_tag','受注分類タグ',0,65535,'tag',0,0,0,60,0),
	(7,1,7,'receive_order_cancel_type_id','受注キャンセル',0,0,'cancel',0,0,0,70,0),
	(8,1,7,'receive_order_delivery_id','発送方法',0,0,'delivery',0,0,1,80,0),
	(9,1,7,'receive_order_payment_method_id','支払方法',0,0,'payment',0,1,0,90,0),
	(10,1,4,'receive_order_goods_amount','商品計',0,13,NULL,1,0,0,100,0),
	(11,1,4,'receive_order_tax_amount','税金',0,13,NULL,1,0,0,110,0),
	(12,1,4,'receive_order_charge_amount','手数料',0,13,NULL,1,0,0,120,0),
	(13,1,4,'receive_order_delivery_fee_amount','発送代',0,13,NULL,1,0,0,130,0),
	(14,1,4,'receive_order_other_amount','他費用',0,13,NULL,1,0,0,140,0),
	(15,1,4,'receive_order_point_amount','ポイント数',0,13,NULL,1,0,0,150,0),
	(16,1,4,'receive_order_total_amount','総合計',0,13,NULL,1,0,0,160,0),
	(17,1,4,'receive_order_deposit_amount','入金額',0,13,NULL,0,0,0,170,0),
	(18,1,7,'receive_order_deposit_type_id','入金状況',0,0,'deposit',0,1,0,180,0),
	(19,1,5,'receive_order_deposit_date','入金日',0,0,NULL,0,0,0,190,0),
	(20,1,2,'receive_order_note','備考',0,1000,NULL,0,0,0,200,0),
	(21,1,5,'receive_order_statement_delivery_instruct_printing_date','納品書印刷指示日',0,0,NULL,0,0,0,210,0),
	(22,1,2,'receive_order_statement_delivery_text','納品書特記事項',0,1000,NULL,0,0,0,220,0),
	(23,1,2,'receive_order_worker_text','作業用欄',0,1000,NULL,0,0,0,230,0),
	(24,1,2,'receive_order_picking_instruct','ピッキング指示',0,1000,NULL,0,0,0,240,0),
	(25,1,5,'receive_order_hope_delivery_date','配達希望日',0,0,NULL,0,0,0,250,0),
	(26,1,7,'receive_order_hope_delivery_time_slot_id','時間指定',0,0,'forwarding_agent_jikantaisitei',0,0,1,260,0),
	(27,1,7,'receive_order_delivery_method_id','便種',0,0,'forwarding_agent_binsyu',0,0,1,270,0),
	(28,1,7,'receive_order_business_office_stop_id','営業所止',0,0,'forwarding_agent_eigyosyo_dome',0,0,1,280,0),
	(29,1,7,'receive_order_invoice_id','送り状',0,0,'forwarding_agent_okurijyo',0,0,1,290,0),
	(30,1,7,'receive_order_temperature_id','温度',0,0,'forwarding_agent_ondo',0,0,1,300,0),
	(31,1,7,'receive_order_seal1_id','シール1',0,0,'forwarding_agent_seal1',0,0,1,310,0),
	(32,1,7,'receive_order_seal2_id','シール2',0,0,'forwarding_agent_seal2',0,0,1,320,0),
	(33,1,7,'receive_order_seal3_id','シール3',0,0,'forwarding_agent_seal3',0,0,1,330,0),
	(34,1,7,'receive_order_seal4_id','シール4',0,0,'forwarding_agent_seal4',0,0,1,340,0),
	(35,1,7,'receive_order_gift_flag','ギフト',0,0,'gift',0,0,0,350,0),
	(36,1,1,'receive_order_delivery_cut_form_id','発送伝票番号',0,50,NULL,0,0,0,360,0),
	(37,1,1,'receive_order_delivery_cut_form_note','発送伝票備考欄',0,16,NULL,0,0,0,370,0),
	(38,1,7,'receive_order_credit_type_id','クレジット種類',0,0,'credit',0,0,0,380,1),
	(39,1,1,'receive_order_credit_approval_no','承認番号',0,20,NULL,0,0,0,390,1),
	(40,1,4,'receive_order_credit_approval_amount','承認額',0,13,NULL,0,0,0,400,0),
	(41,1,7,'receive_order_credit_approval_type_id','承認状況',0,0,'credit_approval',0,1,0,410,0),
	(42,1,1,'receive_order_credit_approval_type_name','クレジット名義人',0,20,NULL,0,0,0,420,1),
	(43,1,5,'receive_order_credit_approval_date','承認日',0,0,NULL,0,0,0,430,0),
	(44,1,7,'receive_order_customer_type_id','顧客区分',0,0,'customer',0,0,0,440,0),
	(45,1,1,'receive_order_customer_id','顧客cd',0,4,NULL,0,0,0,450,0),
	(46,1,1,'receive_order_purchaser_name','購入者名',1,100,NULL,0,0,0,460,0),
	(47,1,1,'receive_order_purchaser_kana','購入者フリガナ',0,100,NULL,0,0,0,470,0),
	(48,1,10,'receive_order_purchaser_zip_code','購入者郵便番号',0,20,NULL,0,0,0,480,0),
	(49,1,1,'receive_order_purchaser_address1','購入者住所1',1,255,NULL,0,0,0,490,0),
	(50,1,1,'receive_order_purchaser_address2','購入者住所2',0,255,NULL,0,0,0,500,0),
	(51,1,9,'receive_order_purchaser_tel','購入者電話番号',0,20,NULL,0,0,0,510,0),
	(52,1,3,'receive_order_purchaser_mail_address','購入者メールアドレス',0,100,NULL,0,0,0,520,0),
	(53,1,1,'receive_order_consignee_name','送り先名',1,100,NULL,0,0,0,530,0),
	(54,1,1,'receive_order_consignee_kana','送り先フリガナ',0,100,NULL,0,0,0,540,0),
	(55,1,10,'receive_order_consignee_zip_code','送り先郵便番号',1,20,NULL,0,0,0,550,0),
	(56,1,1,'receive_order_consignee_address1','送り先住所1',1,255,NULL,0,0,0,560,0),
	(57,1,1,'receive_order_consignee_address2','送り先住所2',0,255,NULL,0,0,0,570,0),
	(58,1,9,'receive_order_consignee_tel','送り先電話番号',0,20,NULL,0,0,0,580,0),
	(59,1,6,'receive_order_important_check_id','重要チェック',0,0,NULL,0,0,0,590,0),
	(60,1,5,'receive_order_statement_delivery_printing_date','納品書発行日',0,0,NULL,0,0,0,600,0),
	(61,1,1,'receive_order_credit_number_payments','クレジット支払い回数',0,20,NULL,0,0,0,610,1),
	(62,1,5,'receive_order_send_plan_date','出荷予定日',0,0,NULL,0,0,0,620,0),
	(63,2,2,'receive_order_option_single_word_memo','一言メモ',0,1000,NULL,0,0,0,1000,0),
	(64,2,2,'receive_order_option_message','メッセージ',0,1000,NULL,0,0,0,1010,0),
	(65,2,2,'receive_order_option_noshi','のし',0,1000,NULL,0,0,0,1020,0),
	(66,2,2,'receive_order_option_rapping','ラッピング',0,1000,NULL,0,0,0,1030,0),
	(67,2,2,'receive_order_option_1','オプション1',0,255,NULL,0,0,0,1040,0),
	(68,2,2,'receive_order_option_2','オプション2',0,255,NULL,0,0,0,1050,0),
	(69,2,2,'receive_order_option_3','オプション3',0,255,NULL,0,0,0,1060,0),
	(70,2,2,'receive_order_option_4','オプション4',0,255,NULL,0,0,0,1070,0),
	(71,2,2,'receive_order_option_5','オプション5',0,255,NULL,0,0,0,1080,0),
	(72,2,2,'receive_order_option_6','オプション6',0,255,NULL,0,0,0,1090,0),
	(73,2,2,'receive_order_option_7','オプション7',0,255,NULL,0,0,0,1100,0),
	(74,2,2,'receive_order_option_8','オプション8',0,255,NULL,0,0,0,1110,0),
	(75,2,2,'receive_order_option_9','オプション9',0,255,NULL,0,0,0,1120,0),
	(76,2,2,'receive_order_option_10','オプション10',0,255,NULL,0,0,0,1130,0);
        ";
        \DB::query($receive_order_column_sql)->execute();
    }

    public function down()
    {
        \DB::delete('receive_order_columns')->execute();
        \DB::delete('column_types_update_methods')->execute();
        \DB::delete('column_types')->execute();
        \DB::delete('receive_order_sections')->execute();
        \DB::delete('update_methods')->execute();
    }
}