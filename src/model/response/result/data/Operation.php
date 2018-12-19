<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;

class Operation {
    public $type;
    public $source_address;
    public $metadata;
    /**
     * @var \src\model\response\result\data\AccountActivateInfo
     */
    public $create_account;
    /**
     * @var \src\model\response\result\data\AssetIssueInfo
     */
    public $issue_asset;
    /**
     * @var \src\model\response\result\data\AssetSendInfo
     */
    public $pay_asset;
    /**
     * @var \src\model\response\result\data\BUSendInfo
     */
    public $pay_coin;
    /**
     * @var \src\model\response\result\data\AccountSetMetadataInfo
     */
    public $set_metadata;
    /**
     * @var \src\model\response\result\data\AccountSetPrivilegeInfo
     */
    public $set_privilege;
    /**
     * @var \src\model\response\result\data\LogInfo
     */
    public $log;
}
?>