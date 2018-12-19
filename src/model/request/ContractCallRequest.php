<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class ContractCallRequest{
    private $sourceAddress; //String
    private $contractAddress;//String
    private $code;//String
    private $input;//String
    private $contractBalance;//Long
    private $optType;//Integer
    private $feeLimit;//Long
    private $gasPrice;//Long

    /**
     * @return mixed
     */
    public function getSourceAddress() {
        return $this->sourceAddress;
    }

    /**
     * @param mixed $sourceAddress
     *
     * @return self
     */
    public function setSourceAddress($sourceAddress) {
        $this->sourceAddress = $sourceAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContractAddress() {
        return $this->contractAddress;
    }

    /**
     * @param mixed $contractAddress
     *
     * @return self
     */
    public function setContractAddress($contractAddress) {
        $this->contractAddress = $contractAddress;
        return $this;
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
    public function getInput() {
        return $this->input;
    }

    /**
     * @param mixed $input
     *
     * @return self
     */
    public function setInput($input) {
        $this->input = $input;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContractBalance() {
        return $this->contractBalance;
    }

    /**
     * @param mixed $contractBalance
     *
     * @return self
     */
    public function setContractBalance($contractBalance) {
        $this->contractBalance = $contractBalance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptType() {
        return $this->optType;
    }

    /**
     * @param mixed $optType
     *
     * @return self
     */
    public function setOptType($optType) {
        $this->optType = $optType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeeLimit() {
        return $this->feeLimit;
    }

    /**
     * @param mixed $feeLimit
     *
     * @return self
     */
    public function setFeeLimit($feeLimit) {
        $this->feeLimit = $feeLimit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGasPrice() {
        return $this->gasPrice;
    }

    /**
     * @param mixed $gasPrice
     *
     * @return self
     */
    public function setGasPrice($gasPrice) {
        $this->gasPrice = $gasPrice;
        return $this;
    }
} 
?>