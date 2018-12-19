<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class ContractCallResult {
    public $logs; //JSONObject
    public $query_rets; //JSONArray
    /**
     * @var \src\model\response\result\data\ContractStat
     */
    public $stat;
    /**
     * @var \src\model\response\result\data\TransactionEnvs[]
     */
    public $txs;
}
?>