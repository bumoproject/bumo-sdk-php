<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class ContractCheckValidRequest{
    private $contractAddress;

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
} 
?>