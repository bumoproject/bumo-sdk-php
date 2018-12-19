<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\model\response\result;
class TokenMessageResult{
    private $metadatas;//MetadataInfo[]   metadatas
    private $contract;//ContractInfo   contract

    /**
     * @return mixed
     */
    public function getMetadatas() {
        return $this->metadatas;
    }

    /**
     * @param mixed $metadatas
     *
     * @return self
     */
    public function setMetadatas($metadatas) {
        $this->metadatas = $metadatas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContract() {
        return $this->contract;
    }

    /**
     * @param mixed $contract
     *
     * @return self
     */
    public function setContract($contract) {
        $this->contract = $contract;
        return $this;
    }
}