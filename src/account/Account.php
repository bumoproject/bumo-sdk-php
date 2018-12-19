<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/2
 * Time: 15:36
 */

namespace src\account;

use src\model\request\AccountCheckValidRequest;
use src\model\request\AccountGetInfoRequest;
use src\model\request\AccountGetNonceRequest;
use src\model\request\AccountGetBalanceRequest;
use src\model\request\AccountGetAssetsRequest;
use src\model\request\AccountGetMetadataRequest;
use src\model\request\AccountCheckActivatedRequest;

use \src\model\response\AccountCheckValidResponse;
use \src\model\response\AccountCreateResponse;
use \src\model\response\AccountGetInfoResponse;
use src\model\response\AccountGetNonceResponse;
use src\model\response\AccountGetBalanceResponse;
use src\model\response\AccountGetAssetsResponse;
use src\model\response\AccountGetMetadataResponse;
use src\model\response\AccountCheckActivatedResponse;

use \src\model\response\result\AccountCheckValidResult;
use \src\model\response\result\AccountCreateResult;
use \src\model\response\result\AccountGetInfoResult;
use \src\model\response\result\AccountGetNonceResult;
use \src\model\response\result\AccountGetBalanceResult;
use \src\model\response\result\AccountGetAssetsResult;
use \src\model\response\result\AccountGetMetadataResult;
use \src\model\response\result\AccountCheckActivatedResult;

use \src\common\General;
use \src\common\Http;
use \src\common\Tools;
use \src\common\Constant;
use \src\crypto\key\KeyPair;
use \src\exception\SDKException;

class Account {
    /**
     * Check the availability of address
     * @param AccountCheckValidRequest $accountCheckValidRequest
     * @return AccountCheckValidResponse
     */
    public function checkValid($accountCheckValidRequest) {
        $accountCheckValidResponse = new AccountCheckValidResponse();
        $accountCheckValidResult = new AccountCheckValidResult();
        try{
            if(!($accountCheckValidRequest instanceof AccountCheckValidRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountCheckValidRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountCheckValidRequest->getAddress();
            $accountCheckValidResult->is_valid = KeyPair::isAddressValid($address);
            $accountCheckValidResponse->buildResponse("SUCCESS", null, $accountCheckValidResult);
        }
        catch(SDKException $e) {
            $accountCheckValidResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountCheckValidResult);
        }
        catch (\Exception $e) {
            $accountCheckValidResponse->buildResponse(20000, $e->getMessage(), $accountCheckValidResult);
        }
        return $accountCheckValidResponse;
    }

    /**
     * Create account info
     * @return AccountCreateResponse, including private key, public key, address
     */
    public function create() {
        $accountCreateResponse = new AccountCreateResponse();
        $accountCreateResult = new AccountCreateResult();
        try{
            $keyPair = new KeyPair();
            $accountCreateResult->private_key = $keyPair->getEncPrivateKey();
            $accountCreateResult->public_key = $keyPair->getEncPublicKey();
            $accountCreateResult->address = $keyPair->getEncAddress();
            $accountCreateResponse->buildResponse("SUCCESS", null, $accountCreateResult);
        }
        catch(\Exception $e){
            $accountCreateResponse->buildResponse(20000, $e->getMessage(), $accountCreateResult);
        }
        return $accountCreateResponse;
    }

    /**
     * Get account info
     * @param AccountGetInfoRequest $accountGetInfoRequest
     * @return AccountGetInfoResponse, including address，balace，nonce and privilege
     */
    public function getInfo($accountGetInfoRequest) {
        $accountGetInfoResponse = new AccountGetInfoResponse();
        $accountGetInfoResult = new AccountGetInfoResult();
        try{
            if(!($accountGetInfoRequest instanceof AccountGetInfoRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountGetInfoRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountGetInfoRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($address);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetInfoResponse = Tools::jsonToClass($result, $accountGetInfoResponse);
            $errorCode = $accountGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $accountGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
        }
        catch(SDKException $e) {
            $accountGetInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountGetInfoResult);
        }
        catch (\Exception $e) {
            $accountGetInfoResponse->buildResponse(20000, $e->getMessage(), $accountGetInfoResult);
        }
        return $accountGetInfoResponse;
    }

    /**
     * Get account nonce
     * @param AccountGetNonceRequest $accountGetNonceRequest
     * @return AccountGetNonceResponse
     */
    public function getNonce($accountGetNonceRequest) {
        $accountGetNonceResponse = new AccountGetNonceResponse();
        $accountGetNonceResult = new AccountGetNonceResult();
        try{
            if(!($accountGetNonceRequest instanceof AccountGetNonceRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountGetNonceRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountGetNonceRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())){
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($address);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetNonceResponse = Tools::jsonToClass($result, $accountGetNonceResponse);
            $errorCode = $accountGetNonceResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $accountGetNonceResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
            if (Tools::isEmpty($errorCode) && Tools::isEmpty($accountGetNonceResponse->result->nonce)) {
                $accountGetNonceResponse->result->nonce = 0;
            }
        }
        catch(SDKException $e) {
            $accountGetNonceResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountGetNonceResult);
        }
        catch (\Exception $e) {
            $accountGetNonceResponse->buildResponse(20000, $e->getMessage(), $accountGetNonceResult);
        }
        return $accountGetNonceResponse;
    }

    /**
     * Get account balance of BU
     * @param AccountGetBalanceRequest $accountGetBalanceRequest
     * @return AccountGetBalanceResponse
     */
    public function getBalance($accountGetBalanceRequest) {
        $accountGetBalanceResponse = new AccountGetBalanceResponse();
        $accountGetBalanceResult = new AccountGetBalanceResult();
        try{
            if(!($accountGetBalanceRequest instanceof AccountGetBalanceRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountGetBalanceRequest)){
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountGetBalanceRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($address);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetBalanceResponse = Tools::jsonToClass($result, $accountGetBalanceResponse);
            $errorCode = $accountGetBalanceResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $accountGetBalanceResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
        }
        catch(SDKException $e) {
            $accountGetBalanceResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountGetBalanceResult);
        }
        catch (\Exception $e) {
            $accountGetBalanceResponse->buildResponse(20000, $e->getMessage(), $accountGetBalanceResult);
        }
        return $accountGetBalanceResponse;
    }

    /**
     * Get all assets of an account
     * @param AccountGetAssetsRequest $accountGetAssetsRequest
     * @return AccountGetAssetsResponse, include code, issuer, amount
     */
    public function getAssets($accountGetAssetsRequest) {
        $accountGetAssetsResponse = new AccountGetAssetsResponse();
        $accountGetAssetsResult = new AccountGetAssetsResult();
        try{
            if(!($accountGetAssetsRequest instanceof AccountGetAssetsRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountGetAssetsRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountGetAssetsRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetAssetsUrl($address);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetAssetsResponse = Tools::jsonToClass($result, $accountGetAssetsResponse);
            $errorCode = $accountGetAssetsResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $accountGetAssetsResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
            if (Tools::isEmpty($errorCode) && Tools::isEmpty($accountGetAssetsResponse->result->assets)) {
                throw new SDKException("NO_ASSET_ERROR", null);
            }
        }
        catch(SDKException $e) {
            $accountGetAssetsResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountGetAssetsResult);
        }
        catch (\Exception $e) {
            $accountGetAssetsResponse->buildResponse(20000, $e->getMessage(), $accountGetAssetsResult);
        }
        return $accountGetAssetsResponse;
    }

    /**
     * Get the metadata of an account
     * @param AccountGetMetadataRequest $accountGetMetadataRequest
     * @return AccountGetMetadataResponse, include key and value
     */
    public function getMetadata($accountGetMetadataRequest) {
        $accountGetMetadataResponse = new AccountGetMetadataResponse();
        $accountGetMetadataResult = new AccountGetMetadataResult();
        try{
            if(!($accountGetMetadataRequest instanceof AccountGetMetadataRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountGetMetadataRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountGetMetadataRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }

            $key = $accountGetMetadataRequest->getKey();
            if (!Tools::isNULL($key) && (!is_string($key) || strlen($key) > Constant::METADATA_KEY_MAX || strlen($key) < 1)) {
                throw new SDKException("INVALID_DATAKEY_ERROR", null);
            }

            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetMetadataUrl($address,$key);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetMetadataResponse = Tools::jsonToClass($result, $accountGetMetadataResponse);
            $errorCode = $accountGetMetadataResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $accountGetMetadataResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
            if (Tools::isEmpty($errorCode) && Tools::isEmpty($accountGetMetadataResponse->result->metadatas)) {
                throw new SDKException("NO_METADATA_ERROR", null);
            }
        }
        catch(SDKException $e) {
            $accountGetMetadataResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountGetMetadataResult);
        }
        catch (\Exception $e) {
            $accountGetMetadataResponse->buildResponse(20000, $e->getMessage(), $accountGetMetadataResult);
        }
        return $accountGetMetadataResponse;
    }

    /**
     * Check the data is or not in bu chain
     * @param AccountCheckActivatedRequest $accountCheckActivatedRequest
     * @return AccountCheckActivatedResponse, true or false
     */
    public function checkActivated($accountCheckActivatedRequest) {
        $accountCheckActivatedResponse = new AccountCheckActivatedResponse;
        $accountCheckActivatedResult = new AccountCheckActivatedResult;
        try{
            if(!($accountCheckActivatedRequest instanceof AccountCheckActivatedRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($accountCheckActivatedRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $accountCheckActivatedRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }
            $baseUrl = General::getInstance()->accountGetInfoUrl($address);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $accountGetInfoResponse = Tools::jsonToClass($result, $accountCheckActivatedResponse);
            $errorCode = $accountGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $accountCheckActivatedResult->is_activated = false;
            }
            else if ($errorCode != 0) {
                $errorDesc = $accountGetInfoResponse->error_desc;
                throw new SDKException($errorCode, $errorDesc);
            }
            else {
                $accountCheckActivatedResult->is_activated = true;
            }
            $accountCheckActivatedResponse->buildResponse("SUCCESS", null, $accountCheckActivatedResult);
        }
        catch(SDKException $e) {
            $accountCheckActivatedResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $accountCheckActivatedResult);
        }
        catch (\Exception $e) {
            $accountCheckActivatedResponse->buildResponse(20000, $e->getMessage(), $accountCheckActivatedResult);
        }
        return $accountCheckActivatedResponse;
    }
}
?>