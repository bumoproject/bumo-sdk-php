<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class SDKInitResult {
    private $isSuccess;//boolean   is_success

    /**
     * @return mixed
     */
    public function getIsSuccess() {
        return $this->isSuccess;
    }

    /**
     * @param mixed $isSuccess
     *
     * @return self
     */
    public function setIsSuccess($isSuccess) {
        $this->isSuccess = $isSuccess;
        return $this;
    }
}
?>