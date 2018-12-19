<?php
/**
 * User: riven
 * Date: 2018/10/31
 * Time: 14:09
 */

namespace src\account\operation;

require_once dirname(__FILE__) . "/../../../vendor/autoload.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationCreateAccount.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/AccountThreshold.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/AccountPrivilege.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationSetMetadata.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationSetPrivilege.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Signer.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/OperationTypeThreshold.php";
require_once dirname(__FILE__) . "/../../crypto/protobuf/Protocol/Operation/Type.php";

use \src\model\request\operation\AccountActivateOperation;
use \src\model\request\operation\AccountSetMetadataOperation;
use \src\model\request\operation\AccountSetPrivilegeOperation;

use \src\common\Constant;
use \src\exception\SdkError;
use \src\exception\SDKException;

use \src\crypto\key\KeyPair;
use \src\common\Tools;
use src\model\response\result\data\Signer;
use src\model\response\result\data\TypeThreshold;

class AccountOperation {
    /**
     * Activate an account that is not in bu chain, which is mean that an account that is not written in the bu chain
     * are recorded in the bu chain and can be queried through the getInfo interface. But the premise is that the account
     * to be processed must be an active account.
     * Notice: Here just creates an operation
     * @param AccountActivateOperation $accountActivateOperation
     * @param string $tranSourceAddress
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function activate($accountActivateOperation, $tranSourceAddress) {
        try{
            if (!($accountActivateOperation instanceof AccountActivateOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountActivateOperation)){
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $accountActivateOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $destAddress = $accountActivateOperation->getDestAddress();
            $isDestValid = KeyPair::isAddressValid($destAddress);
            if(Tools::isEmpty($isDestValid)) {
                throw new SDKException("INVALID_DESTADDRESS_ERROR", null);
            }
            if((!Tools::isEmpty($sourceAddress) && strcmp($sourceAddress, $destAddress) == 0) || strcmp($tranSourceAddress, $destAddress) == 0) {
                throw new SDKException("SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR", null);
            }
            $initBalance = $accountActivateOperation->getInitBalance();
            if(Tools::isEmpty($initBalance) || is_string($initBalance) || !is_numeric($initBalance) || $initBalance <= 0) {
                throw new SDKException("INVALID_INITBALANCE_ERROR", null);
            }
            $metadata = $accountActivateOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build createAccount operation
            $accountThreshold = new \Protocol\AccountThreshold();
            $accountThreshold->setTxThreshold(1);

            $accountPrivilege = new \Protocol\AccountPrivilege();
            $accountPrivilege->setMasterWeight(1);
            $accountPrivilege->setThresholds($accountThreshold);

            $createAccount = new \Protocol\OperationCreateAccount();
            $createAccount->setDestAddress($destAddress);
            $createAccount->setInitBalance($initBalance);
            $createAccount->setPriv($accountPrivilege);

            // build operation
            $operation = new \Protocol\Operation();
            if(!Tools::isEmpty($sourceAddress)) {
                $operation->setSourceAddress($sourceAddress);
            }
            if(!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::CREATE_ACCOUNT); //CREATE_ACCOUNT
            $operation->setCreateAccount($createAccount);
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
     * Set the metadatas of an account, including key, value, version or deleteFlag
     * @param AccountSetMetadataOperation $accountSetMetadataOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function setMetadata($accountSetMetadataOperation) {
        try{
            if (!($accountSetMetadataOperation instanceof AccountSetMetadataOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountSetMetadataOperation)){
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $accountSetMetadataOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $key = $accountSetMetadataOperation->getKey();
            if(Tools::isEmpty($key) || !is_string($key) || strlen($key) > Constant::METADATA_KEY_MAX) {
                throw new SDKException("INVALID_DATAKEY_ERROR", null);
            }
            $value = $accountSetMetadataOperation->getValue();
            if(Tools::isEmpty($value) || !is_string($value) || strlen($value) > Constant::METADATA_KEY_MAX) {
                throw new SDKException("INVALID_DATAVALUE_ERROR", null);
            }
            $version = $accountSetMetadataOperation->getVersion();
            if(!Tools::isNULL($version) && (!is_int($version) || $version < 0)) {
                throw new SDKException("INVALID_DATAVERSION_ERROR", null);
            }
            $deleteFlag = $accountSetMetadataOperation->getDeleteFlag();
            if (!Tools::isNULL($deleteFlag) && !is_bool($deleteFlag)) {
                throw new SDKException("INVALID_DELETE_FLAG_ERROR", null);
            }
            $metadata = $accountSetMetadataOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }

            // build setMetadata operation
            $setMetadata = new \Protocol\OperationSetMetadata();
            $setMetadata->setKey($key);
            $setMetadata->setValue($value);
            if(!Tools::isNULL($version)) {
                $setMetadata->setVersion($version);
            }
            if(!Tools::isNULL($deleteFlag)) {
                $setMetadata->setDeleteFlag($deleteFlag);
            }

            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if(!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::SET_METADATA);
            $operation->setSetMetadata($setMetadata);
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
     * Set teh privilege of an account, including master key, signers, transaction threshold etc.
     * @param AccountSetPrivilegeOperation $accountSetPrivilegeOperation
     * @return \Protocol\Operation
     * @throws SDKException
     */
    public static function setPrivilege($accountSetPrivilegeOperation){
        try{
            if (!($accountSetPrivilegeOperation instanceof AccountSetPrivilegeOperation)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountSetPrivilegeOperation)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $accountSetPrivilegeOperation->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $masterWeight = $accountSetPrivilegeOperation->getMasterWeight();
            if(!Tools::isNULL($masterWeight)) {
                if(!is_string($masterWeight) || (is_string($masterWeight) &&
                        (!is_numeric($masterWeight) || bccomp($masterWeight, "0") < 0 ||
                            bccomp($masterWeight, Constant::UINT_MAX) > 0))) {
                    throw new SDKException("INVALID_MASTERWEIGHT_ERROR", null);
                }
            }
            $txThreshold = $accountSetPrivilegeOperation->getTxThreshold();
            if(!Tools::isNULL($txThreshold)) {
                if(!is_string($txThreshold) || (is_string($txThreshold) &&
                        (!is_numeric($txThreshold) || bccomp($txThreshold, "0") < 0 ||
                            bccomp($txThreshold, Constant::INT64_MAX) > 0))) {
                    throw new SDKException("INVALID_TX_THRESHOLD_ERROR", null);
                }
            }
            $metadata = $accountSetPrivilegeOperation->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            $signers = $accountSetPrivilegeOperation->getSigners();
            if (!is_array($signers)) {
                throw new SDKException("SIGNERS_NOT_ARRAY_ERROR", null);
            }
            $typeThresholds = $accountSetPrivilegeOperation->getTypeThresholds();
            if (!is_array($typeThresholds)){
                throw new SDKException("TYPE_THRESHOLDS_NOT_ARRAY_ERROR", null);
            }

            // build setPrivilege operation
            $setPrivilege = new \Protocol\OperationSetPrivilege();
            $setPrivilege->setMasterWeight($masterWeight);
            $setPrivilege->setTxThreshold($txThreshold);
            if(!Tools::isEmpty($signers)) {
                $signerArray = array();
                foreach ($signers as $key => $signer) {
                    if (!($signer instanceof Signer)){
                        throw new SDKException("INVALID_SIGNER_ERROR", null);
                    }
                    $isSignerAddressValid = KeyPair::isAddressValid($signer->address);
                    if(Tools::isEmpty($isSignerAddressValid)) {
                        throw new SDKException("INVALID_SIGNER_ADDRESS_ERROR", null);
                    }
                    if(Tools::isNULL($signer->weight) || !is_int($signer->weight) ||
                        $signer->weight < 0 || $signer->weight > Constant::UINT_MAX) {
                        throw new SDKException("INVALID_SIGNER_WEIGHT_ERROR", null);
                    }
                    $SignerPro = new \Protocol\Signer();
                    $SignerPro->setAddress($signer->address);
                    $SignerPro->setWeight($signer->weight);
                    array_push($signerArray,$SignerPro);
                }
                $setPrivilege->setSigners($signerArray);
            }
            if($typeThresholds) {
                $typeThresholdArray = array();
                foreach ($typeThresholds as $key => $typeThreshold) {
                    if (!($typeThreshold instanceof TypeThreshold)) {
                        throw new SDKException("INVALID_TYPE_THRESHOLD_ERROR", null);
                    }
                    if(Tools::isNULL($typeThreshold->type) || !is_int($typeThreshold->type) ||
                        $typeThreshold->type < 1 || $typeThreshold->type > 100) {
                        throw new SDKException("INVALID_TYPETHRESHOLD_TYPE_ERROR", null);
                    }
                    if(Tools::isNULL($typeThreshold->threshold) || !is_int($typeThreshold->threshold) ||
                        $typeThreshold->threshold < 0) {
                        throw new SDKException("INVALID_TYPE_THRESHOLD_ERROR", null);
                    }
                    $SignerPro = new \Protocol\OperationTypeThreshold();
                    try {
                        $SignerPro->setType($typeThreshold->type);
                    }
                    catch (\Exception $e) {
                        throw new SDKException("INVALID_TYPETHRESHOLD_TYPE_ERROR", null);
                    }
                    $SignerPro->setThreshold($typeThreshold->threshold);
                    array_push($typeThresholdArray,$SignerPro);
                }
                $setPrivilege->setTypeThresholds($typeThresholdArray);
            }
            // build operation
            $operation = new \Protocol\Operation();
            $operation->setSourceAddress($sourceAddress);
            if(!Tools::isEmpty($metadata)) {
                $operation->setMetadata($metadata);
            }
            $operation->setType(\Protocol\Operation\Type::SET_PRIVILEGE);
            $operation->setSetPrivilege($setPrivilege);
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
?>