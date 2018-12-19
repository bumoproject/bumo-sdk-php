<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/5
 * Time: 20:28
 */
namespace src\blockchain;

use \src\model\request\BlockGetInfoRequest;
use src\model\request\BlockGetTransactionsRequest;
use \src\model\request\BlockGetValidatorsRequest;
use \src\model\request\BlockGetRewardRequest;
use \src\model\request\BlockGetFeesRequest;

use \src\model\response\BlockGetNumberResponse;
use \src\model\response\BlockCheckStatusLedgerSeqResponse;
use \src\model\response\BlockCheckStatusResponse;
use \src\model\response\BlockGetInfoResponse;
use \src\model\response\BlockGetLatestInfoResponse;
use src\model\response\BlockGetTransactionsResponse;
use \src\model\response\BlockGetValidatorsResponse;
use \src\model\response\BlockGetLatestValidatorsResponse;
use \src\model\response\BlockGetRewardResponse;
use \src\model\response\BlockGetRewardJsonResponse;
use \src\model\response\BlockGetLatestRewardResponse;
use \src\model\response\BlockGetFeesResponse;
use \src\model\response\BlockGetLatestFeesResponse;

use \src\model\response\result\BlockGetNumberResult;
use \src\model\response\result\BlockCheckStatusResult;
use \src\model\response\result\BlockGetInfoResult;
use \src\model\response\result\BlockGetLatestInfoResult;
use src\model\response\result\BlockGetTransactionsResult;
use \src\model\response\result\BlockGetValidatorsResult;
use \src\model\response\result\BlockGetLatestValidatorsResult;
use \src\model\response\result\BlockGetRewardResult;
use \src\model\response\result\BlockGetLatestRewardResult;
use \src\model\response\result\BlockGetFeesResult;
use \src\model\response\result\BlockGetLatestFeesResult;

use \src\model\response\result\data\ValidatorRewardInfo;

use \src\common\General;
use \src\common\Http;
use \src\common\Tools;
use \src\exception\SDKException;


class Block {
    /**
     * Get the latest block number
     * @return BlockGetNumberResponse
     */
    function getNumber() {
        $blockGetNumberResponse = new BlockGetNumberResponse();
        $blockGetNumberResult = new BlockGetNumberResult();
        try{
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetNumberUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetNumberResponse = Tools::jsonToClass($result, $blockGetNumberResponse);
        }
        catch(SDKException $e) {
            $blockGetNumberResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetNumberResult);
        }
        catch (\Exception $e) {
            $blockGetNumberResponse->buildResponse(20000, $e->getMessage(), $blockGetNumberResult);
        }
        return $blockGetNumberResponse;
    }

    /**
     * Check the status of block synchronization
     * @return BlockCheckStatusResponse
     */
    public function checkStatus() {
        $blockCheckStatusResponse = new BlockCheckStatusResponse();
        $blockCheckStatusResult = new BlockCheckStatusResult();
        try{
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockCheckStatusUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockCheckStatusLedgerResponse = Tools::jsonToClass($result, new BlockCheckStatusLedgerSeqResponse());
            $ledger_sequence = $blockCheckStatusLedgerResponse->ledger_manager->ledger_sequence;
            $chain_max_ledger_seq = $blockCheckStatusLedgerResponse->ledger_manager->chain_max_ledger_seq;
            if(!is_string($ledger_sequence)) {
                $ledger_sequence = number_format($ledger_sequence);
            }
            if (!is_string($chain_max_ledger_seq)) {
                $chain_max_ledger_seq = number_format($chain_max_ledger_seq);
            }
            if (bccomp($ledger_sequence, $chain_max_ledger_seq) < 0) {
                $blockCheckStatusResult->is_synchronous = false;
            }
            else {
                $blockCheckStatusResult->is_synchronous = true;
            }
            $blockCheckStatusResponse->buildResponse("SUCCESS", null, $blockCheckStatusResult);
        }
        catch(SDKException $e) {
            $blockCheckStatusResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockCheckStatusResult);
        }
        catch (\Exception $e) {
            $blockCheckStatusResponse->buildResponse(20000, $e->getMessage(), $blockCheckStatusResult);
        }
        return $blockCheckStatusResponse;
    }

    /**
     * Get the transactions of specific block
     * @param BlockGetTransactionsRequest $blockGetTransactionsRequest
     * @return BlockGetTransactionsResponse
     */
    function getTransactions($blockGetTransactionsRequest) {
        $blockGetTransactionsResponse = new BlockGetTransactionsResponse();
        $blockGetTransactionsResult = new BlockGetTransactionsResult();
        try {
            if(!($blockGetTransactionsRequest instanceof BlockGetTransactionsRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($blockGetTransactionsRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blockNumber = $blockGetTransactionsRequest->getBlockNumber();
            if (Tools::isEmpty($blockNumber) || !is_int($blockNumber) || $blockNumber < 1) {
                throw new SDKException("INVALID_BLOCKNUMBER_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetTransactionsUrl($blockNumber);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetInfoResponse = Tools::jsonToClass($result, $blockGetTransactionsResponse);
            $errorCode = $blockGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $blockGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Block(" . $blockNumber . ") does not exist" : $errorDesc);
            }
        } catch(SDKException $e) {
            $blockGetTransactionsResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetTransactionsResult);
        }
        catch (\Exception $e) {
            $blockGetTransactionsResponse->buildResponse(20000, $e->getMessage(), $blockGetTransactionsResult);
        }
        return $blockGetTransactionsResponse;
    }

    /**
     * Get the information of specific block
     * @param BlockGetInfoRequest $blockGetInfoRequest
     * @return BlockGetInfoResponse
     */
    function getInfo($blockGetInfoRequest) {
        $blockGetInfoResponse = new BlockGetInfoResponse();
        $blockGetInfoResult = new BlockGetInfoResult();
        try {
            if(!($blockGetInfoRequest instanceof BlockGetInfoRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($blockGetInfoRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blockNumber = $blockGetInfoRequest->getBlockNumber();
            if (Tools::isEmpty($blockNumber) || !is_int($blockNumber) || $blockNumber < 1) {
                throw new SDKException("INVALID_BLOCKNUMBER_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetInfoUrl($blockNumber);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetInfoResponse = Tools::jsonToClass($result, $blockGetInfoResponse);
            $errorCode = $blockGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $blockGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Block(" . $blockNumber . ") does not exist" : $errorDesc);
            }
        }
        catch(SDKException $e) {
            $blockGetInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetInfoResult);
        }
        catch (\Exception $e) {
            $blockGetInfoResponse->buildResponse(20000, $e->getMessage(), $blockGetInfoResult);
        }
        return $blockGetInfoResponse;
    }

    /**
     * Get the latest information of block
     * @return BlockGetLatestInfoResponse
     */
    function getLatestInfo() {
        $blockGetLatestInfoResponse = new BlockGetLatestInfoResponse();
        $blockGetLatestInfoResult = new BlockGetLatestInfoResult();
        try {
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetLatestInfoUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetLatestInfoResponse = Tools::jsonToClass($result, $blockGetLatestInfoResponse);
        }
        catch(SDKException $e) {
            $blockGetLatestInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetLatestInfoResult);
        }
        catch (\Exception $e) {
            $blockGetLatestInfoResponse->buildResponse(20000, $e->getMessage(), $blockGetLatestInfoResult);
        }
        return $blockGetLatestInfoResponse;
    }

    /**
     * Get the validators of specific block
     * @param  BlockGetValidatorsRequest $blockGetValidatorsRequest
     * @return BlockGetValidatorsResponse
     */
    function getValidators($blockGetValidatorsRequest){
        $blockGetValidatorsResponse = new BlockGetValidatorsResponse();
        $blockGetValidatorsResult = new BlockGetValidatorsResult();
        try {
            if(!($blockGetValidatorsRequest instanceof BlockGetValidatorsRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($blockGetValidatorsRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blockNumber = $blockGetValidatorsRequest->getBlockNumber();
            if (Tools::isEmpty($blockNumber) || !is_int($blockNumber) || $blockNumber < 1) {
                throw new SDKException("INVALID_BLOCKNUMBER_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetValidatorsUrl($blockNumber);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetValidatorsResponse = Tools::jsonToClass($result, $blockGetValidatorsResponse);
            $errorCode = $blockGetValidatorsResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $blockGetValidatorsResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Block(" . $blockNumber . ") does not exist" : $errorDesc);
            }
        }
        catch(SDKException $e) {
            $blockGetValidatorsResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetValidatorsResult);
        }
        catch (\Exception $e) {
            $blockGetValidatorsResponse->buildResponse(20000, $e->getMessage(), $blockGetValidatorsResult);
        }
        return $blockGetValidatorsResponse;
    }

    /**
     * Get the latest validators of block
     * @return BlockGetLatestValidatorsResponse
     */
    function getLatestValidators() {
        $blockGetLatestValidatorsResponse = new BlockGetLatestValidatorsResponse();
        $blockGetLatestValidatorsResult = new BlockGetLatestValidatorsResult();
        try{
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetLatestValidatorsUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetLatestValidatorsResponse = Tools::jsonToClass($result, $blockGetLatestValidatorsResponse);
        }
        catch(SDKException $e) {
            $blockGetLatestValidatorsResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetLatestValidatorsResult);
        }
        catch (\Exception $e) {
            $blockGetLatestValidatorsResponse->buildResponse(20000, $e->getMessage(), $blockGetLatestValidatorsResult);
        }
        return $blockGetLatestValidatorsResponse;
    }

    /**
     * Get the reward of specific block
     * @param  BlockGetRewardRequest $blockGetRewardRequest
     * @return BlockGetRewardResponse
     */
    function GetReward($blockGetRewardRequest){
        $blockGetRewardResponse = new BlockGetRewardResponse();
        $blockGetRewardResult = new BlockGetRewardResult();
        try {
            if(!($blockGetRewardRequest instanceof BlockGetRewardRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if (Tools::isEmpty($blockGetRewardRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blockNumber = $blockGetRewardRequest->getBlockNumber();
            if (Tools::isEmpty($blockNumber) || !is_int($blockNumber) || $blockNumber < 1) {
                throw new SDKException("INVALID_BLOCKNUMBER_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetRewardUrl($blockNumber);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetRewardJsonResponse = Tools::jsonToClass($result, new BlockGetRewardJsonResponse());
            $errorCode = $blockGetRewardJsonResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $blockGetRewardJsonResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Block(" . $blockNumber . ") does not exist" : $errorDesc);
            }
            if ($errorCode != 0) {
                $errorDesc = $blockGetRewardJsonResponse->error_desc;
                throw new SDKException($errorCode, $errorDesc);
            }
            $validator_reward = (array)$blockGetRewardJsonResponse->result->validators_reward;
            $blockGetRewardResult->block_reward = $blockGetRewardJsonResponse->result->block_reward;
            $validators = array_keys($validator_reward);
            $validatorRewardInfoArray = array();
            for ($i = 0; $i < count($validators); $i++) {
                $validatorRewardInfo = new ValidatorRewardInfo();
                $validatorRewardInfo->validator = $validators[$i];
                $validatorRewardInfo->reward = $validator_reward[$validators[$i]];
                $validatorRewardInfoArray[$i] = $validatorRewardInfo;
            }
            $blockGetRewardResult->validators_reward = $validatorRewardInfoArray;
            $blockGetRewardResponse->buildResponse("SUCCESS", null, $blockGetRewardResult);
        }
        catch(SDKException $e) {
            $blockGetRewardResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetRewardResult);
        }
        catch (\Exception $e) {
            $blockGetRewardResponse->buildResponse(20000, $e->getMessage(), $blockGetRewardResult);
        }
        return $blockGetRewardResponse;
    }

    /**
     * Get the latest reward of block
     * @return BlockGetLatestRewardResponse
     */
    function GetLatestReward() {
        $blockGetLatestRewardResponse = new BlockGetLatestRewardResponse();
        $blockGetLatestRewardResult = new BlockGetLatestRewardResult();
        try{
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetLatestRewardUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetRewardJsonResponse = Tools::jsonToClass($result, new BlockGetRewardJsonResponse());
            $errorCode = $blockGetRewardJsonResponse->error_code;
            if ($errorCode != 0) {
                $errorDesc = $blockGetRewardJsonResponse->error_desc;
                throw new SDKException($errorCode, $errorDesc);
            }
            $validator_reward = (array)$blockGetRewardJsonResponse->result->validators_reward;
            $blockGetLatestRewardResult->block_reward = $blockGetRewardJsonResponse->result->block_reward;
            $validators = array_keys($validator_reward);
            $validatorRewardInfoArray = array();
            for ($i = 0; $i < count($validators); $i++) {
                $validatorRewardInfo = new ValidatorRewardInfo();
                $validatorRewardInfo->validator = $validators[$i];
                $validatorRewardInfo->reward = $validator_reward[$validators[$i]];
                $validatorRewardInfoArray[$i] = $validatorRewardInfo;
            }
            $blockGetLatestRewardResult->validators_reward = $validatorRewardInfoArray;
            $blockGetLatestRewardResponse->buildResponse("SUCCESS", null, $blockGetLatestRewardResult);
        }
        catch(SDKException $e) {
            $blockGetLatestRewardResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetLatestRewardResult);
        }
        catch (\Exception $e) {
            $blockGetLatestRewardResponse->buildResponse(20000, $e->getMessage(), $blockGetLatestRewardResult);
        }
        return $blockGetLatestRewardResponse;
    }

    /**
     * Get the fees of specific block
     * @param  BlockGetFeesRequest $blockGetFeesRequest
     * @return BlockGetFeesResponse
     */
    function getFees($blockGetFeesRequest){
        $blockGetFeesResponse = new BlockGetFeesResponse();
        $blockGetFeesResult = new BlockGetFeesResult();
        try{
            if(!($blockGetFeesRequest instanceof BlockGetFeesRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($blockGetFeesRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $blockNumber = $blockGetFeesRequest->getBlockNumber();
            if (Tools::isEmpty($blockNumber) || !is_int($blockNumber) || $blockNumber < 1) {
                throw new SDKException("INVALID_BLOCKNUMBER_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetFeesUrl($blockNumber);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetFeesResponse = Tools::jsonToClass($result, $blockGetFeesResponse);
            $errorCode = $blockGetFeesResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $blockGetFeesResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Block(" . $blockNumber . ") does not exist" : $errorDesc);
            }
        }
        catch(SDKException $e) {
            $blockGetFeesResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetFeesResult);
        }
        catch (\Exception $e) {
            $blockGetFeesResponse->buildResponse(20000, $e->getMessage(), $blockGetFeesResult);
        }
        return $blockGetFeesResponse;
    }

    /**
     * Get the latest fees of block
     * @return BlockGetLatestFeesResponse
     */
    function getLatestFees() {
        $blockGetLatestFeesResponse = new BlockGetLatestFeesResponse();
        $blockGetLatestFeesResult = new BlockGetLatestFeesResult();
        try{
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->blockGetLatestFeeUrl();
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $blockGetLatestFeesResponse = Tools::jsonToClass($result, $blockGetLatestFeesResponse);
        }
        catch(SDKException $e) {
            $blockGetLatestFeesResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $blockGetLatestFeesResult);
        }
        catch (\Exception $e) {
            $blockGetLatestFeesResponse->buildResponse(20000, $e->getMessage(), $blockGetLatestFeesResult);
        }
        return $blockGetLatestFeesResponse;
    }
}