<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class ContractInvokeByAssetOperation extends BaseOperation {
    private  $contractAddress; //Long
    private  $code; //string
    private  $issuer; //string
    private  $assetAmount; //Long
    private  $input; //Long

    public function __construct() {
        $this->operationType = OperationType::CONTRACT_INVOKE_BY_ASSET;
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
    public function getIssuer() {
        return $this->issuer;
    }

    /**
     * @param mixed $issuer
     *
     * @return self
     */
    public function setIssuer($issuer) {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssetAmount() {
        return $this->assetAmount;
    }

    /**
     * @param mixed $assetAmount
     *
     * @return self
     */
    public function setAssetAmount($assetAmount) {
        $this->assetAmount = $assetAmount;
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
}
?>
