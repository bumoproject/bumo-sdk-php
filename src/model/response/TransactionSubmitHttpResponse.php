<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/8
 * Time: 17:49
 */

namespace src\model\response;


class TransactionSubmitHttpResponse {
    public $success_count;
    /**
     * @var \src\model\response\result\TransactionSubmitHttpResult[]
     */
    public $results;
}