<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/9
 * Time: 10:19
 */

require_once dirname(dirname(dirname(__FILE__))) . "/vendor/autoload.php";
include_once dirname(dirname(dirname(__FILE__))) . "/autoload.php";

//$sdk = \src\SDK::getInstance("http://seed1.bumotest.io:26002");
$sdk = \src\SDK::getInstance("http://127.0.0.1:36002"); // localhost

class ATP10TokenDemo extends PHPUnit_Framework_TestCase {
    public function setSDKConfigure($chainId) {
        $sdkConfigure = new \src\model\request\SDKConfigure();
        $sdkConfigure->setChainId($chainId);
        $sdkConfigure->setUrl("http://127.0.0.1:36002");
        $GLOBALS['sdk'] = \src\SDK::getInstanceWithConfigure($sdkConfigure);
    }
    /** @test */
    public function test() {
        $blob = "0a246275516a52734b46723748664e7242545757675134346655664151354e77675668614274100118c0f1ced11220e8073a3c08021a2b746573742074686520756e6c696d697465642069737375616e6365206f6620617074312e3020746f6b656e2a0b0a03545854108094ebdc033ad30108041a2b746573742074686520756e6c696d697465642069737375616e6365206f6620617074312e3020746f6b656e3aa1010a1261737365745f70726f70657274795f545854128a017b226e616d65223a22545854222c22636f6465223a22545854222c226465736372697074696f6e223a227465737420756e6c696d697465642069737375616e6365206f6620617074312e3020746f6b656e222c22646563696d616c73223a382c22746f74616c537570706c79223a302c2269636f6e223a22222c2276657273696f6e223a22312e30227d400a";
        $request = new \src\model\request\TransactionParseBlobRequest();
        $request->setBlob($blob);
        $response = $GLOBALS['sdk']->getTransactionService()->parseBlob($request);
        echo json_encode($response);
    }

    /**
     * Issue the unlimited apt1.0 token successfully
     * Unlimited requirement: The totalSupply must be equal to 0
     */
    /** @test */
    public function issueUnlimitedAtp10Token() {
        $this->setSDKConfigure(0);

        // Init variable
        // The account private key to issue atp1.0 token
        $issuerPrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        // The token name
        $name = "TXT";
        // The token code
        $code = "TXT";
        // The apt token version
        $version = "1.0";
        // The apt token icon
        $icon = "";
        // The token decimals
        $decimals = 8;
        // The token total supply number
        $totalSupply = 0;
        // The token now supply number, which includes the dicimals.
        // If decimals is 8 and you want to issue 10 tokens now, the nowSupply must be 10 * 10 ^ 8, like below.
        $nowSupply = \src\common\Tools::unitWithDecimals("10", 8);
        // The token description
        $description = "test unlimited issuance of apt1.0 token";
        // The operation note
        $operationMetadata = "test the unlimited issuance of apt1.0 token";
        // The transaction note
        $transMetadata = "test the unlimited issuance of apt1.0 token";
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        // Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("50.03");


        // 1. Get the account address to send this transaction
        $issuerAddresss = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($issuerPrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($issuerAddresss) + 1;

        // 2. Build issueAsset
        $issueAsset = new \src\model\request\operation\AssetIssueOperation();
        $issueAsset->setSourceAddress($issuerAddresss);
        $issueAsset->setCode($code);
        $issueAsset->setAmount($nowSupply);
        $issueAsset->setMetadata($operationMetadata);

        // 3. If this is an atp 1.0 token, you must set metadata like this
        $apt10Json = array(
            "name" => $name,
            "code" => $code,
            "description" => $description,
            "decimals" => $decimals,
            "totalSupply" => $totalSupply,
            "icon" => $icon,
            "version" => $version,
        );

        $key = "asset_property_" . $code;
        $value = json_encode($apt10Json);

        // 4. Build setMetadata
        $setMetadata = new \src\model\request\operation\AccountSetMetadataOperation();
        $setMetadata->setSourceAddress($issuerAddresss);
        $setMetadata->setKey($key);
        $setMetadata->setValue($value);
        $setMetadata->setMetadata($operationMetadata);

        $operations = array();
        array_push($operations, $issueAsset);
        array_push($operations, $setMetadata);

        $privateKeys = array();
        array_push($privateKeys, $issuerPrivateKey);

        $hash = ATP10TokenDemo::buildBlobAndSignAndSubmit($privateKeys, $issuerAddresss, $nonce, $gasPrice, $feeLimit, $transMetadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }

    /**
     * Issue the limited apt1.0 token successfully
     * Limited requirement: The totalSupply must be bigger than 0
     */
    /** @test */
    public function issueLimitedAtp10Token() {
        $this->setSDKConfigure(0);

        // Init variable
        // The account private key to issue atp1.0 token
        $issuerPrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        // The token name
        $name = "TXT";
        // The token code
        $code = "TXT";
        // The apt token version
        $version = "1.0";
        // The apt token icon
        $icon = "";
        // The token decimals
        $decimals = 1;
        // The token total supply number, which includes the decimals.
        // If decimals is 1 and you plan to issue 1000 tokens, the totalSupply must be 1000 * 10 ^ 1, like below.
        $totalSupply = \src\common\Tools::unitWithDecimals("1000", 1);
        // The token now supply number, which includes the dicimals.
        // If decimals is 8 and you want to issue 10 tokens now, the nowSupply must be 10 * 10 ^ 1, like below.
        $nowSupply = \src\common\Tools::unitWithDecimals("10", 1);
        // The token description
        $description = "test unlimited issuance of apt1.0 token";
        // The operation note
        $operationMetadata = "test the unlimited issuance of apt1.0 token";
        // The transaction note
        $transMetadata = "test the unlimited issuance of apt1.0 token";
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        // Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("50.03");


        // 1. Get the account address to send this transaction
        $issuerAddresss = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($issuerPrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($issuerAddresss) + 1;

        // 2. Build issueAsset
        $issueAsset = new \src\model\request\operation\AssetIssueOperation();
        $issueAsset->setCode($code);
        $issueAsset->setAmount($nowSupply);
        $issueAsset->setMetadata($operationMetadata);

        // 3. If this is an atp 1.0 token, you must set metadata like this
        $apt10Json = array(
            "name" => $name,
            "code" => $code,
            "description" => $description,
            "decimals" => $decimals,
            "totalSupply" => $totalSupply,
            "icon" => $icon,
            "version" => $version,
        );

        $key = "asset_property_" . $code;
        $value = json_encode($apt10Json);

        // 4. Build setMetadata
        $setMetadata = new \src\model\request\operation\AccountSetMetadataOperation();
        $setMetadata->setKey($key);
        $setMetadata->setValue($value);
        $setMetadata->setMetadata($operationMetadata);

        $operations = array();
        array_push($operations, $issueAsset);
        array_push($operations, $setMetadata);

        $privateKeys = array();
        array_push($privateKeys, $issuerPrivateKey);

        $hash = ATP10TokenDemo::buildBlobAndSignAndSubmit($privateKeys, $issuerAddresss, $nonce, $gasPrice, $feeLimit, $transMetadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }

    /**
     * Send apt 1.0 token to other account
     */
    /** @test */
    public function sendAtp10Token() {
        $this->setSDKConfigure(0);

        // The account private key to send asset
        $senderPrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        // The account to receive asset
        $destAddress = "buQhapCK83xPPdjQeDuBLJtFNvXYZEKb6tKB";
        // The asset code
        $assetCode = "buQhapCK83xPPdjQeDuBLJtFNvXYZEKb6tKB";
        // The accout address of issuing asset
        $assetIssuer = "buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt";
        // The asset amount to be sent
        $assetAmount = 100000;
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        //Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("0.01");
        // Metadata
        $metadata = "send asset";

        // 1. Get the account address to send this transaction
        $accountAddress = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($senderPrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($accountAddress) + 1;

        // 2. Build sendAsset
        $sendAsset = new \src\model\request\operation\AssetSendOperation();
        $sendAsset->setSourceAddress(null);
        $sendAsset->setDestAddress($destAddress);
        $sendAsset->setCode($assetCode);
        $sendAsset->setIssuer($assetIssuer);
        $sendAsset->setAmount($assetAmount);
        $sendAsset->setMetadata($metadata);

        $operations = array();
        array_push($operations, $sendAsset);

        $privateKeys = array();
        array_push($privateKeys, $senderPrivateKey);

        $hash = ATP10TokenDemo::buildBlobAndSignAndSubmit($privateKeys, $accountAddress, $nonce, $gasPrice, $feeLimit, $metadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }


    /** @test */
    public function accountGetAssets() {
        
        $account = $GLOBALS['sdk']->getAccountService();

        $accountGetAssetsRequest = new \src\model\request\AccountGetAssetsRequest();
        $accountGetAssetsRequest->setAddress("buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt");
        $response = $account->getAssets($accountGetAssetsRequest);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
    }

    /** @test */
    public function assetGetInfo() {
        $this->setSDKConfigure(0);
        $asset = $GLOBALS['sdk']->getAssetService();

        $assetGetInfoRequest = new \src\model\request\AssetGetInfoRequest();
        $assetGetInfoRequest->setAddress("buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt");
        $assetGetInfoRequest->setCode("TXT");
        $assetGetInfoRequest->setIssuer("buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt");
        $response = $asset->getInfo($assetGetInfoRequest);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
    }

    private function getAccountNonce($accountAddress) {
        $this->setSDKConfigure(0);
        $account = $GLOBALS['sdk']->getAccountService();

        $accountGetNonceRequest = new \src\model\request\AccountGetNonceRequest();
        $accountGetNonceRequest->setAddress($accountAddress);
        $response = $account->getNonce($accountGetNonceRequest);
        if ($response->error_code != 0) {
            return false;
        }
        return $response->result->nonce;
    }

    private function buildBlobAndSignAndSubmit($privateKeys, $sourceAddress, $nonce, $gasPrice, $feeLimit, $metadata, $operations) {
        $this->setSDKConfigure(0);
        $transaction =  $GLOBALS['sdk']->getTransactionService();

        // Build blob
        $buildBlobRequest = new \src\model\request\TransactionBuildBlobRequest();
        $buildBlobRequest->setSourceAddress($sourceAddress);
        $buildBlobRequest->setNonce($nonce);
        $buildBlobRequest->setGasPrice($gasPrice);
        $buildBlobRequest->setFeeLimit($feeLimit);
        $buildBlobRequest->setOperations($operations);
        if (!$metadata) {
            $buildBlobRequest->setMetadata($metadata);
        }

        $buildBlobResponse = $transaction->buildBlob($buildBlobRequest);
        if ($buildBlobResponse->error_code != 0) {
            echo $buildBlobResponse->error_code . ", " . $buildBlobResponse->error_desc . "\n";
            return false;
        }
        echo "blob: " . $buildBlobResponse->result->transaction_blob . "\n";
        $hash = $buildBlobResponse->result->hash;

        // Sign blob
        $signRequest = new \src\model\request\TransactionSignRequest();
        $signRequest->setBlob($buildBlobResponse->result->transaction_blob);
        $signRequest->setPrivateKeys($privateKeys);
        $signResponse = $transaction->sign($signRequest);
        if ($signResponse->error_code != 0) {
            echo $signResponse->error_desc . "\n";
            return false;
        }
        echo "Sign successfully\n";

        // Submit
        $submitRequest = new \src\model\request\TransactionSubmitRequest();
        $submitRequest->setTransactionBlob($buildBlobResponse->result->transaction_blob);
        $submitRequest->setSignatures($signResponse->result->signatures);
        $submitResponse = $transaction->submit($submitRequest);
        if ($submitResponse->error_code != 0) {
            echo json_encode($submitResponse). "\n";
            return false;
        }
        return $hash;
    }
}
