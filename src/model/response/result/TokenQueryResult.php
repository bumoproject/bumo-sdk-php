<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class TokenQueryResult {
    private $type;//String    type
    private $value;//String   value

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type) {
        $this->type = $type;
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
}

?>