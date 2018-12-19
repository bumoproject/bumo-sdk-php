<?php
/**
 * @author [zjl] <[<email address>]>
 */
namespace src\model\request;
class AccountGetBalanceRequest{
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