<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 20:28
 */
namespace src\blockchain;

require_once dirname(__FILE__) . "/../../vendor/autoload.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/GPBMetadata/Common.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/GPBMetadata/Chain.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Transaction.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Operation.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Operation/Type.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationCreateAccount.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/AccountThreshold.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/AccountPrivilege.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationSetMetadata.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationSetPrivilege.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Signer.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationTypeThreshold.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationIssueAsset.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationPayAsset.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Asset.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/AssetKey.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/Contract.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationLog.php";
require_once dirname(__FILE__) . "/../crypto/protobuf/Protocol/OperationPayCoin.php";

use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBType;

use \src\model\request\TransactionBuildBlobRequest;
use src\model\request\TransactionEvaluateFeeRequest;
use \src\model\request\TransactionParseBlobRequest;
use \src\model\request\TransactionSignRequest;
use src\model\request\TransactionSubmitItemsRequest;
use \src\model\request\TransactionSubmitRequest;
use src\model\request\TransactionGetInfoRequest;

use src\model\request\TransactionTestRequest;
use \src\model\response\BlockGetNumberResponse;
use src\model\response\result\data\Signature;
use src\model\response\result\data\TransactionItem;
use src\model\response\result\data\TransactionSubmitItem;
use \src\model\response\TransactionBuildBlobResponse;
use src\model\response\TransactionEvaluateFeeResponse;
use \src\model\response\TransactionParseBlobResponse;
use \src\model\response\TransactionSignResponse;
use src\model\response\TransactionSubmitHttpResponse;
use \src\model\response\TransactionSubmitResponse;
use \src\model\response\TransactionGetInfoResponse;

use \src\model\response\result\TransactionBuildBlobResult;
use \src\model\response\result\TransactionParseBlobResult;
use \src\model\response\result\TransactionEvaluateFeeResult;
use \src\model\response\result\TransactionSignResult;
use \src\model\response\result\TransactionSubmitResult;
use \src\model\response\result\TransactionGetInfoResult;

use \src\account\operation\AccountOperation;
use src\SDK;
use \src\token\operation\AssetOperation;
use \src\token\operation\BUOperation;
use \src\contract\operation\ContractOperation;
use \src\log\operation\LogOperation;

use \src\model\request\operation\BaseOperation;
use src\model\response\result\data\Operation;
use src\model\response\result\data\TransactionInfo;
use src\model\response\result\data\AccountActivateInfo;
use src\model\response\result\data\AccountSetMetadataInfo;
use src\model\response\result\data\AccountSetPrivilegeInfo;
use src\model\response\result\data\AssetInfo;
use src\model\response\result\data\AssetIssueInfo;
use src\model\response\result\data\AssetKey;
use src\model\response\result\data\AssetSendInfo;
use src\model\response\result\data\BUSendInfo;
use src\model\response\result\data\ContractInfo;
use src\model\response\result\data\LogInfo;
use src\model\response\result\data\Signer;
use src\model\response\result\data\TypeThreshold;

use \src\crypto\key\KeyPair;
use \src\common\General;
use \src\common\Http;
use \src\common\Tools;
use \src\common\Constant;
use src\common\OperationType;
use \src\exception\SDKException;

class Transaction {
    /**
     * Serialize the transaction
     * @param TransactionBuildBlobRequest $transactionBuildBlobRequest
     * @return TransactionBuildBlobResponse
     */
    public function buildBlob($transactionBuildBlobRequest) {
        $transactionBuildBlobResponse = new TransactionBuildBlobResponse();
        $transactionBuildBlobResult = new TransactionBuildBlobResult();
        try {
            if(!($transactionBuildBlobRequest instanceof TransactionBuildBlobRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionBuildBlobRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $transactionBuildBlobRequest->getSourceAddress();
            $isSourceAddress = KeyPair::isAddressValid($sourceAddress);
            if (Tools::isEmpty($isSourceAddress)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $nonce = $transactionBuildBlobRequest->getNonce();
            if (Tools::isEmpty($nonce) || !is_int($nonce) || $nonce < 1) {
                throw new SDKException("INVALID_NONCE_ERROR", null);
            }
            $gasPrice = $transactionBuildBlobRequest->getGasPrice();
            if (Tools::isEmpty($gasPrice) || !is_int($gasPrice) || $gasPrice < Constant::GAS_PRICE_MIN) {
                throw new SDKException("INVALID_GASPRICE_ERROR", null);
            }
            $feeLimit = $transactionBuildBlobRequest->getFeeLimit();
            if (Tools::isEmpty($feeLimit) || !is_int($feeLimit) || $feeLimit < Constant::FEE_LIMIT_MIN) {
                throw new SDKException("INVALID_FEELIMIT_ERROR", null);
            }
            $ceilLedgerSeq = $transactionBuildBlobRequest->getCeilLedgerSeq();
            if (!Tools::isNULL($ceilLedgerSeq) && (!is_int($ceilLedgerSeq) || $ceilLedgerSeq < 0)) {
                throw new SDKException("INVALID_CEILLEDGERSEQ_ERROR", null);
            }
            $metadata = $transactionBuildBlobRequest->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            // build Operation
            $operations = $transactionBuildBlobRequest->getOperations();
            if (Tools::isEmpty($operations)) {
                throw new SDKException("OPERATIONS_EMPTY_ERROR", null);
            }
            $operationArray = $this->buildOperations($operations, $sourceAddress);

            // build Transaction
            $transaction = new \Protocol\Transaction();
            if(!Tools::isEmpty($metadata)) {
                $transaction->setMetadata($metadata);
            }
            $transaction->setSourceAddress($sourceAddress);
            $transaction->setNonce($nonce);
            $transaction->setFeeLimit($feeLimit);
            $transaction->setGasPrice($gasPrice);
            $transaction->setOperations($operationArray);
            $chainId = SDK::getConfigure()->getChainId();
            if (is_int($chainId) && $chainId > 0) {
                $transaction->setChainId($chainId);
            }
            if (!Tools::isEmpty($ceilLedgerSeq)) {
                $baseUrl = General::getInstance()->blockGetNumberUrl();
                $result = Http::get($baseUrl);
                if (Tools::isEmpty($result)) {
                    throw new SDKException("CONNECTNETWORK_ERROR", null);
                }
                $blockGetNumberResponse = Tools::jsonToClass($result, new BlockGetNumberResponse());
                $errorCode = $blockGetNumberResponse->error_code;
                if (!Tools::isEmpty($errorCode)) {
                    $errorDesc = $blockGetNumberResponse->error_desc;
                    throw new SDKException($errorCode, $errorDesc);
                }
                $realCeilLedgerSeq = $blockGetNumberResponse->result->header->seq + $ceilLedgerSeq;
                if (!is_int($realCeilLedgerSeq)) {
                    throw new SDKException("INVALID_CEILLEDGERSEQ_ERROR", null);
                }
                $transaction->setCeilLedgerSeq($realCeilLedgerSeq);
            }
            $serialTransaction = $transaction->serializeToString();
            $transactionBlob = bin2hex($serialTransaction);
            $transactionBuildBlobResult->transaction_blob = $transactionBlob;
            $hash = hash('sha256', $serialTransaction,true);
            $transactionBuildBlobResult->hash = bin2hex($hash);
            $transactionBuildBlobResponse->buildResponse("SUCCESS", null, $transactionBuildBlobResult);
        }
        catch(SDKException $e) {
            $transactionBuildBlobResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionBuildBlobResult);
        }
        catch (\Exception $e) {
            $transactionBuildBlobResponse->buildResponse(20000, $e->getMessage(), $transactionBuildBlobResult);
        }
        return $transactionBuildBlobResponse;
    }

    /**
     * Parse transaction blob into the transaction
     * @param TransactionParseBlobRequest $transactionParseBlobRequest
     * @return TransactionParseBlobResponse
     */
    public function parseBlob($transactionParseBlobRequest) {
        $transactionParseBlobResponse = new TransactionParseBlobResponse();
        $transactionParseBlobResult = new TransactionParseBlobResult();
        try {
            if(!($transactionParseBlobRequest instanceof TransactionParseBlobRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionParseBlobRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blob = $transactionParseBlobRequest->getBlob();
            $tranParse = new \Protocol\Transaction();
            try {
                $tranParse->mergeFromString(hex2bin($blob));
            }
            catch (\Exception $e) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $transactionParseBlobResult = $this->transactionToInfo($tranParse);
            $transactionParseBlobResponse->buildResponse("SUCCESS", null, $transactionParseBlobResult);
        }
        catch(SDKException $e) {
            $transactionParseBlobResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionParseBlobResult);
        }
        catch (\Exception $e) {
            $transactionParseBlobResponse->buildResponse(20000, $e->getMessage(), $transactionParseBlobResult);
        }
        return $transactionParseBlobResponse;
    }

    /**
     * Evaluate the fee of a transaction
     * @param TransactionEvaluateFeeRequest $transactionEvaluateFeeRequest
     * @return TransactionEvaluateFeeResponse
     */
    public function evaluateFee($transactionEvaluateFeeRequest) {
        $transactionEvaluateFeeResponse = new TransactionEvaluateFeeResponse();
        $transactionEvaluateFeeResult = new TransactionEvaluateFeeResult();
        try {
            if(!($transactionEvaluateFeeRequest instanceof TransactionEvaluateFeeRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionEvaluateFeeRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $transactionEvaluateFeeRequest->getSourceAddress();
            $isSourceAddress = KeyPair::isAddressValid($sourceAddress);
            if (Tools::isEmpty($isSourceAddress)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $nonce = $transactionEvaluateFeeRequest->getNonce();
            if (Tools::isEmpty($nonce) || !is_int($nonce) || $nonce < 1) {
                throw new SDKException("INVALID_NONCE_ERROR", null);
            }
            $signatureNum = $transactionEvaluateFeeRequest->getSignatureNumber();
            if (!Tools::isNULL($signatureNum) && (!is_int($signatureNum) || $signatureNum < 1)) {
                throw new SDKException("INVALID_SIGNATURENUMBER_ERROR", null);
            }
            $ceilLedgerSeq = $transactionEvaluateFeeRequest->getCeilLedgerSeq();
            if (!Tools::isNULL($ceilLedgerSeq) && (!is_int($ceilLedgerSeq) || $ceilLedgerSeq < 0)) {
                throw new SDKException("INVALID_CEILLEDGERSEQ_ERROR", null);
            }
            $metadata = $transactionEvaluateFeeRequest->getMetadata();
            if (!Tools::isEmpty($metadata) && !is_string($metadata)) {
                throw new SDKException("METADATA_NOT_STRING_ERROR", null);
            }
            $operations = $transactionEvaluateFeeRequest->getOperations();
            if (Tools::isEmpty($operations)) {
                throw new SDKException("OPERATIONS_EMPTY_ERROR", null);
            }
            if (Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $operationArray = $this->buildOperations($operations, $sourceAddress);

            // build Transaction
            $transaction = new \Protocol\Transaction();
            if(!Tools::isEmpty($metadata)) {
                $transaction->setMetadata($metadata);
            }
            $transaction->setSourceAddress($sourceAddress);
            $transaction->setNonce($nonce);
            $transaction->setOperations($operationArray);
            if (!Tools::isEmpty($ceilLedgerSeq)) {
                $baseUrl = General::getInstance()->blockGetNumberUrl();
                $result = Http::get($baseUrl);
                if (Tools::isEmpty($result)) {
                    throw new SDKException("CONNECTNETWORK_ERROR", null);
                }
                $blockGetNumberResponse = Tools::jsonToClass($result, new BlockGetNumberResponse());
                $errorCode = $blockGetNumberResponse->error_code;
                if (!Tools::isEmpty($errorCode)) {
                    $errorDesc = $blockGetNumberResponse->error_desc;
                    throw new SDKException($errorCode, $errorDesc);
                }
                $realCeilLedgerSeq = $blockGetNumberResponse->result->header->seq + $ceilLedgerSeq;
                if (!is_int($realCeilLedgerSeq)) {
                    throw new SDKException("INVALID_CEILLEDGERSEQ_ERROR", null);
                }
                $transaction->setCeilLedgerSeq($realCeilLedgerSeq);
            }

            $transactionInfo = $this->transactionToInfo($transaction);
            $transactionTestRequest = new TransactionTestRequest();
            $transactionItem = new TransactionItem();
            $transactionItem->transaction_json = $transactionInfo;
            $transactionTestRequest->items[0] = $transactionItem;
            $baseUrl = General::getInstance()->transactionEvaluationFee();
            $result = Http::post($baseUrl, json_encode($transactionTestRequest));
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $transactionEvaluateFeeResponse = Tools::jsonToClass($result, $transactionEvaluateFeeResponse);
        }
        catch(SDKException $e) {
            $transactionEvaluateFeeResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionEvaluateFeeResult);
        }
        catch (\Exception $e) {
            $transactionEvaluateFeeResponse->buildResponse(20000, $e->getMessage(), $transactionEvaluateFeeResult);
        }
        return $transactionEvaluateFeeResponse;
    }

    /**
     * Sign a transaction
     * @param TransactionSignRequest $transactionSignRequest
     * @return TransactionSignResponse
     */
    public function sign($transactionSignRequest) {
        $transactionSignResponse = new TransactionSignResponse();
        $transactionSignResult = new TransactionSignResult();
        try {
            if(!($transactionSignRequest instanceof TransactionSignRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionSignRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blob = $transactionSignRequest->getBlob();
            if (Tools::isEmpty($blob)) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $blobBytes = null;
            try {
                $blobBytes = hex2bin($blob);
                $tranParse = new \Protocol\Transaction();
                $tranParse->mergeFromString($blobBytes);
            }
            catch (\Exception $e) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $privateKeys = $transactionSignRequest->getPrivateKeys();
            if (Tools::isEmpty($privateKeys)) {
                throw new SDKException("PRIVATEKEY_NULL_ERROR", null);
            }
            $signatures = array();
            for ($i = 0; $i < count($privateKeys); $i++) {
                if (!KeyPair::isPrivateKeyValid($privateKeys[$i])) {
                    throw new SDKException("PRIVATEKEY_ONE_ERROR", null);
                }
                $signature = new Signature();
                $signature->sign_data = bin2hex(KeyPair::signByPrivateKey($blobBytes, $privateKeys[$i]));
                $signature->public_key = KeyPair::getEncPublicKeyByPrivateKey($privateKeys[$i]);
                array_push($signatures, $signature);
            }
            $transactionSignResult->signatures = $signatures;
            $transactionSignResponse->buildResponse("SUCCESS", null, $transactionSignResult);
        }
        catch(SDKException $e) {
            $transactionSignResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionSignResult);
        }
        catch (\Exception $e) {
            $transactionSignResponse->buildResponse(20000, $e->getMessage(), $transactionSignResult);
        }
        return $transactionSignResponse;
    }

    /**
     * Submit a transaction to bu chain
     * @param TransactionSubmitRequest $transactionSubmitRequest
     * @return TransactionSubmitResponse
     */
    public function submit($transactionSubmitRequest) {
        $transactionSubmitResponse = new TransactionSubmitResponse();
        $transactionSubmitResult = new TransactionSubmitResult();
        try {
            if(!($transactionSubmitRequest instanceof TransactionSubmitRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionSubmitRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blob = $transactionSubmitRequest->getTransactionBlob();
            if (Tools::isEmpty($blob)) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $blobBytes = null;
            try {
                $blobBytes = hex2bin($blob);
                $tranParse = new \Protocol\Transaction();
                $tranParse->mergeFromString($blobBytes);
            }
            catch (\Exception $e) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $signatures = $transactionSubmitRequest->getSignatures();
            if (Tools::isEmpty($signatures)) {
                throw new SDKException("SIGNATURE_EMPTY_ERROR", null);
            }
            $transactionItems = new TransactionSubmitItemsRequest();
            $transactionItem = new TransactionSubmitItem();
            $transactionItem->transaction_blob = $blob;
            for ($i = 0; $i < count($signatures); $i++) {
                $signData = $signatures[$i]->sign_data;
                if (Tools::isEmpty($signData)) {
                    throw new SDKException("SIGNDATA_NULL_ERROR", null);
                }
                $publicKey = $signatures[$i]->public_key;
                if (Tools::isEmpty($publicKey)) {
                    throw new SDKException("PUBLICKEY_NULL_ERROR", null);
                }
            }
            $transactionItem->signatures = $signatures;
            $transactionItems->items[0] = $transactionItem;

            // submit
            $baseUrl = General::getInstance()->transactionSubmitUrl();
            $result = Http::post($baseUrl, json_encode($transactionItems));
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $transactionSubmitHttpResponse = Tools::jsonToClass($result, new TransactionSubmitHttpResponse());
            $httpResults = $transactionSubmitHttpResponse->results;
            if (Tools::isEmpty($httpResults)) {
                throw new SDKException("INVALID_BLOB_ERROR", null);
            }
            $transactionSubmitResult->hash = $httpResults[0]->hash;
            $errorCode = $httpResults[0]->error_code;
            if ($errorCode != 0) {
                $errorDesc = $httpResults[0]->error_desc;
                throw new SDKException($errorCode, $errorDesc);
            }
            $transactionSubmitResponse->buildResponse("SUCCESS", null, $transactionSubmitResult);
        }
        catch(SDKException $e) {
            $transactionSubmitResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionSubmitResult);
        }
        catch (\Exception $e) {
            $transactionSubmitResponse->buildResponse(20000, $e->getMessage(), $transactionSubmitResult);
        }
        return $transactionSubmitResponse;
    }

    /**
     * Get the information of specific block
     * @param TransactionGetInfoRequest $transactionGetInfoRequest
     * @return TransactionGetInfoResponse
     */
    function getInfo($transactionGetInfoRequest) {
        $transactionGetInfoResponse = new TransactionGetInfoResponse();
        $transactionGetInfoResult = new TransactionGetInfoResult();
        try {
            if(!($transactionGetInfoRequest instanceof TransactionGetInfoRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($transactionGetInfoRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $hash = $transactionGetInfoRequest->getHash();
            if (Tools::isEmpty($hash) || strlen($hash) != Constant::HASH_HEX_LENGTH) {
                throw new SDKException("INVALID_HASH_ERROR", null);
            }
            $baseUrl = General::getInstance()->transactionGetInfoUrl($hash);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $transactionGetInfoResponse = Tools::jsonToClass($result, new $transactionGetInfoResponse());
            $errorCode = $transactionGetInfoResponse->error_code;
            if (4 == $errorCode) {
                throw new SDKException($errorCode, "Transaction(" . $hash . ") doest not exist");
            }
        }
        catch(SDKException $e) {
            $transactionGetInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $transactionGetInfoResult);
        }
        catch (\Exception $e) {
            $transactionGetInfoResponse->buildResponse(20000, $e->getMessage(), $transactionGetInfoResult);
        }
        return $transactionGetInfoResponse;
    }

    /**
     * Build operations
     * @param BaseOperation[] $operations
     * @param string $tranSourceAddress
     * @return RepeatedField
     * @throws SDKException
     */
    private function buildOperations($operations, $tranSourceAddress) {
        $operationArray = new RepeatedField(GPBType::MESSAGE, \Protocol\Operation::class);
        for ($i = 0; $i < count($operations); $i++) {
            $operation = null;
            $operationType = $operations[$i]->getOperationType();
            switch ($operationType) {
                case OperationType::ACCOUNT_ACTIVATE:
                    $operation = AccountOperation::activate($operations[$i], $tranSourceAddress);
                    break;
                case OperationType::ACCOUNT_SET_METADATA:
                    $operation = AccountOperation::setMetadata($operations[$i]);
                    break;
                case OperationType::ACCOUNT_SET_PRIVILEGE:
                    $operation = AccountOperation::setPrivilege($operations[$i]);
                    break;
                case OperationType::ASSET_ISSUE:
                    $operation = AssetOperation::issue($operations[$i]);
                    break;
                case OperationType::ASSET_SEND:
                    $operation = AssetOperation::send($operations[$i], $tranSourceAddress);
                    break;
                case OperationType::BU_SEND:
                    $operation = BUOperation::send($operations[$i], $tranSourceAddress);
                    break;
                case OperationType::CONTRACT_CREATE:
                    $operation = ContractOperation::create($operations[$i]);
                    break;
                case OperationType::CONTRACT_INVOKE_BY_ASSET:
                    $operation = ContractOperation::invokeByAsset($operations[$i], $tranSourceAddress);
                    break;
                case OperationType::CONTRACT_INVOKE_BY_BU:
                    $operation = ContractOperation::invokeByBU($operations[$i], $tranSourceAddress);
                    break;
                case OperationType::LOG_CREATE:
                    $operation = LogOperation::create($operations[$i]);
                    break;
                default:
                    throw new SDKException("OPERATIONS_ONE_ERROR", null);
            }
            if (Tools::isEmpty($operations)) {
                throw new SDKException("OPERATIONS_ONE_ERROR", null);
            }
            ///array_push($operationArray, $operation);
            $operationArray[] = $operation;
        }
        return $operationArray;
    }

    /**
     * Build operations
     * @param \Protocol\Transaction $transaction
     * @return boolean | TransactionInfo
     * @throws SDKException
     */
    private function transactionToInfo($transaction) {
        $operations = $transaction->getOperations();
        if (Tools::isEmpty($transaction) || Tools::isEmpty($operations)) {
            return false;
        }
        $transactionInfo = new TransactionInfo();
        $transactionInfo->source_address = $transaction->getSourceAddress();
        $transactionInfo->nonce = $transaction->getNonce();
        if (!Tools::isEmpty($transaction->getMetadata())) {
            $transactionInfo->metadata = $transaction->getMetadata();
        }
        if (!Tools::isNULL($transaction->getCeilLedgerSeq())) {
            $transactionInfo->ceil_ledger_seq = $transaction->getCeilLedgerSeq();
        }
        $transactionInfo->fee_limit = $transaction->getFeeLimit();
        $transactionInfo->gas_price = $transaction->getGasPrice();
        $transactionInfo->chain_id = $transaction->getChainId();

        $operationArray = array();
        for ($i = 0; $i < $operations->count(); $i++) {
            $operation = new Operation();
            $type = $operations[$i]->getType();
            $operation->type = $type;
            $sourceAddress = $operations[$i]->getSourceAddress();
            if (!Tools::isEmpty($sourceAddress)) {
                $operation->source_address = $sourceAddress;
            }
            $metadata = $operations[$i]->getMetadata();
            if (!Tools::isEmpty($metadata)) {
                $operation->metadata = $metadata;
            }
            switch ($type) {
                case \Protocol\Operation\Type::CREATE_ACCOUNT: {
                    $createAccount = new AccountActivateInfo();
                    $createAccount->dest_address = $operations[$i]->getCreateAccount()->getDestAddress();
                    $createAccount->init_balance = $operations[$i]->getCreateAccount()->getInitBalance();
                    if (!Tools::isEmpty($operations[$i]->getCreateAccount()->getContract())) {
                        $contractInfo = new ContractInfo();
                        $contractInfo->type = $operations[$i]->getCreateAccount()->getContract()->getType();
                        $payload = $operations[$i]->getCreateAccount()->getContract()->getPayload();
                        if (!Tools::isEmpty($payload)) {
                            $contractInfo->payload = $payload;
                            $createAccount->contract = $contractInfo;
                        }
                    }
                    $initInput = $operations[$i]->getCreateAccount()->getInitInput();
                    if (!Tools::isEmpty($initInput)) {
                        $createAccount->init_input = $initInput;
                    }
                    $operation->create_account = $createAccount;
                }
                    break;
                case \Protocol\Operation\Type::ISSUE_ASSET: {
                    $issueAsset = new AssetIssueInfo();
                    $issueAsset->code = $operations[$i]->getIssueAsset()->getCode();
                    $issueAsset->amount = $operations[$i]->getIssueAsset()->getAmount();
                    $operation->issue_asset = $issueAsset;
                }
                    break;
                case \Protocol\Operation\Type::PAY_ASSET: {
                    $payAsset = new AssetSendInfo();
                    $payAsset->dest_address = $operations[$i]->getPayAsset()->getDestAddress();
                    $assetKey = new AssetKey();
                    $assetKey->code = $operations[$i]->getPayAsset()->getAsset()->getKey()->getCode();
                    $assetKey->issuer = $operations[$i]->getPayAsset()->getAsset()->getKey()->getIssuer();
                    $asset = new AssetInfo();
                    $asset->key = $assetKey;
                    $asset->amount = $operations[$i]->getPayAsset()->getAsset()->getAmount();
                    $payAsset->asset = $asset;
                    $input = $operations[$i]->getPayAsset()->getInput();
                    if (!Tools::isEmpty($input)) {
                        $payAsset->input = $input;
                    }
                    $operation->pay_asset = $payAsset;
                }
                    break;
                case \Protocol\Operation\Type::SET_METADATA: {
                    $setMetadata = new AccountSetMetadataInfo();
                    $setMetadata->key = $operations[$i]->getSetMetadata()->getKey();
                    $setMetadata->value = $operations[$i]->getSetMetadata()->getValue();
                    $setMetadata->version = $operations[$i]->getSetMetadata()->getVersion();
                    $deleteFlag = $operations[$i]->getSetMetadata()->getDeleteFlag();
                    if (!Tools::isEmpty($deleteFlag)) {
                        $setMetadata->delete_flag = $deleteFlag;
                    }
                    $operation->set_metadata = $setMetadata;
                }
                    break;
                case \Protocol\Operation\Type::SET_PRIVILEGE: {
                    $setPrivilege = new AccountSetPrivilegeInfo();
                    $masterWeight = $operations[$i]->getSetPrivilege()->getMasterWeight();
                    if (!Tools::isEmpty($masterWeight)) {
                        $setPrivilege->master_weight = $masterWeight;
                    }
                    $txThreshold = $operations[$i]->getSetPrivilege()->getTxThreshold();
                    if (!Tools::isEmpty($txThreshold)) {
                        $setPrivilege->tx_threshold = $txThreshold;
                    }
                    $signers = $operations[$i]->getSetPrivilege()->getSigners();
                    if ($signers->count() > 0) {
                        $signerArray = array();
                        for ($i = 0; $i < $signers->count(); $i++) {
                            $signer = new Signer();
                            $signer->address = $signers[i]->getAddress();
                            $signer->weight = $signers[i]->getWeight();
                            array_push($signerArray, $signer);
                        }
                        $setPrivilege->signers = $signers;
                    }
                    $typeThresholds = $operations[$i]->getSetPrivilege()->GetTypeThresholds();
                    if ($typeThresholds->count() > 0) {
                        $typeThresholdArray = array();
                        for ($i = 0; $i < $typeThresholds->count(); $i++) {
                            $typeThreshold = new TypeThreshold();
                            $typeThreshold->type = $typeThresholds[$i]->getType();
                            $typeThreshold->threshold = $typeThresholds[$i]->getThreshold;
                            array_push($typeThresholdArray, $typeThreshold);
                        }
                        $setPrivilege->type_thresholds = $typeThresholdArray;
                    }
                    $operation->set_privilege = $setPrivilege;

                }
                    break;
                case \Protocol\Operation\Type::PAY_COIN: {
                    $payCoin = new BUSendInfo();
                    $payCoin->dest_address = $operations[$i]->getPayCoin()->getDestAddress();
                    $payCoin->amount = $operations[$i]->getPayCoin()->getAmount();
                    $input = $operations[$i]->getPayCoin()->getInput();
                    if (!Tools::isEmpty($input)) {
                        $payCoin->input = $input;
                    }
                    $operation->pay_coin = $payCoin;
                }
                    break;
                case \Protocol\Operation\Type::LOG: {
                    $log = new LogInfo();
                    $log->topic = $operations[$i]->getLog()->getTopic();
                    $datas = $operations[$i]->getLog()->getDatas();
                    $dataArray = array();
                    for ($i = 0; $i < $datas->count(); $i++) {
                        array_push($dataArray, $datas[$i]);
                    }
                    if (!Tools::isEmpty($dataArray)) {
                        $log->datas = $dataArray;
                    }

                    $operation->log = $log;
                }
                    break;
                default:
                    throw new SDKException("OPERATIONS_ONE_ERROR", nil);
            }
            array_push($operationArray, $operation);
        }
        if (!Tools::isEmpty($operationArray)) {
            $transactionInfo->operations = $operationArray;
        }

        return $transactionInfo;
    }
}