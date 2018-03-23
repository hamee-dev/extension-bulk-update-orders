<?php
namespace Fuel\Migrations;

class Create_table
{

    public function up()
    {
        $company_sql = "
CREATE TABLE IF NOT EXISTS `companies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `main_function_id` VARCHAR(255) NOT NULL COMMENT 'メイン機能企業ID',
  `company_ne_id` VARCHAR(255) NOT NULL COMMENT 'ネクストエンジン企業ID',
  `name` VARCHAR(255) NULL DEFAULT NULL COMMENT '企業名',
  `name_kana` VARCHAR(255) NULL DEFAULT NULL COMMENT '企業名かな',
  `stoped_at` DATETIME NULL DEFAULT NULL COMMENT '停止日時',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `main_function_id_UNIQUE` (`main_function_id` ASC))
ENGINE = InnoDB COMMENT='メイン機能単位の企業情報';
        ";
        \DB::query($company_sql)->execute();

        $user_sql = "
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企業ID',
  `uid` VARCHAR(255) NOT NULL COMMENT 'uid',
  `pic_id` VARCHAR(255) NOT NULL COMMENT 'メイン機能担当者ID',
  `pic_ne_id` VARCHAR(255) NOT NULL COMMENT 'ネクストエンジン担当者ID',
  `pic_name` VARCHAR(255) NULL DEFAULT NULL COMMENT '担当者名',
  `pic_kana` VARCHAR(255) NULL DEFAULT NULL COMMENT '担当者カナ',
  `access_token` VARCHAR(255) NOT NULL COMMENT 'アクセストークン',
  `access_token_end_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'アクセストークン有効期限',
  `refresh_token` VARCHAR(255) NOT NULL COMMENT 'リフレッシュトークン',
  `refresh_token_end_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'リフレッシュトークン有効期限',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC),
  CONSTRAINT `users_ibfk_1`
    FOREIGN KEY (`company_id`)
    REFERENCES `companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='メイン機能単位のアプリ利用ユーザー情報';
        ";
        \DB::query($user_sql)->execute();

        $column_type_sql = "
CREATE TABLE IF NOT EXISTS `column_types` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` VARCHAR(255) NOT NULL COMMENT 'タイプ名',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB COMMENT='項目のタイプ（型）';
        ";
        \DB::query($column_type_sql)->execute();

        $update_method_sql = "
CREATE TABLE IF NOT EXISTS `update_methods` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` VARCHAR(255) NOT NULL COMMENT '更新方法名',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB COMMENT='更新方法';
        ";
        \DB::query($update_method_sql)->execute();

        $column_types_update_method_sql = "
CREATE TABLE IF NOT EXISTS `column_types_update_methods` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `column_type_id` INT UNSIGNED NOT NULL COMMENT 'カラムタイプID',
  `update_method_id` INT UNSIGNED NOT NULL COMMENT '更新方法ID',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `column_type_id_update_method_id_UNIQUE` (`column_type_id` ASC, `update_method_id` ASC),
  CONSTRAINT `column_types_update_methods_ibfk_1`
    FOREIGN KEY (`column_type_id`)
    REFERENCES `column_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `column_types_update_methods_ibfk_2`
    FOREIGN KEY (`update_method_id`)
    REFERENCES `update_methods` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='項目のタイプに対する更新方法';
        ";
        \DB::query($column_types_update_method_sql)->execute();

        $receive_order_section_sql = "
CREATE TABLE IF NOT EXISTS `receive_order_sections` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` VARCHAR(255) NOT NULL COMMENT 'セクション名',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB COMMENT='受注伝票のセクション';
        ";
        \DB::query($receive_order_section_sql)->execute();

        $receive_order_column_sql = "
CREATE TABLE IF NOT EXISTS `receive_order_columns` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `receive_order_section_id` INT UNSIGNED NOT NULL COMMENT '受注伝票セクションID',
  `column_type_id` INT UNSIGNED NOT NULL COMMENT 'カラムタイプID',
  `physical_name` VARCHAR(255) NOT NULL COMMENT 'カラムの物理名',
  `logical_name` VARCHAR(255) NOT NULL COMMENT 'カラムの論理名',
  `input_min_length` INT NOT NULL COMMENT '入力最小文字数',
  `input_max_length` INT NOT NULL COMMENT '入力最大文字数',
  `master_name` VARCHAR(255) DEFAULT NULL COMMENT 'マスタ名',
  `order_amount` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '受注金額関連項目フラグ',
  `payment` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '支払関連項目フラグ',
  `delivery` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '発送関連項目フラグ',
  `display_order` INT NOT NULL COMMENT '表示順',
  `disabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '無効',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `physical_name_UNIQUE` (`physical_name` ASC),
  UNIQUE INDEX `logical_name_UNIQUE` (`logical_name` ASC),
  CONSTRAINT `receive_order_columns_ibfk_1`
    FOREIGN KEY (`receive_order_section_id`)
    REFERENCES `receive_order_sections` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `receive_order_columns_ibfk_2`
    FOREIGN KEY (`column_type_id`)
    REFERENCES `column_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='受注伝票の項目情報';
        ";
        \DB::query($receive_order_column_sql)->execute();

        $bulk_update_setting_sql = "
CREATE TABLE IF NOT EXISTS `bulk_update_settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企業ID',
  `name` VARCHAR(255) NOT NULL COMMENT '名称',
  `allow_update_shipment_confirmed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '出荷確定済の更新許可',
  `allow_update_yahoo_cancel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Yahoo!ショッピングの受注キャンセル更新許可',
  `allow_optimistic_lock_update_retry` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '他者更新済み受注伝票の再実行有無',
  `allow_reflect_order_amount` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '受注金額関連項目更新値の総合計への反映許可',
  `temporary` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '一時的な設定の判定',
  `original_bulk_update_setting_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT '一括更新設定ID',
  `created_user_id` INT UNSIGNED NOT NULL COMMENT '作成ユーザーID',
  `last_updated_user_id` INT UNSIGNED NOT NULL COMMENT '最終更新ユーザーID',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` DATETIME NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`),
  INDEX `company_id_temporary` (`company_id` ASC, `temporary` ASC),
  CONSTRAINT `bulk_update_settings_ibfk_1`
    FOREIGN KEY (`company_id`)
    REFERENCES `companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bulk_update_settings_ibfk_2`
    FOREIGN KEY (`original_bulk_update_setting_id`)
    REFERENCES `bulk_update_settings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bulk_update_settings_ibfk_3`
    FOREIGN KEY (`created_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bulk_update_settings_ibfk_4`
    FOREIGN KEY (`last_updated_user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='一括更新設定情報';
        ";
        \DB::query($bulk_update_setting_sql)->execute();

        $bulk_update_column_sql = "
CREATE TABLE IF NOT EXISTS `bulk_update_columns` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `bulk_update_setting_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '一括更新設定ID',
  `receive_order_column_id` INT UNSIGNED NOT NULL COMMENT '受注伝票カラムID',
  `update_method_id` INT UNSIGNED NOT NULL COMMENT '更新方法ID',
  `update_value` TEXT NOT NULL COMMENT '更新値',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `bulk_update_setting_id_receive_order_column_id_UNIQUE` (`bulk_update_setting_id` ASC, `receive_order_column_id` ASC),
  CONSTRAINT `bulk_update_columns_ibfk_1`
    FOREIGN KEY (`bulk_update_setting_id`)
    REFERENCES `bulk_update_settings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bulk_update_columns_ibfk_2`
    FOREIGN KEY (`receive_order_column_id`)
    REFERENCES `receive_order_columns` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bulk_update_columns_ibfk_3`
    FOREIGN KEY (`update_method_id`)
    REFERENCES `update_methods` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='一括更新項目情報';
        ";
        \DB::query($bulk_update_column_sql)->execute();

        $request_key_sql = "
CREATE TABLE IF NOT EXISTS `request_keys` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企業ID',
  `request_date` DATE NOT NULL COMMENT 'リクエスト日付',
  `request_number` INT NOT NULL COMMENT 'リクエスト番号',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `company_id_UNIQUE` (`company_id` ASC),
  CONSTRAINT `request_keys_ibfk_1`
    FOREIGN KEY (`company_id`)
    REFERENCES `companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='一括更新の実行リクエストキー情報';
        ";
        \DB::query($request_key_sql)->execute();

        $execution_bulk_update_setting_sql = "
CREATE TABLE IF NOT EXISTS `execution_bulk_update_settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `request_key` VARCHAR(20) NOT NULL COMMENT 'リクエストキー',
  `user_id` INT UNSIGNED NOT NULL COMMENT 'ユーザーID',
  `extension_execution_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '拡張機能実行ID',
  `target_order_count` INT UNSIGNED NOT NULL COMMENT '対象伝票数',
  `executed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '実行済み',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企業ID',
  `name` VARCHAR(255) NOT NULL COMMENT '名称',
  `allow_update_shipment_confirmed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '出荷確定済の更新許可',
  `allow_update_yahoo_cancel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Yahoo!ショッピングの受注キャンセル更新許可',
  `allow_optimistic_lock_update_retry` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '他者更新済み受注伝票の再実行有無',
  `allow_reflect_order_amount` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '受注金額関連項目更新値の総合計への反映許可',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `extension_execution_id_UNIQUE` (`extension_execution_id` ASC),
  UNIQUE INDEX `request_key_company_id_UNIQUE` (`request_key` ASC, `company_id` ASC),
  INDEX `company_id_executed` (`company_id` ASC, `executed` ASC),
  CONSTRAINT `execution_bulk_update_settings_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `execution_bulk_update_settings_ibfk_2`
    FOREIGN KEY (`company_id`)
    REFERENCES `companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='実行する一括更新設定情報';
        ";
        \DB::query($execution_bulk_update_setting_sql)->execute();

        $execution_bulk_update_column_sql = "
CREATE TABLE IF NOT EXISTS `execution_bulk_update_columns` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `execution_bulk_update_setting_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '一括更新設定ID',
  `receive_order_column_id` INT UNSIGNED NOT NULL COMMENT '受注伝票カラムID',
  `update_method_id` INT UNSIGNED NOT NULL COMMENT '更新方法ID',
  `update_value` TEXT NOT NULL COMMENT '更新値',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `bulk_update_setting_id_receive_order_column_id_UNIQUE` (`execution_bulk_update_setting_id` ASC, `receive_order_column_id` ASC),
  CONSTRAINT `execution_bulk_update_columns_ibfk_1`
    FOREIGN KEY (`execution_bulk_update_setting_id`)
    REFERENCES `execution_bulk_update_settings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `execution_bulk_update_columns_ibfk_2`
    FOREIGN KEY (`receive_order_column_id`)
    REFERENCES `receive_order_columns` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `execution_bulk_update_columns_ibfk_3`
    FOREIGN KEY (`update_method_id`)
    REFERENCES `update_methods` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='実行する一括更新項目情報';
        ";
        \DB::query($execution_bulk_update_column_sql)->execute();

        $excluded_receive_order_sql = "
CREATE TABLE IF NOT EXISTS `excluded_receive_orders` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `execution_bulk_update_setting_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '実行一括更新設定ID',
  `receive_order_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '伝票番号',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `bulk_update_setting_id_receive_order_id_UNIQUE` (`execution_bulk_update_setting_id` ASC, `receive_order_id` ASC),
  CONSTRAINT `exclued_receive_orders_ibfk_1`
    FOREIGN KEY (`execution_bulk_update_setting_id`)
    REFERENCES `execution_bulk_update_settings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB COMMENT='一括更新の除外受注伝票情報';
        ";
        \DB::query($excluded_receive_order_sql)->execute();
    }

    public function down()
    {
        // 外部キー制約を外す
        \DB::query('SET FOREIGN_KEY_CHECKS=0;')->execute();

        \DBUtil::drop_table('companies');
        \DBUtil::drop_table('users');
        \DBUtil::drop_table('column_types');
        \DBUtil::drop_table('update_methods');
        \DBUtil::drop_table('column_types_update_methods');
        \DBUtil::drop_table('receive_order_sections');
        \DBUtil::drop_table('receive_order_columns');
        \DBUtil::drop_table('bulk_update_settings');
        \DBUtil::drop_table('bulk_update_columns');
        \DBUtil::drop_table('request_keys');
        \DBUtil::drop_table('execution_bulk_update_settings');
        \DBUtil::drop_table('execution_bulk_update_columns');
        \DBUtil::drop_table('excluded_receive_orders');

        // 外部キー制約をつける
        \DB::query('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }
}