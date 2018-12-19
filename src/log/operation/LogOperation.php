<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/6
 * Time: 15:23
 */
namespace src\log\operation;

require_once dirname(__FILE__) . "/../../../vendor/autoload.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationLog.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation/Type.php";

use \src\common\Constant;
use \src\exception\SdkError;
use \src\exception\SDKException;
use \src\crypto\key\KeyPair;
use \src\common\Tools;

class LogOperation {
    /**
     * Create a log in bu chain
     * @param \src\model\request\operation\LogCreateOperation $logCreateOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function create($logCreateOperation) {
        try {
            if(Tools::isEmpty($logCreateOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $logCreateOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $topic = $logCreateOperation->getTopic();
            if (Tools::isEmpty($topic) || !is_string($topic) || strlen($topic) < Constant::LOG_TOPIC_MIN || strlen($topic) > Constant::LOG_TOPIC_MAX) {
                throw new SDKException("INVALID_LOG_TOPIC_ERROR", null);
            }
            $datas = $logCreateOperation->getDatas();
            if (Tools::isEmpty($datas)) {
                throw new SDKException("INVALID_LOG_DATA_ERROR", null);
            }
            for ($i = 0; $i < count($datas); $i++) {
                $data = $datas[$i];
                if (!is_string($data) || strlen($data) < Constant::LOG_EACH_DATA_MIN || strlen($data) > Constant::LOG_EACH_DATA_MAX) {
                    throw new SDKException("INVALID_LOG_DATA_ERROR", null);
                }
            }
            $metadata = $logCreateOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build createLog operation
            $createLog = new \Protocol\OperationLog();
            $createLog->setTopic($topic);
            $createLog->setDatas($datas);

            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if(!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::LOG);
            $operation->setLog($createLog);
            return $operation;
        }
        catch (SDKException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new SDKException(SdkError::getCode("SYSTEM_ERROR"), $e->getMessage());
        }
    }
}