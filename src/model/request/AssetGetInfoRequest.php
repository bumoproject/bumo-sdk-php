<?php
/**
 * @author [zjl] <[<email address>]>
 */
namespace src\model\request;
class AssetGetInfoRequest{
    private $address;//string
    private $code;//string
    private $issuer;//string

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

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return self
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssuer() {
        return $this->issuer;
    }

    /**
     * @param mixed $issuer
     *
     * @return self
     */
    public function setIssuer($issuer) {
        $this->issuer = $issuer;
        return $this;
    }
}
?>