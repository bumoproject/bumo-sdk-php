<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 10:40
 */


namespace src\token\operation;
require_once dirname(__FILE__) . "/../../../vendor/autoload.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationPayCoin.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation/Type.php";

use \src\model\request\operation\BUSendOperation;
use \src\common\Tools;
use \src\exception\SdkError;
use \src\exception\SDKException;
use \src\crypto\key\KeyPair;

class BUOperation {
    /**
     * Send BU to other account
     * Notice: Here just creates an operation
     * @param BUSendOperation $buSendOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function send($buSendOperation, $tranSourceAddress){
        try{
            if(!($buSendOperation instanceof BUSendOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($buSendOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $buSendOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $destAddress = $buSendOperation->getDestAddress();
            $isDestValid = KeyPair::isAddressValid($destAddress);
            if(Tools::isEmpty($destAddress) || Tools::isEmpty($isDestValid)) {
                throw new SDKException("INVALID_DESTADDRESS_ERROR", null);
            }
            if((!Tools::isEmpty($sourceAddress) && strcmp($sourceAddress, $destAddress) == 0) || strcmp($tranSourceAddress, $destAddress) == 0) {
                throw new SDKException("SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR", null);
            }
            $amount = $buSendOperation->getAmount();
            if(Tools::isNULL($amount) || is_string($amount) || !is_numeric($amount) || $amount < 0) {
                throw new SDKException("INVALID_BU_AMOUNT_ERROR", null);
            }
            $metadata = $buSendOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build buSend operation
            $buSend = new \Protocol\OperationPayCoin();
            $buSend->setDestAddress($destAddress);
            $buSend->setAmount($amount);

            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            $operation->setType(\Protocol\Operation\Type::PAY_COIN);
            $operation->setPayCoin($buSend);
            if (!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
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