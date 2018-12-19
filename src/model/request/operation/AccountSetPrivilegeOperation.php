<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class AccountSetPrivilegeOperation extends BaseOperation {
    private $masterWeight; //string
    private $signers = array(); //Signer[]
    private $txThreshold; //string
    private $typeThresholds = array();//TypeThreshold[]

    public function __construct() {
        $this->operationType = OperationType::ACCOUNT_SET_PRIVILEGE;
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
    public function getMasterWeight() {
        return $this->masterWeight;
    }

    /**
     * @param mixed $masterWeight
     *
     * @return self
     */
    public function setMasterWeight($masterWeight) {
        $this->masterWeight = $masterWeight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSigners() {
        return $this->signers;
    }

    /**
     * @param mixed $signers
     *
     * @return self
     */
    public function setSigners($signers) {
        $this->signers = $signers;
        return $this;
    }

    /**
     * @param mixed $signers
     *
     * @return self
     */
    public function addSigner($signer) {
        if ($signer) {
            array_push($this->signers, $signer);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTxThreshold() {
        return $this->txThreshold;
    }

    /**
     * @param mixed $txThreshold
     *
     * @return self
     */
    public function setTxThreshold($txThreshold) {
        $this->txThreshold = $txThreshold;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeThresholds() {
        return $this->typeThresholds;
    }

    /**
     * @param mixed $typeThresholds
     *
     * @return self
     */
    public function setTypeThresholds($typeThresholds) {
        $this->typeThresholds = $typeThresholds;
        return $this;
    }

    /**
     * @param mixed $typeThresholds
     *
     * @return self
     */
    public function addTypeThreshold($typeThreshold) {
        if ($this->typeThresholds) {
            $this->typeThresholds = [];
        }
        if ($typeThreshold) {
            array_push($this->typeThresholds, $typeThreshold);
        }
        return $this;
    }
}
?>
