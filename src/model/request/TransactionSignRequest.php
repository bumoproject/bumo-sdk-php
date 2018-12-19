<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class TransactionSignRequest{
    private $blob;
    private $privateKeys = array(); //string[]

    /**
     * @return mixed
     */
    public function getBlob() {
        return $this->blob;
    }

    /**
     * @param mixed $blob
     *
     * @return self
     */
    public function setBlob($blob) {
        $this->blob = $blob;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivateKeys() {
        return $this->privateKeys;
    }

    /**
     * @param mixed $privateKeys
     *
     * @return self
     */
    public function setPrivateKeys($privateKeys) {
        if($privateKeys) {
            foreach ($privateKeys as $key => $value) {
                array_push($this->privateKeys , $value);
            }
        }
        return $this;
    }

    /**
     * @param mixed $privateKey
     *
     * @return self
     */
    public function addPrivateKey($privateKey) {
        if ($privateKey) {
            array_push($this->privateKeys , $privateKey);
        }
        return $this;
    }
} 
?>