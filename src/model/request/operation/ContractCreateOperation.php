<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class ContractCreateOperation extends BaseOperation {
    private $initBalance;//Long
    private $type;//Integer
    private $payload;//String
    private $initInput;//String

    public function __construct() {
        $this->operationType = OperationType::CONTRACT_CREATE;
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

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     *
     * @return self
     */
    public function setPayload($payload) {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInitInput() {
        return $this->initInput;
    }

    /**
     * @param mixed $initInput
     *
     * @return self
     */
    public function setInitInput($initInput) {
        $this->initInput = $initInput;
        return $this;
    }
}
?>