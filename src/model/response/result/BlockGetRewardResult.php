<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;

class BlockGetRewardResult{
    /**
     * @var \src\model\response\result\data\Rewards[]
     */
    public $validators = array();

    /**
     * @var \src\model\response\result\data\Rewards[]
     */
    public $kols = array();

    /**
     * @param mixed $operation
     *
     * @return self
     */
    public function addValidator($validator) {
        if ($validator) {
            array_push($this->validators, $validator);
        }
        return $this;
    }

    /**
     * @param mixed $operation
     *
     * @return self
     */
    public function addKol($kol) {
        if ($kol) {
            array_push($this->kols, $kol);
        }
        return $this;
    }
}
?>