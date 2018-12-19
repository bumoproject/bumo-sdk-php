<?php
/**
 * User: riven
 * Date: 2018/11/2
 * Time: 17:44
 */

namespace src\token;
use src\common\Constant;
use \src\model\request\AssetGetInfoRequest;
use \src\model\response\AssetGetInfoResponse;
use \src\model\response\result\AssetGetInfoResult;
use \src\common\Tools;
use \src\common\Http;
use \src\common\General;
use \src\exception\SDKException;
use \src\crypto\key\KeyPair;

class Asset {
    /**
     * Get details of the specified asset
     * @param  AssetGetInfoRequest $assetGetInfoRequest
     * @return AssetGetInfoResponse
     */
    function getInfo($assetGetInfoRequest) {
        $assetGetInfoResponse = new AssetGetInfoResponse();
        $assetGetInfoResult = new AssetGetInfoResult();
        try{
            if(!($assetGetInfoRequest instanceof AssetGetInfoRequest)){
                throw new SDKException("INVALID_REQUEST_ERROR", null);
            }
            if(Tools::isEmpty($assetGetInfoRequest)) {
                throw new SDKException("REQUEST_NULL_ERROR", null);
            }
            $address = $assetGetInfoRequest->getAddress();
            $isValid = KeyPair::isAddressValid($address);
            if(Tools::isEmpty($isValid)) {
                throw new SDKException("INVALID_ADDRESS_ERROR", null);
            }
            $code = $assetGetInfoRequest->getCode();
            if(Tools::isEmpty($code) || !is_string($code) || strlen($code) > Constant::ASSET_CODE_MAX) {
                throw new SDKException("INVALID_ASSET_CODE_ERROR", null);
            }
            $issuer = $assetGetInfoRequest->getIssuer();
            $isIssuerValid = KeyPair::isAddressValid($issuer);
            if(Tools::isEmpty($isIssuerValid)) {
                throw new SDKException("INVALID_ISSUER_ADDRESS_ERROR", null);
            }
            if(Tools::isEmpty(General::getInstance()->getUrl())) {
                throw new SDKException("URL_EMPTY_ERROR", null);
            }

            $baseUrl = General::getInstance()->assetGetUrl($address, $code, $issuer);
            $result = Http::get($baseUrl);
            if (Tools::isEmpty($result)) {
                throw new SDKException("CONNECTNETWORK_ERROR", null);
            }
            $assetGetInfoResponse = Tools::jsonToClass($result, $assetGetInfoResponse);
            $errorCode = $assetGetInfoResponse->error_code;
            if (4 == $errorCode) {
                $errorDesc = $assetGetInfoResponse->error_desc;
                throw new SDKException($errorCode, is_null($errorDesc)? "Account(" . $address . ") does not exist" : $errorDesc);
            }
            if (Tools::isEmpty($errorCode) && Tools::isEmpty($assetGetInfoResponse->result->assets)) {
                throw new SDKException("NO_ASSET_ERROR", null);
            }
        }
        catch(SDKException $e) {
            $assetGetInfoResponse->buildResponse($e->getErrorCode(), $e->getErrorDesc(), $assetGetInfoResult);
        }
        catch (\Exception $e) {
            $assetGetInfoResponse->buildResponse(20000, $e->getMessage(), $assetGetInfoResult);
        }
        return $assetGetInfoResponse;
    }
}