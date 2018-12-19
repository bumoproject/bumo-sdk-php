<?php
/**
 * @author [zjl] <[<email address>]>
 */
namespace src\model\request;
class BlockGetValidatorsRequest{
    private $blockNumber;

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