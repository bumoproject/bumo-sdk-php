<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request\operation;
use src\model\request\operation\BaseOperation;
use src\common\OperationType;

class AccountSetMetadataOperation extends BaseOperation {
    private $key;
    private $value;
    private $version;
    private $deleteFlag;

    public function __construct() {
        $this->operationType = OperationType::ACCOUNT_SET_METADATA;
    }

    /**
     * 
     */
    public function getOperationType() {
        return $this->operationType;
    }

    /**
     * @return mixed
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param mixed $key
     *
     * @return self
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @param mixed $version
     *
     * @return self
     */
    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleteFlag() {
        return $this->deleteFlag;
    }

    /**
     * @param mixed $deleteFlag
     *
     * @return self
     */
    public function setDeleteFlag($deleteFlag) {
        $this->deleteFlag = $deleteFlag;
        return $this;
    }
}
?>
