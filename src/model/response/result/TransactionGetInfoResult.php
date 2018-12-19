<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class TransactionGetInfoResult{
    public $total_count;
    /**
     * @var \src\model\response\result\data\TransactionHistory[]
     */
    public $transactions;
}
?>