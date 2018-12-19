<?php
/**
 * @author [zjl] <[<email address>]>
 */
namespace src\model\request;

class TransactionBuildBlobRequest{
    private $sourceAddress; //String 
    private $nonce;//Long 
    private $gasPrice;//Long 
    private $feeLimit;//Long 
    private $operations = array();//BaseOperation[]
    private $ceilLedgerSeq;//Long
    private $metadata;//String 

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
    public function getNonce() {
        return $this->nonce;
    }

    /**
     * @param mixed $nonce
     *
     * @return self
     */
    public function setNonce($nonce) {
        $this->nonce = $nonce;
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
    public function getOperations() {
        return $this->operations;
    }

    /**
     * @param mixed $operations
     *
     * @return self
     */
    public function setOperations($operations) {
        $this->operations = $operations;
        return $this;
    }

    /**
     * @param mixed $operation
     *
     * @return self
     */
    public function addOperation($operation) {
        if ($operation) {
            array_push($this->operations, $operation);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCeilLedgerSeq() {
        return $this->ceilLedgerSeq;
    }

    /**
     * @param mixed $ceilLedgerSeq
     *
     * @return self
     */
    public function setCeilLedgerSeq($ceilLedgerSeq) {
        $this->ceilLedgerSeq = $ceilLedgerSeq;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetadata() {
        return $this->metadata;
    }

    /**
     * @param mixed $metadata
     *
     * @return self
     */
    public function setMetadata($metadata) {
        $this->metadata = $metadata;
        return $this;
    }
}
?>