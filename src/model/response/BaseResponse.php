<?php
namespace src\model\response;
use src\exception\SdkError;

class BaseResponse{
    public $error_code;
    public $error_desc;
    public $result;

    /**
     * [buildResponse description]
     * @param  [type] $errorCode [description]
     * @param  [type] $errorDesc [description]
     * @param  [type] $result    [description]
     * @return [type]            [description]
     */
    public function buildResponse($error, $errorDesc ,$result){
        if (is_null($errorDesc)) {
            $this->error_code = SdkError::getCode($error);
            $this->error_desc = SdkError::getDescription($error);
        }
        else {
            $this->error_code = $error;
            $this->error_desc = $errorDesc;
        }
        $this->result = $result;
    }
}
?>