<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/27
 * Time: 15:49
 */

namespace src\model\request;


class SDKConfigure {
    private $url;
    private $timeOut;
    private $chainId;

    /**
     * SDKConfigure constructor.
     */
    public function __construct() {
        $this->timeOut = 15 * 1000;
        $this->chainId = 0;
    }


    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getTimeOut() {
        return $this->timeOut;
    }

    /**
     * @param mixed $timeOut
     */
    public function setTimeOut($timeOut) {
        $this->timeOut = $timeOut;
    }

    /**
     * @return mixed
     */
    public function getChainId() {
        return $this->chainId;
    }

    /**
     * @param mixed $chainId
     */
    public function setChainId($chainId) {
        $this->chainId = $chainId;
    }


}