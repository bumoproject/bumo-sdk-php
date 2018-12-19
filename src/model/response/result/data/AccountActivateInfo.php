<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;//  
class AccountActivateInfo {
    public $dest_address;
    /**
     * @var \src\model\response\result\data\ContractInfo
     */
    public $contract;
    /**
     * @var \src\model\response\result\data\Priv
     */
    public $priv;
    /**
     * @var \src\model\response\result\data\MetadataInfo[]
     */
    public $metadatas;
    public $init_balance;
    public $init_input;
}
?>