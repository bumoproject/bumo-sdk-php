<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;
class TestTransactionFees {
    private $transactionFees; //TransactionFees  transaction

    /**
     * @return mixed
     */
    public function getTransactionFees() {
        return $this->transactionFees;
    }

    /**
     * @param mixed $transactionFees
     *
     * @return self
     */
    public function setTransactionFees($transactionFees) {
        $this->transactionFees = $transactionFees;
        return $this;
    }
}
?>