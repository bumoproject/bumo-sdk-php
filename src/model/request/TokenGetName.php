<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;

class TokenGetName{
    private $contractName;

    /**
     * @return mixed
     */
    public function getContractName() {
        return $this->contractName;
    }

    /**
     * @param mixed $contractName
     *
     * @return self
     */
    public function setContractName($contractName) {
        $this->contractName = $contractName;
        return $this;
    }
} 
?>