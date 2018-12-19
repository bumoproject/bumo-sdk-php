<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class ContractGetAddressRequest{
    private $hash;

    /**
     * @return mixed
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     *
     * @return self
     */
    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }
} 
?>