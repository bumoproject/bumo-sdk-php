<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;
class TransactionHistory {
    public $actual_fee;
    public $close_time;
    public $error_code;
    public $error_desc;
    public $hash;
    public $ledger_seq;
    /**
     * @var \src\model\response\result\data\Signature[]
     */
    public $signatures;
    /**
     * @var \src\model\response\result\data\TransactionInfo
     */
    public $transaction;
    public $tx_size;
}
?>