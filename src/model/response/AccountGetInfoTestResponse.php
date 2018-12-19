<?php
/**
 * User: fengruiming
 * Date: 2018/11/1
 * Time: 15:13
 */

namespace src\model\response;


class AccountGetInfoTestResponse {
    /**
     * @var int
     */
    public $error_code;
    /**
     * @var string
     */
    public $error_desc;
    /**
     * @var \src\model\response\result\AccountGetInfoTestResult
     */
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
            $keys = array_keys(SdkError::$errorDescArray[$error]);
            $values = array_values(SdkError::$errorDescArray[$error]);
            $this->errorCode = $keys[0];
            $this->errorDesc = $values[0];
        }
        else {
            $this->errorCode = $error;
            $this->errorDesc = $errorDesc;
        }
        $this->result = $result;
    }
}