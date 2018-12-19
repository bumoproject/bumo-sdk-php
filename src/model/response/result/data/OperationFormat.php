<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;
class OperationFormat {
    private $type; //String  type
    private $sourceAddress; //String  source_address
    private $metadata; //String  metadata
    private $createAccount; //AccountActiviateInfo  create_account
    private $issueAsset; //AssetIssueInfo  issue_asset
    private $sendAsset; //AssetSendInfo  pay_asset
    private $sendBU; //BUSendInfo  pay_coin
    private $setMetadata; //AccountSetMetadataInfo  set_metadata
    private $setPrivilege; //AccountSetPrivilegeInfo  set_privilege
    private $log; //LogInfo  log

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceAddress() {
        return $this->sourceAddress;
    }

    /**
     * @param mixed $sourceAddress
     *
     * @return self
     */
    public function setSourceAddress($sourceAddress) {
        $this->sourceAddress = $sourceAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetadata() {
        return $this->metadata;
    }

    /**
     * @param mixed $metadata
     *
     * @return self
     */
    public function setMetadata($metadata) {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreateAccount() {
        return $this->createAccount;
    }

    /**
     * @param mixed $createAccount
     *
     * @return self
     */
    public function setCreateAccount($createAccount) {
        $this->createAccount = $createAccount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssueAsset() {
        return $this->issueAsset;
    }

    /**
     * @param mixed $issueAsset
     *
     * @return self
     */
    public function setIssueAsset($issueAsset) {
        $this->issueAsset = $issueAsset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSendAsset() {
        return $this->sendAsset;
    }

    /**
     * @param mixed $sendAsset
     *
     * @return self
     */
    public function setSendAsset($sendAsset) {
        $this->sendAsset = $sendAsset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSendBU() {
        return $this->sendBU;
    }

    /**
     * @param mixed $sendBU
     *
     * @return self
     */
    public function setSendBU($sendBU) {
        $this->sendBU = $sendBU;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSetMetadata() {
        return $this->setMetadata;
    }

    /**
     * @param mixed $setMetadata
     *
     * @return self
     */
    public function setSetMetadata($setMetadata) {
        $this->setMetadata = $setMetadata;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSetPrivilege() {
        return $this->setPrivilege;
    }

    /**
     * @param mixed $setPrivilege
     *
     * @return self
     */
    public function setSetPrivilege($setPrivilege) {
        $this->setPrivilege = $setPrivilege;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * @param mixed $log
     *
     * @return self
     */
    public function setLog($log) {
        $this->log = $log;
        return $this;
    }
}
?>