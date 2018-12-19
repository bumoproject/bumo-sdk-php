<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\common\OperationType;

class BaseOperation {
    protected $operationType;//OperationType
    private $sourceAddress;//String
    private $metadata;//String

    /**
     * 
     */
    public function getOperationType() {
        return $this->operationType;
    }
    

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
