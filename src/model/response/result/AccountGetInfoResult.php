<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class AccountGetInfoResult {
    private $address; //String
    private $balance; //Long
    private $nonce;  //Long
    private $priv; //Priv

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
    public function getBalance() {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     *
     * @return self
     */
    public function setBalance($balance) {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNonce() {
        return $this->nonce;
    }

    /**
     * @param mixed $nonce
     *
     * @return self
     */
    public function setNonce($nonce) {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriv() {
        return $this->priv;
    }

    /**
     * @param mixed $priv
     *
     * @return self
     */
    public function setPriv($priv) {

//        // var_dump($priv);exit;
//        $privOb = new \src\model\response\result\data\Priv();
//        $privOb->setMasterWeight(isset($priv->master_weight)?$priv->master_weight:"");
//        if(isset($priv->signers))
//            $privOb->setSigners($priv->signers);
//        if(isset($priv->thresholds))
//            $privOb->setThreshold($priv->thresholds);
        if ($priv) {
            $this->priv = $priv;
        }
        return $this;
    }
}
?>





 