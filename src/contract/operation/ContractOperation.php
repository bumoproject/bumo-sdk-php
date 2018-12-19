<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 11:26
 */
namespace src\contract\operation;

require_once dirname(__FILE__) . "/../../../vendor/autoload.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationCreateAccount.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/AccountThreshold.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/AccountPrivilege.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Contract.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationPayAsset.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Asset.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationPayCoin.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation/Type.php";


use \src\common\Constant;
use \src\exception\SdkError;
use \src\exception\SDKException;

use \src\crypto\key\KeyPair;
use \src\common\Tools;
use src\model\request\operation\ContractInvokeByAssetOperation;
use src\model\request\operation\ContractInvokeByBUOperation;
use src\model\request\operation\ContractCreateOperation;

class ContractOperation {
    /**
     * Create a contract, whose address will be returned after success
     * Notice: Here just creates an operation
     * @param ContractCreateOperation $contractCreateOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function create($contractCreateOperation) {
        try {
            if(!($contractCreateOperation instanceof ContractCreateOperation) || Tools::isEmpty($contractCreateOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $contractCreateOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $initBalance = $contractCreateOperation->getInitBalance();
            if(Tools::isEmpty($initBalance) ||is_string($initBalance) || !is_numeric($initBalance) || $initBalance <= 0) {
                throw new SDKException("INVALID_INITBALANCE_ERROR", null);
            }
            $type = $contractCreateOperation->getType();
            if(!Tools::isNULL($type) && (!is_int($type) || $type < 0)) {
                throw new SDKException("INVALID_CONTRACT_TYPE_ERROR", null);
            }
            $payload = $contractCreateOperation->getPayload();
            if(Tools::isEmpty($payload) || !is_string($payload)) {
                throw new SDKException("PAYLOAD_EMPTY_ERROR", null);
            }
            $metadata = $contractCreateOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            $initInput = $contractCreateOperation->getInitInput();
            if (!Tools::isEmpty($initInput) && !is_string($initInput)) {
                throw new SDKException("INIT_INPUT_NOT_STRING_ERROR", null);
            }

            // build createContract
            $createContract = new \Protocol\OperationCreateAccount();
            $createContract->setInitBalance($initBalance);
            if(!Tools::isEmpty($initInput)) {
                $createContract->setInitInput($initInput);
            }
            $accountThreshold = new \Protocol\AccountThreshold();
            $accountThreshold->setTxThreshold(1);
            $accountPrivilege = new \Protocol\AccountPrivilege();
            $accountPrivilege->setMasterWeight(0);
            $accountPrivilege->setThresholds($accountThreshold);
            $createContract->setPriv($accountPrivilege);

            $contract = new \Protocol\Contract();
            $contract->setPayload($payload);
            $createContract->setContract($contract);

            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if (!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::CREATE_ACCOUNT);
            $operation->setCreateAccount($createContract);
            return $operation;
        }
        catch (SDKException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new SDKException(20000, $e->getMessage());
        }
    }

    /**
     * Send asset and invoke contract
     * Notice: Here just creates an operation
     * @param ContractInvokeByAssetOperation $contractInvokeByAssetOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function invokeByAsset($contractInvokeByAssetOperation, $transSourceAddress) {
        try{
            if(!($contractInvokeByAssetOperation instanceof ContractInvokeByAssetOperation) || Tools::isEmpty($contractInvokeByAssetOperation)){
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $contractInvokeByAssetOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $contractAddress = $contractInvokeByAssetOperation->getContractAddress();
            $isContractValid = KeyPair::isAddressValid($contractAddress);
            if(Tools::isEmpty($isContractValid)) {
                throw new SDKException("INVALID_CONTRACTADDRESS_ERROR", null);
            }
            if ((!Tools::isEmpty($sourceAddress) && strcmp($contractAddress, $sourceAddress) === 0) ||
                (strcmp($contractAddress, $transSourceAddress) === 0)) {
                throw new SDKException("SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR", null);
            }
            $code = $contractInvokeByAssetOperation->getCode();
            if(!Tools::isNULL($code) && (!is_string($code) || strlen($code) > Constant::ASSET_CODE_MAX)) {
                throw new SDKException("INVALID_ASSET_CODE_ERROR", null);
            }
            $issuer = $contractInvokeByAssetOperation->getIssuer();
            $isIssuerValid = KeyPair::isAddressValid($issuer);
            if(!Tools::isEmpty($issuer) && Tools::isEmpty($isIssuerValid)) {
                throw new SDKException("INVALID_ISSUER_ADDRESS_ERROR", null);
            }
            $amount = $contractInvokeByAssetOperation->getAssetAmount();
            if(!Tools::isNULL($amount) && (is_string($amount) || !is_numeric($amount) || $amount < 0)) {
                throw new SDKException("INVALID_ASSET_AMOUNT_ERROR", null);
            }
            $metadata = $contractInvokeByAssetOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            $initInput = $contractInvokeByAssetOperation->getInput();
            if (!Tools::isEmpty($initInput) && !is_string($initInput)) {
                throw new SDKException("INIT_INPUT_NOT_STRING_ERROR", null);
            }


            // build sendAsset operation
            $sendAsset = new \Protocol\OperationPayAsset();
            $sendAsset->setDestAddress($contractAddress);
            if(!Tools::isEmpty($initInput)) {
                $sendAsset->setInput($initInput);
            }
            if(!Tools::isEmpty($code) && !Tools::isEmpty($issuer) && !Tools::isNULL($amount) && $amount >0){
                $asset = new \Protocol\Asset();
                $assetKey = new \Protocol\AssetKey();
                $assetKey->setCode($code);
                $assetKey->setIssuer($issuer);
                $asset->setAmount($amount);
                $asset->setKey($assetKey);
                $sendAsset->setAsset($asset);
            }
            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if (!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::PAY_ASSET);
            $operation->setPayAsset($sendAsset);
            return $operation;
        }
        catch (SDKException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new SDKException(20000, $e->getMessage());
        }
    }

    /**
     * Send BU and invoke contract
     * Notice: Here just creates an operation
     * @param ContractInvokeByBUOperation $contractInvokeByBUOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function invokeByBU($contractInvokeByBUOperation, $transSourceAddress){
        try{
            if(!($contractInvokeByBUOperation instanceof ContractInvokeByBUOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($contractInvokeByBUOperation)){
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $contractInvokeByBUOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $contractAddress = $contractInvokeByBUOperation->getContractAddress();
            $isContractValid = KeyPair::isAddressValid($contractAddress);
            if(Tools::isEmpty($isContractValid)) {
                throw new SDKException("INVALID_CONTRACTADDRESS_ERROR", null);
            }
            if((!Tools::isEmpty($sourceAddress) && strcmp($sourceAddress, $contractAddress) == 0) ||
                (strcmp($transSourceAddress, $contractAddress) == 0)) {
                throw new SDKException("SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR", null);
            }
            $amount = $contractInvokeByBUOperation->getBuAmount();
            if(!Tools::isNULL($amount) && (is_string($amount) || !is_numeric($amount) || $amount < 0)) {
                throw new SDKException("INVALID_BU_AMOUNT_ERROR", null);
            }
            $metadata = $contractInvokeByBUOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            $initInput = $contractInvokeByBUOperation->getInput();
            if (!Tools::isEmpty($initInput) && !is_string($initInput)) {
                throw new SDKException("INIT_INPUT_NOT_STRING_ERROR", null);
            }

            // build sendBU operation
            $sendBU = new \Protocol\OperationPayCoin();
            $sendBU->setDestAddress($contractAddress);
            if (!Tools::isNULL($amount)) {
                $sendBU->setAmount($amount);
            }
            if (!Tools::isEmpty($initInput)) {
                $sendBU->setInput($initInput);
            }

            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if (!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::PAY_COIN);
            $operation->setPayCoin($sendBU);
            return $operation;
        }
        catch (SDKException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new SDKException(20000, $e->getMessage());
        }
    }
}