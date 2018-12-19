<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class TransactionParseBlobRequest{
    private $blob;

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
} 
?>