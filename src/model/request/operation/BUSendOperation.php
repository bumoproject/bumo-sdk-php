<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class BUSendOperation extends BaseOperation {
    private $destAddress; //string
    private $amount; //long

    public function __construct() {
        $this->operationType = OperationType::BU_SEND;
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
    public function getDestAddress() {
        return $this->destAddress;
    }

    /**
     * @param mixed $destAddress
     *
     * @return self
     */
    public function setDestAddress($destAddress) {
        $this->destAddress = $destAddress;

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
