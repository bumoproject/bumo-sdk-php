<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class AccountActivateOperation extends BaseOperation {
    private  $destAddress; //String
    private  $initBalance; //Long

    public function __construct() {
        $this->operationType = OperationType::ACCOUNT_ACTIVATE;
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
    public function getInitBalance() {
        return $this->initBalance;
    }

    /**
     * @param mixed $initBalance
     *
     * @return self
     */
    public function setInitBalance($initBalance) {
        $this->initBalance = $initBalance;
        return $this;
    }
}
?>