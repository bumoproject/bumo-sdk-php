<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/8
 * Time: 17:06
 */

namespace src\model\response\result\data;


class TransactionSubmitItem {
    public $transaction_blob;
    /**
     * @var \src\model\response\result\data\Signature[]
     */
    public $signatures;
}