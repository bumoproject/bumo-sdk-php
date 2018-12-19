<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response;
use src\model\response\BaseResponse;

class BlockCheckStatusLedgerSeqResponse extends BaseResponse {
    /**
     * @var \src\model\response\result\data\LedgerSeq
     */
    public $ledger_manager;
}
?>