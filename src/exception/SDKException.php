<?php
namespace src\exception;

class SDKException extends \Exception {
    private $errorCode;
    private $errorDesc;
    public function __construct($error, $message) {
        if (is_null($message)) {
            $this->errorCode = SdkError::getCode($error);
            $this->errorDesc = SdkError::getDescription($error);
        } else {
            $this->errorCode = $error;
            $this->errorDesc = $message;
        }
    }

    /**
     * @return mixed
     */
    public function getErrorCode() {
        return $this->errorCode;
    }
  
    /**
     * @return mixed
     */
    public function getErrorDesc() {
        return $this->errorDesc;
    }
}
?>