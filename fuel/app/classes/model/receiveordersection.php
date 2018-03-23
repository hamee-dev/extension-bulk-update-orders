<?php
/**
 * receive_order_sections
 * 受注伝票のセクション
 *
 * Class Model_Receiveordersection
 */
class Model_Receiveordersection extends Model_Base
{
    const RECEIVE_ORDER_BASE   = 1;
    const RECEIVE_ORDER_OPTION = 2;

    protected static $_table_name = 'receive_order_sections';

    protected static $_properties = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected static $_has_many = [
        'receive_order_columns' => [
            'model_to'       => 'Model_Receiveordercolumn',
            'key_from'       => 'id',
            'key_to'         => 'receive_order_section_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];
}