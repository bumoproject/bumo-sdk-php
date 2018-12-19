<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class BlockGetTransactionsResult {
    public $total_count;//Long
    /**
     * @var \src\model\response\result\data\TransactionHistory[]
     */
    public $transactions;
}
?>