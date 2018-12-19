<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 11:25
 */
namespace src\contract;
use \src\model\request\ContractGetInfoRequest;
use \src\model\request\ContractGetAddressRequest;
use \src\model\request\ContractCheckValidRequest;
use \src\model\request\ContractCallRequest;

use \src\model\response\ContractGetInfoResponse;
use \src\model\response\ContractGetAddressResponse;
use \src\model\response\TransactionGetInfoResponse;
use src\model\response\ContractCheckValidResponse;
use src\model\response\ContractCallResponse;

use \src\model\response\result\ContractGetInfoResult;
use \src\model\response\result\ContractGetAddressResult;
use \src\model\response\result\ContractCheckValidResult;
use \src\model\response\result\ContractCallResult;

use \src\model\input\ContractCallInput;

use \src\common\Tools;
use \src\common\Http;
use \src\common\General;
use \src\common\Constant;
use \src\crypto\key\KeyPair;
use \src\exception\SDKException;

class Contract {
    /**
     * Get the details of contract, include type and payload
     * @param ContractGetInfoRequest $contractGetInfoRequest
     * @return ContractGetInfoResponse
     */
    function getInfo($contractGetInfoRequest) {
        $contractGetInfoResponse = new ContractGetInfoResponse();
        $contractGetInfoResult = new ContractGetInfoResult();
        try{
            if(!($contractGetInfoRequest instanceof ContractGetInfoRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($contractGetInfoRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $contractAddress = $contractGetInfoRequest->getContractAddress();
            $isValid = KeyPair::isAddressValid($contractAddress);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_CONTRACTADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($contractAddress);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $contractGetInfoResponse = Tools::jsonToClass($result, $contractGetInfoResponse);
            $errorCode = $contractGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $contractGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Contract (" . $contractAddress . ") does not exist" : $errorDesc);
            }
            if (Tools::isEmpty($contractGetInfoResponse->result->contract) || Tools::isEmpty($contractGetInfoResponse->result->contract->payload)) {
                throw new SDKException("CONTRACTADDRESS_NOT_CONTRACTACCOUNT_ERROR", null);
            }
        }
        catch(SDKException $e) {
            $contractGetInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $contractGetInfoResult);
        }
        catch (\Exception $e) {
            $contractGetInfoResponse->buildResponse(20000, $e->getMessage(), $contractGetInfoResult);
        }
        return $contractGetInfoResponse;
    }

    /**
     * Get the address of a contract account
     * @param  ContractGetAddressRequest $contractGetAddressRequest
     * @return ContractGetAddressResponse
     */
    function getAddress($contractGetAddressRequest) {
        $contractGetAddressResponse = new ContractGetAddressResponse();
        $contractGetAddressResult = new ContractGetAddressResult();
        try{
            if(!($contractGetAddressRequest instanceof ContractGetAddressRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($contractGetAddressRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $hash = $contractGetAddressRequest->getHash();
            if(Tools::isEmpty($hash) || !is_string($hash) || strlen($hash) != Constant::HASH_HEX_LENGTH) {
                throw new SDKException("INVALID_HASH_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }

            $baseUrl = General::getInstance()->transactionGetInfoUrl($hash);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $transactionGetInfoResponse = new TransactionGetInfoResponse();
            $transactionGetInfoResponse = Tools::jsonToClass($result, $transactionGetInfoResponse);
            $errorCode = $transactionGetInfoResponse->error_code;
            $errorDesc = $transactionGetInfoResponse->error_desc;
            if (4 === $errorCode) {
                throw new SDKException($errorCode, is_null($errorDesc)? "Transaction(" . $hash . ") does not exist" : $errorDesc);
            }
            else if ($errorCode !== 0) {
                throw new SDKException($errorCode, $errorDesc);
            }

            $transactionHistory = $transactionGetInfoResponse->result->transactions[0];
            if (Tools::isEmpty($transactionHistory)) {
                throw new SDKException("INVALID_CONTRACT_HASH_ERROR", null);
            }
            $historyErrorCode = $transactionHistory->error_code;
            $errorDesc = $transactionHistory->error_desc;
            if ($historyErrorCode !== 0) {
                throw new SDKException($errorCode, $errorDesc);
            }
            if (Tools::isEmpty($errorDesc)) {
                throw new SDKException("INVALID_CONTRACT_HASH_ERROR", null);
            }
            $contractAddressArray = Tools::jsonArrayToClassArray($errorDesc, "\\src\model\\response\\result\\data\\ContractAddressInfo");
            if (Tools::isEmpty($contractGetAddressResult)) {
                throw new SDKException("INVALID_CONTRACT_HASH_ERROR", null);
            }
            $contractGetAddressResult->contract_address_infos = $contractAddressArray;
            $contractGetAddressResponse->buildResponse("SUCCESS", null, $contractGetAddressResult);
        }
        catch(SDKException $e) {
            $contractGetAddressResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $contractGetAddressResult);
        }
        catch (\Exception $e) {
            $contractGetAddressResponse->buildResponse(20000, $e->getMessage(), $contractGetAddressResult);
        }
        return $contractGetAddressResponse;
    }

    /**
     * Check the availability of a contract
     * @param  ContractCheckValidRequest $contractCheckValidRequest
     * @return ContractCheckValidResponse
     */
    function checkValid($contractCheckValidRequest) {
        $contractCheckValidResponse = new ContractCheckValidResponse();
        $contractCheckValidResult = new ContractCheckValidResult();
        try{
            if(!($contractCheckValidRequest instanceof ContractCheckValidRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($contractCheckValidRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $contractAddress = $contractCheckValidRequest->getContractAddress();
            $isValid = KeyPair::isAddressValid($contractAddress);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_CONTRACTADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($contractAddress);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $contractGetInfoResponse = Tools::jsonToClass($result, new ContractGetInfoResponse());
            $errorCode = $contractGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $contractGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Contract (" . $contractAddress . ") does not exist" : $errorDesc);
            }
            if ($errorCode !== 0) {
                $errorDesc = $contractGetInfoResponse->error_desc;
                throw new SDKException($errorCode, $errorDesc);
            }
            if (Tools::isEmpty($contractGetInfoResponse->result->contract) || Tools::isEmpty($contractGetInfoResponse->result->contract->payload)) {
                $contractCheckValidResult->is_valid = false;
            }
            else {
                $contractCheckValidResult->is_valid = true;
            }
            $contractCheckValidResponse->buildResponse("SUCCESS", null, $contractCheckValidResult);
        }
        catch(SDKException $e) {
            $contractCheckValidResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $contractCheckValidResult);
        }
        catch (\Exception $e) {
            $contractCheckValidResponse->buildResponse(20000, $e->getMessage(), $contractCheckValidResult);
        }
        return $contractCheckValidResponse;
    }

    /**
     * Call contract for free
     * @param  ContractCallRequest $contractCallRequest
     * @return ContractCallResponse
     */
    function call($contractCallRequest) {
        $contractCallResponse = new ContractCallResponse();
        $contractCallResult = new ContractCallResult();
        try{
            if(!($contractCallRequest instanceof ContractCallRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($contractCallRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $sourceAddress = $contractCallRequest->getSourceAddress();
            $isSourceValid = KeyPair::isAddressValid($sourceAddress);
            if(!Tools::isEmpty($sourceAddress) && Tools::isEmpty($isSourceValid)) {
                throw new SDKException("INVALID_SOURCEADDRESS_ERROR", null);
            }
            $contractAddress = $contractCallRequest->getContractAddress();
            $isContractValid = KeyPair::isAddressValid($contractAddress);
            if(!Tools::isEmpty($contractAddress) && Tools::isEmpty($isContractValid)) {
                throw new SDKException("INVALID_CONTRACTADDRESS_ERROR", null);
            }
            if (!Tools::isEmpty($sourceAddress) && !Tools::isEmpty($contractAddress) && strcmp($contractAddress, $sourceAddress) === 0) {
                throw new SDKException("SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR", null);
            }
            $code = $contractCallRequest->getCode();
            if(Tools::isEmpty($code) && !is_string($code) && Tools::isEmpty($contractAddress)) {
                throw new SDKException("CONTRACTADDRESS_CODE_BOTH_NULL_ERROR", null);
            }
            $feeLimit = $contractCallRequest->getFeeLimit();
            if(Tools::isEmpty($feeLimit) || !is_int($feeLimit) || $feeLimit < Constant::FEE_LIMIT_MIN) {
                throw new SDKException("INVALID_FEELIMIT_ERROR", null);
            }
            $optType = $contractCallRequest->getOptType();
            if(Tools::isNULL($optType) || !is_int($optType) || $optType < Constant::OPT_TYPE_MIN || $optType > Constant::OPT_TYPE_MAX) {
                throw new SDKException("INVALID_OPTTYPE_ERROR", null);
            }
            $contractBalance = $contractCallRequest->getContractBalance();
            if (!Tools::isNULL($contractBalance) && (!is_int($contractBalance) || $contractBalance < 1)) {
                throw new SDKException("INVALID_CONTRACT_BALANCE_ERROR", null);
            }
            $input = $contractCallRequest->getInput();
            if (!Tools::isEmpty($input) && !is_string($input)) {
                throw new SDKException("INPUT_NOT_STRING_ERROR", null);
            }
            $gasPrice = $contractCallRequest->getGasPrice();
            if (!Tools::isNULL($gasPrice) && (!is_int($gasPrice) || $gasPrice < Constant::GAS_PRICE_MIN)) {
                throw new SDKException("INVALID_GASPRICE_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }

            // init input
            $contractCallInput = new ContractCallInput();
            $contractCallInput->opt_type = $optType;
            $contractCallInput->fee_limit = $feeLimit;
            if (!Tools::isEmpty($sourceAddress)) {
                $contractCallInput->source_address = $sourceAddress;
            }
            if (!Tools::isEmpty($contractAddress)) {
                $contractCallInput->contract_address = $contractAddress;
            }
            if (!Tools::isEmpty($code)) {
                $contractCallInput->code = $code;
            }
            if (!Tools::isEmpty($input)) {
                $contractCallInput->input = $input;
            }
            if (!Tools::isNULL($contractBalance)) {
                $contractCallInput->contract_balance = $contractBalance;
            }
            if (!Tools::isNULL($gasPrice)) {
                $contractCallInput->gas_price = $gasPrice;
            }

            $contractGetInfoUrl = General::getInstance()->contractCallUrl();
            $result = Http::post($contractGetInfoUrl, json_encode($contractCallInput));
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $contractCallResponse = Tools::jsonToClass($result, $contractCallResponse);
        }
        catch(SDKException $e) {
            $contractCallResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $contractCallResult);
        }
        catch (\Exception $e) {
            $contractCallResponse->buildResponse(20000, $e->getMessage(), $contractCallResult);
        }
        return $contractCallResponse;
    }
}