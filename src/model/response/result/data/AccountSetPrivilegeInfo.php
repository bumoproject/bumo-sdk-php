<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;// //
class AccountSetPrivilegeInfo {
    public  $master_weight;
    /**
     * @var \src\model\response\result\data\Signer[]
     */
    public  $signers;
    public  $tx_threshold;
    /**
     * @var \src\model\response\result\data\TypeThreshold[]
     */
    public  $type_thresholds;
}
?>