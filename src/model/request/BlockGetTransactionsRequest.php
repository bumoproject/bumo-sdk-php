<?php
/**
 * @author [zjl] <[<email address>]>
 */
namespace src\model\request;
class BlockGetTransactionsRequest{
    private $blockNumber; //long

    /**
     * @return mixed
     */
    public function getBlockNumber() {
        return $this->blockNumber;
    }

    /**
     * @param mixed $blockNumber
     *
     * @return self
     */
    public function setBlockNumber($blockNumber) {
        $this->blockNumber = $blockNumber;
        return $this;
    }
}
?>