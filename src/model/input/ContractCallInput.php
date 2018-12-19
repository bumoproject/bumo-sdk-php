<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 18:28
 */
namespace src\model\input;

class ContractCallInput {
    public $source_address;
    public $contract_address;
    public $code;
    public $input;
    public $opt_type;
    public $contract_balance;
    public $fee_limit;
    public $gas_price;

}