<?php
/**
 * User: zjl
 * Date: 2018/08/08 10:00
 */
namespace src\model\request;
class AccountGetAssetsRequest{
    private $address;

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     *
     * @return self
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }
}
?>