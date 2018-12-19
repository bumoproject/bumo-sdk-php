<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class AssetIssueOperation extends BaseOperation {
    private  $code; //String
    private  $amount; //Long

    public function __construct() {
        $this->operationType = OperationType::ASSET_ISSUE;
    }

    /**
     * 
     */
    public function getOperationType() {
        return $this->operationType;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return self
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     *
     * @return self
     */
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }
}
?>
