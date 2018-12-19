<?php
/**
 * Created by PhpStorm.
 * User: fengruiming
 * Date: 2018/11/9
 * Time: 10:19
 */

include_once dirname(dirname(dirname(__FILE__))) . "/autoload.php";

//$sdk = \src\SDK::getInstance("http://seed1.bumotest.io:26002");
$sdk = \src\SDK::getInstance("http://127.0.0.1:36002"); // localhost

class ContractDemo extends PHPUnit_Framework_TestCase {
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

    /** @test */
    public function contractCreate() {
        // The account private key to activate a new account
        $activatePrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        $initBalance = \src\common\Tools::BU2MO("0.1");
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        // Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("0.01");
        // Metadata
        $metadata = "activate new account";

        // 1. Get the account address to send this transaction
        $activateAddress = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($activatePrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($activateAddress) + 1;

        // 2. Build activateAccount
        $contractCreate = new \src\model\request\operation\ContractCreateOperation();
        $contractCreate->setSourceAddress($activateAddress);
        $contractCreate->setInitBalance($initBalance);
        $contractCreate->setPayload("123");
        $contractCreate->setMetadata("activate a contract");

        $operations = array();
        array_push($operations, $contractCreate);

        $privateKeys = array();
        array_push($privateKeys, $activatePrivateKey);

        $hash = ContractDemo::buildBlobAndSignAndSubmit($privateKeys, $activateAddress, $nonce, $gasPrice, $feeLimit, $metadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }

    /** @test */
    public function contractCheckValid() {
        $this->setSDKConfigure(10);
        $contract = $GLOBALS['sdk']->getContractService();

        $contractCheckValidRequest = new \src\model\request\ContractCheckValidRequest();
        $contractCheckValidRequest->setContractAddress("buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw");
        $response = $contract->checkValid($contractCheckValidRequest);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
    }

    /** @test */
    public function contractGetInfo() {
        $this->setSDKConfigure(10);
        $contract = $GLOBALS['sdk']->getContractService();

        $contractGetInfoRequest = new \src\model\request\ContractGetInfoRequest();
        $contractGetInfoRequest->setContractAddress("buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea");
        $response = $contract->getInfo($contractGetInfoRequest);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
    }

    /** @test */
    public function contractGetAddress() {
        $this->setSDKConfigure(10);
        $contract = $GLOBALS['sdk']->getContractService();

        $contractGetAddressRequest = new \src\model\request\ContractGetAddressRequest();
        //$contractGetAddressRequest->setHash("44246c5ba1b8b835a5cbc29bdc9454cdb9a9d049870e41227f2dcfbcf7a07689");
        $contractGetAddressRequest->setHash("68e8f1dea066cef95715de69ec067fd2eb424d85122b8bc4d5feed5a847bb7db");
        $response = $contract->getAddress($contractGetAddressRequest);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
    }

    /** @test */
    public function contractInvokeByBU() {
        // The account private key to send bu
        $senderPrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        // The account to receive bu
        $destAddress = "buQmi7hbpnapiwXPmzbDPwuvxGVC9BLmWyGy";
        // The amount to be sent
        $buAmount = 0;
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        //Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("50.01");
        // Metadata
        $metadata = "发送BU资产";

        // 1. Get the account address to send this transaction
        $accountAddress = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($senderPrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($accountAddress) + 1;

        // 2. Build sendBU
        $contractInvokeByBU = new \src\model\request\operation\ContractInvokeByBUOperation();
        $contractInvokeByBU->setSourceAddress($accountAddress);
        $contractInvokeByBU->setContractAddress($destAddress);
        $contractInvokeByBU->setBuAmount(null);
        $contractInvokeByBU->setMetadata($metadata);

        $operations = array();
        array_push($operations, $contractInvokeByBU);

        $privateKeys = array();
        array_push($privateKeys, $senderPrivateKey);

        $hash = ContractDemo::buildBlobAndSignAndSubmit($privateKeys, $accountAddress, $nonce, $gasPrice, $feeLimit, $metadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }

    /** @test */
    public function contractInvokeByAsset() {
        // The account private key to send asset
        $senderPrivateKey = "privbyJsNEz6oGFmXCXBHtCKSerTFidxd86Swe5JfKxyUQ5Mqt48Rg22";
        // The account to receive asset
        $destAddress = "buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt";
        // The asset code
        $assetCode = "";
        // The accout address of issuing asset
        $assetIssuer = "buQjRsKFr7HfNrBTWWgQ44fUfAQ5NwgVhaBt";
        // The asset amount to be sent
        $assetAmount = 100000;
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        //Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("50.01");
        // Metadata
        $metadata = "send asset";

        // 1. Get the account address to send this transaction
        $accountAddress = \src\crypto\key\KeyPair::getEncAddressByPrivateKey($senderPrivateKey);
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($accountAddress) + 1;

        // 2. Build sendAsset
        $contractInvokeByAsset = new \src\model\request\operation\ContractInvokeByAssetOperation();
        $contractInvokeByAsset->setSourceAddress(null);
        $contractInvokeByAsset->setContractAddress($destAddress);
        $contractInvokeByAsset->setCode($assetCode);
        $contractInvokeByAsset->setIssuer($assetIssuer);
        $contractInvokeByAsset->setAssetAmount(null);
        $contractInvokeByAsset->setMetadata($metadata);

        $operations = array();
        array_push($operations, $contractInvokeByAsset);

        $privateKeys = array();
        array_push($privateKeys, $senderPrivateKey);

        $hash = ContractDemo::buildBlobAndSignAndSubmit($privateKeys, $accountAddress, $nonce, $gasPrice, $feeLimit, $metadata, $operations);
        if ($hash) {
            echo "Submit transaction successfully, hash: " . $hash;
        }
    }

    /** @test */
    public function contractCall() {
        $this->setSDKConfigure(10);
        $contract = $GLOBALS['sdk']->getContractService();

        $contractCallRequest = new \src\model\request\ContractCallRequest();
        $contractCallRequest->setContractAddress("buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea");
        $contractCallRequest->setFeeLimit(10000000000);
        $contractCallRequest->setOptType(1);
        $response = $contract->call($contractCallRequest);
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
