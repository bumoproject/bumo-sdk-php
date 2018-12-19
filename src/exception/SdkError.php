<?php
namespace src\exception;

/**
*  
*/
class SdkError {
    public static $errorDescArray=
        array(
       "SUCCESS"=>array(0 => ""),
       "ACCOUNT_CREATE_ERROR"=>array(11001 => "Failed to create the account"),
       "INVALID_SOURCEADDRESS_ERROR"=>array(11002 => "Invalid sourceAddress"),
       "INVALID_DESTADDRESS_ERROR"=>array(11003 => "Invalid destAddress"),
       "INVALID_INITBALANCE_ERROR"=>array(11004 => "InitBalance must be between 1 and max(int64)"),
       "SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR"=>array(11005 => "SourceAddress cannot be equal to destAddress"),
       "INVALID_ADDRESS_ERROR"=>array(11006 => "Invalid address"),
       "CONNECTNETWORK_ERROR"=>array(11007 => "Fail to connect network"),
       "INVALID_ISSUE_AMOUNT_ERROR"=>array(11008 => "Amount of the token to be issued must be between 1 and Long.MAX_VALUE"),
       "NO_ASSET_ERROR"=>array(11009 => "The account does not have this token"),
       "NO_METADATA_ERROR"=>array(11010 => "The account does not have this metadata"),
       "INVALID_DATAKEY_ERROR"=>array(11011 => "The length of key must be between 1 and 1024"),
       "INVALID_DATAVALUE_ERROR"=>array(11012 => "The length of value must be between 0 and 256000"),
       "INVALID_DATAVERSION_ERROR"=>array(11013 => "The version must be equal to or bigger than 0"),
       "INVALID_MASTERWEIGHT_ERROR"=>array(11015 => "MasterWeight must be between 0 and max(uint32)"),
       "INVALID_SIGNER_ADDRESS_ERROR"=>array(11016 => "Invalid signer address"),
       "INVALID_SIGNER_WEIGHT_ERROR"=>array(11017 => "Signer weight must be between 0 and max(uint32)"),
       "INVALID_TX_THRESHOLD_ERROR"=>array(11018 => "TxThreshold must be between 0 and max(int64)"),
       "INVALID_TYPETHRESHOLD_TYPE_ERROR"=>array(11019 => "Type of TypeThreshold is invalid"),
       "INVALID_TYPE_THRESHOLD_ERROR"=>array(11020 => "TypeThreshold must be between 0 and max(int64)"),
       "INVALID_ASSET_CODE_ERROR"=>array(11023 => "The length of code must be between 1 and 64"),
       "INVALID_ASSET_AMOUNT_ERROR"=>array(11024 => "AssetAmount must be between 0 and max(int64)"),
       "INVALID_CONTRACT_HASH_ERROR"=>array(11025 => "Invalid transaction hash to create contract"),
       "INVALID_BU_AMOUNT_ERROR"=>array(11026 => "BuAmount must be between 0 and max(int64)"),
       "INVALID_ISSUER_ADDRESS_ERROR"=>array(11027 => "Invalid issuer address"),
       "INVALID_CONTRACT_BALANCE_ERROR"=>array(11028 => "The contractBalance must be between 1 and max(int64)"),
       "NO_SUCH_TOKEN_ERROR"=>array(11030 => "No such token"),
       "INVALID_TOKEN_NAME_ERROR"=>array(11031 => "The length of token name must be between 1 and 1024"),
       "INVALID_TOKEN_SIMBOL_ERROR"=>array(11032 => "The length of symbol must be between 1 and 1024"),
       "INVALID_TOKEN_DECIMALS_ERROR"=>array(11033 => "Decimals must be between 0 and 8"),
       "INVALID_TOKEN_TOTALSUPPLY_ERROR"=>array(11034 => "TotalSupply must be between 1 and max(int64)"),
       "INVALID_TOKENOWNER_ERROR"=>array(11035 => "Invalid token owner"),
       "INVALID_TOKEN_SUPPLY_ERROR"=>array(11036 => "Supply * decimals must be between 1 and max(int64)"),
       "INVALID_CONTRACTADDRESS_ERROR"=>array(11037 => "Invalid contract address"),
       "CONTRACTADDRESS_NOT_CONTRACTACCOUNT_ERROR"=>array(11038 => "ContractAddress is not a contract account"),
       "INVALID_TOKEN_AMOUNT_ERROR"=>array(11039 => "TokenAmount must be between 1 and max(int64)"),
       "SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR"=>array(11040 => "SourceAddress cannot be equal to contractAddress"),
       "INVALID_FROMADDRESS_ERROR"=>array(11041 => "Invalid fromAddress"),
       "FROMADDRESS_EQUAL_DESTADDRESS_ERROR"=>array(11042 => "FromAddress cannot be equal to destAddress"),
       "INVALID_SPENDER_ERROR"=>array(11043 => "Invalid spender"),
       "PAYLOAD_EMPTY_ERROR"=>array(11044 => "Payload cannot be empty"),
       "INVALID_LOG_TOPIC_ERROR"=>array(11045 => "The length of a log topic must be between 1 and 128"),
       "INVALID_LOG_DATA_ERROR"=>array(11046 => "The length of one piece of log data must be between 1 and 1024"),
       "INVALID_CONTRACT_TYPE_ERROR"=>array(11047 => "Invalid contract type"),
       "INVALID_NONCE_ERROR"=>array(11048 => "Nonce must be between 1 and max(int64)"),
       "INVALID_GASPRICE_ERROR"=>array(11049 => "GasPrice must be between 1000 and max(int64)"),
       "INVALID_FEELIMIT_ERROR"=>array(11050 => "FeeLimit must be between 1 and max(int64)"),
       "OPERATIONS_EMPTY_ERROR"=>array(11051 => "Operations cannot be empty"),
       "INVALID_CEILLEDGERSEQ_ERROR"=>array(11052 => "CeilLedgerSeq must be equal to or bigger than 0"),
       "OPERATIONS_ONE_ERROR"=>array(11053 => "One of operations cannot be resolved"),
       "INVALID_SIGNATURENUMBER_ERROR"=>array(11054 => "SignatureNumber must be between 1 and max(int32)"),
       "INVALID_HASH_ERROR"=>array(11055 => "Invalid transaction hash"),
       "INVALID_BLOB_ERROR"=>array(11056 => "Invalid blob"),
       "PRIVATEKEY_NULL_ERROR"=>array(11057 => "PrivateKeys cannot be empty"),
       "PRIVATEKEY_ONE_ERROR"=>array(11058 => "One of privateKeys is invalid"),
       "INVALID_BLOCKNUMBER_ERROR"=>array(11060 => "BlockNumber must be bigger than 0"),
       "URL_EMPTY_ERROR"=>array(11062 => "Url cannot be empty"),
       "CONTRACTADDRESS_CODE_BOTH_NULL_ERROR"=>array(11063 => "ContractAddress and code cannot be empty at the same time"),
       "INVALID_OPTTYPE_ERROR"=>array(11064 => "OptType must be between 0 and 2"),
       "GET_ALLOWANCE_ERROR"=>array(11065 => "Failed to get allowance"),
       "GET_TOKEN_INFO_ERROR"=>array(11066 => "Failed to get token info"),
       "SIGNATURE_EMPTY_ERROR"=>array(11067 => "The signatures cannot be empty"),
       "INVALID_ISSUE_TYPE_ERROR"=>array(11068 => "Invalid issuing type"),
       "INVALID_TOKEN_CODE_ERROR"=>array(11069 => "The length of token code must be between 1 and 64"),
       "INVALID_TOKEN_DESCRIPTION_ERROR"=>array(11070 => "The length of description must be between 1 and 1024"),
       "INVALID_LIMITED_TOKEN_NOW_SUPPLY_ERROR"=>array(11071 => "The nowSupply must be between 1 and supply in the limited issuance"),
       "INVALID_ONE_OFF_NOWSUPPLY_NOT_EQUAL_SUPPLY_ERROR"=>array(11072 => "In the one-off issuance, the nowSupply must be equal to supply"),
       "INVALID_TOKEN_APPEND_SUPPLY_ERROR"=>array(11073 => "The appendSupply must be between 1 and max(int64)"),
       "INVALID_UNLIMITED_TOKEN_NOW_SUPPLY_ERROR"=>array(11074 => "The nowSupply must be between 1 and Long.MAX_VALUE in the unlimited issuance"),
       "INVALID_ATP10TOKEN_HASH_ERROR"=>array(11075 => "Invalid transaction hash to issue atp1.0 token"),
       "TOKEN_NOT_FOUND_ERROR"=>array(11076 => "The token is not found"),
       "CONNECTN_BLOCKCHAIN_ERROR"=>array(19999 => "Failed to connect blockchain"),
       "SYSTEM_ERROR"=>array(20000 => "System error"),
       "REQUEST_NULL_ERROR"=>array(12001 => "Request parameter cannot be null"),
       "METADATA_NOT_STRING_ERROR"=>array(17001 => "Metadata must be a string"),
       "INPUT_NOT_STRING_ERROR"=>array(17002 => "Input must be a string"),
       "INIT_INPUT_NOT_STRING_ERROR"=>array(17003 => "InitInput must be a string"),
       "INVALID_REQUEST_ERROR"=>array(17004=>"Request is invalid"),
       "INVALID_DELETE_FLAG_ERROR"=>array(17005=>"The deleteFlag is invalid"),
       "SIGNERS_NOT_ARRAY_ERROR"=> array(17006=>"The signers should be an array"),
       "INVALID_SIGNER_ERROR"=>array(17007=>"The signer is invalid"),
       "TYPE_THRESHOLDS_NOT_ARRAY_ERROR"=>array(17008=>"The typeThresholds should be an array"),
        );

    /**
     * @return mixed
     */
    public static function getCode($error) {
        $keys = array_keys(SdkError::$errorDescArray[$error]);
        return $keys[0];
    }

    /**
     * @return mixed
     */
    public static function getDescription($error) {
        $values = array_values(SdkError::$errorDescArray[$error]);
        return $values[0];
    }

    /**
     * [checkErrorCode description]
     * @param  [type] $baseResponse [description]
     * @return [type]               [description]
     */
    public static function checkErrorCode($baseResponse) {
        $errCode = $baseResponse->getErrorCode();
        try {
            if(!$errCode) {
                throw new \Exception("errCode is error", 1);
            }
        }
        catch(\Exception $e) {
            echo $e->getMessage(); exit;
        }
        $errorDesc = $baseResponse->getErrorDesc();
        try {
            if(is_null($errorDesc)) {
                throw new \Exception("errorDesc is error", 1);
            }
        }
        catch(\Exception $e){
            echo $e->getMessage();exit;
        }
    }
}
?>