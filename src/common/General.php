<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\common;

use src\SDK;

class General{
    static private $instance;
    private $url;

    private function __construct() {
    }
    private function __clone() {
    }
    static public function getInstance() {
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }
        self::$instance->url = SDK::getUrl();
        return self::$instance;
    }

    public function getUrl() {
        return $this->url;
    }

    public function accountGetInfoUrl($address) {
        return $this->url . "/getAccountBase?address=" . urlencode($address);
    }

    public function accountGetAssetsUrl($address) {
        return $this->url . "/getAccount?address=" . $address;
    }

    public function accountGetMetadataUrl($address,$key) {
        if(!$key)
            return $this->url . "/getAccount?address=" . urlencode($address) ;
        else
            return $this->url . "/getAccount?address=" . urlencode($address) . "&key=" . urlencode($key);
    }

    public function assetGetUrl($address,$code,$issuer) {
        return $this->url . "/getAccount?address=" . urlencode($address) . "&code=" .   urlencode($code) . "&issuer=" . urlencode($issuer);
    }

    public function contractCallUrl() {
        return $this->url . "/callContract";
    }


    public function transactionEvaluationFee() {
        return $this->url . "/testTransaction";
    }

    public function transactionSubmitUrl() {
        return $this->url . "/submitTransaction";
    }

    public function transactionGetInfoUrl($hash) {
        return $this->url . "/getTransactionHistory?hash=" . urlencode($hash);
    }


    public function blockGetNumberUrl() {
        return $this->url . "/getLedger";
    }

    public function blockCheckStatusUrl() {
        return $this->url . "/getModulesStatus";
    }

    public function blockGetTransactionsUrl($blockNumber) {
        return $this->url . "/getTransactionHistory?ledger_seq=" . $blockNumber;
    }

    public function blockGetInfoUrl($blockNumber) {
        return $this->url . "/getLedger?seq=" . $blockNumber;
    }

    public function blockGetLatestInfoUrl() {
        return $this->url . "/getLedger";
    }

    public function blockGetValidatorsUrl($blockNumber) {
        return $this->url . "/getLedger?seq=" . $blockNumber . "&with_validator=true";
    }

    public function blockGetLatestValidatorsUrl() {
        return $this->url . "/getLedger?with_validator=true";
    }

    public function blockGetRewardUrl($blockNumber) {
        return $this->url . "/getLedger?seq=" . $blockNumber . "&with_block_reward=true";
    }

    public function blockGetLatestRewardUrl() {
        return $this->url . "/getLedger?with_block_reward=true";
    }

    public function blockGetFeesUrl($blockNumber) {
        return $this->url . "/getLedger?seq=" . $blockNumber . "&with_fee=true";
    }

    public function blockGetLatestFeeUrl() {
        return $this->url . "/getLedger?with_fee=true";
    }
}