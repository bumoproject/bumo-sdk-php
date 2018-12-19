<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result\data;

class Priv {
    public $master_weight; //String  master_weight
    /**
     * @var \src\model\response\result\data\Signer[]
     */
    public $signers;
    /**
     * @var \src\model\response\result\data\Threshold
     */
    public $thresholds;
}
?>