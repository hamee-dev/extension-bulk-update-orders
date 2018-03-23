<?php
/**
 * マスタデータオブジェクト
 */
class Domain_Value_Master
{
    /**
     * ID
     *
     * @var string
     */
    private $_id;

    /**
     * マスタの項目名
     *
     * @var string
     */
    private $_name;

    /**
     * その他情報
     *
     * @var array
     */
    private $_params;

    /**
     * 無効なデータかどうか true:無効/false:有効
     *
     * @var bool
     */
    private $_disabled;

    /**
     * Domain_Value_Master constructor.
     *
     * @param string $id ユニークなID
     * @param string $name マスタの項目名
     * @param bool $disabled 無効なデータかどうか true:無効/false:有効
     * @param array $params 基本的にはidとnameだけで問題ないが、その他に情報が必要な場合ここに設定する（発送方法別項目タイプの対応する発送方法や項目タイプなど）
     */
    public function __construct(string $id, string $name, bool $disabled = false, array $params = [])
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_disabled = $disabled;
        $this->_params = $params;
    }

    public function get_id() : string {
        return $this->_id;
    }

    public function get_name() : string {
        return $this->_name;
    }

    public function get_params() : array {
        return $this->_params;
    }

    public function get_disabled() : bool {
        return $this->_disabled;
    }


    /**
     * マスタデータオブジェクトを連想配列に変換して取得する
     *
     * @return array
     */
    public function to_array() : array {
        return [
            'id' => $this->_id,
            'name' => $this->_name,
            'params' => $this->_params,
            'disabled' => $this->_disabled,
        ];
    }
}