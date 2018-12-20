<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 10:40
 */

namespace src\token\operation;


require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationIssueAsset.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationPayAsset.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Asset.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/AssetKey.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation/Type.php";

use \src\model\request\operation\AssetIssueOperation;
use \src\model\request\operation\AssetSendOperation;
use \src\common\Tools;
use \src\common\Constant;
use \src\exception\SdkError;
use \src\exception\SDKException;
use \src\crypto\key\KeyPair;

class AssetOperation {
    /**
     * Issue an asset of an account.
     * Notice: Here just creates an operation
     * @param AssetIssueOperation $assetIssueOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function issue($assetIssueOperation) {
        try{
            if(!($assetIssueOperation instanceof AssetIssueOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($assetIssueOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $assetIssueOperation->getSourceAddress();
            $isValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $code = $assetIssueOperation->getCode();
            if(Tools::isEmpty($code) || !is_string($code) || strlen($code)> Constant::ASSET_CODE_MAX) {
                throw new SDKException("INVALID_ASSET_CODE_ERROR", null);
            }
            $amount = $assetIssueOperation->getAmount();
            if(Tools::isEmpty($amount) || !is_int($amount) || $amount < 0) {
                throw new SDKException("INVALID_ISSUE_AMOUNT_ERROR", null);
            }
            $metadata = $assetIssueOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build issueAsset operation
            $issueAsset = new \Protocol\OperationIssueAsset();
            $issueAsset->setCode($code);
            $issueAsset->setAmount($amount);

            // build operation
            $operation = new \Protocol\Operation();
            if(!Tools::isEmpty($sourceAddress)) {
                $operation->setSourceAddress($sourceAddress);
            }
            if(!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::ISSUE_ASSET);
            $operation->setIssueAsset($issueAsset);
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
     * Send an asset to other account
     * Notice: Here just creates an operation
     * @param AssetSendOperation $assetSendOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function send($assetSendOperation, $tranSourceAddress){
        try{
            if(!($assetSendOperation instanceof AssetSendOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($assetSendOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $assetSendOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $destAddress = $assetSendOperation->getDestAddress();
            $isDestValid = KeyPair::isAddressValid($destAddress);
            if(Tools::isEmpty($destAddress) || Tools::isEmpty($isDestValid)) {
                throw new SDKException("INVALID_DESTADDRESS_ERROR", null);
            }
            if((!Tools::isEmpty($sourceAddress) && strcmp($sourceAddress, $destAddress) == 0) || strcmp($tranSourceAddress, $destAddress) == 0) {
                throw new SDKException("SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR", null);
            }
            $code = $assetSendOperation->getCode();
            if(Tools::isEmpty($code) || strlen($code)> Constant::ASSET_CODE_MAX) {
                throw new SDKException("INVALID_ASSET_CODE_ERROR", null);
            }
            $issuer = $assetSendOperation->getIssuer();
            $isIssuerValid = KeyPair::isAddressValid($issuer);
            if(Tools::isEmpty($issuer) || Tools::isEmpty($isIssuerValid)) {
                throw new SDKException("INVALID_ISSUER_ADDRESS_ERROR", null);
            }
            $amount = $assetSendOperation->getAmount();
            if(Tools::isNULL($amount) || is_string($amount) || !is_numeric($amount) || $amount < 0) {
                throw new SDKException("INVALID_ASSET_AMOUNT_ERROR", null);
            }
            $metadata = $assetSendOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build sendAsset operation
            $sendAsset = new \Protocol\OperationPayAsset();
            $sendAsset->setDestAddress($destAddress);

            $asset = new \Protocol\Asset();
            $assetKey = new \Protocol\AssetKey();
            $assetKey->setCode($code);
            $assetKey->setIssuer($issuer);
            $asset->setKey($assetKey);
            $asset->setAmount($amount);
            $sendAsset->setAsset($asset);

            // build operation
            $operation = new \Protocol\Operation();
            if(!Tools::isEmpty($sourceAddress)) {
                $operation->setSourceAddress($sourceAddress);
            }
            if(!Tools::isEmpty($metadata)) {
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

}