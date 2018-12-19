<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class BlockGetRewardResult{
    public $block_reward;//Long
    /**
     * @var \src\model\response\result\data\ValidatorRewardInfo[]
     */
    public $validators_reward;
}
?>