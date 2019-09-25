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

class OfflineSignatureDemo extends PHPUnit_Framework_TestCase {
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
     * @Description 1. Generating transaction Blob in a network environment
     * @Author riven
     * @Method Online_BuildTransactionBlob
     * @Params []
     * @Return void
     * @Date 2018/7/12 16:10
     */
    /** @test */
    public function Online_BuildTransactionBlob() {
        // The account to send BU
        $senderAddresss = "buQnnUEBREw2hB6pWHGPzwanX7d28xk6KVcp";
        // The account to receive BU
        $destAddress = "buQBjJD1BSJ7nzAbzdTenAhpFjmxRVEEtmxH";
        // The amount to be sent
        $amount = \src\common\Tools::BU2MO("10.9");
        // The fixed write 1000L, the unit is MO
        $gasPrice = 1000;
        // Set up the maximum cost 0.01BU
        $feeLimit = \src\common\Tools::BU2MO("0.01");
        // Transaction initiation account's nonce + 1
        $nonce = $this->getAccountNonce($senderAddresss) + 1;
        // Metadata
        $metadata = "";

        // Build the transaction Blob and return to transactionBlobResult (including transaction Blob and transaction hash)
        $transactionBlobResult = OfflineSignatureDemo::buildTransactionBlob($senderAddresss, $nonce, $destAddress, $amount, $feeLimit, $gasPrice, $metadata);
        var_dump($transactionBlobResult);
    }

    /**
     * @description 2. Parsing transaction Blob under no network environment
     * @author riven
     * @method Offline_ParseBlob
     * @params []
     * @return void
     * @date 2018/7/12 16:11
     */
    /** @test */
    public function Offline_ParseBlob() {
        // Get transactionBlobResult from 1 (Network Environment)
        $transactionBlobResult = "{\"transaction_blob\":\"0A246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370102118C0843D20E8073A56080712246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370522C0A24627551426A4A443142534A376E7A41627A6454656E416870466A6D7852564545746D78481080A9E08704\",\"hash\":\"d8fde921219ce265acb51e2cffbe7855e6423f795781e1810595159d9c104522\"}";

            // Parsing the transaction Blob
        OfflineSignatureDemo::parseBlob($transactionBlobResult);
    }

    /**
     * @Description 3. Blob under no network environment
     * @Author riven
     * @Method Offline_SignTransactionBlob
     * @Params []
     * @Return void
     * @Date 2018/7/12 16:12
     */
    /** @test */
    public function Offline_SignTransactionBlob() {
        // When the transaction Blob is confirmed, it begins to sign a signature

        // Transaction Blob
        $transactionBlob = "0A246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370102118C0843D20E8073A56080712246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370522C0A24627551426A4A443142534A376E7A41627A6454656E416870466A6D7852564545746D78481080A9E08704";
            // The account private key to send BU
        $senderPrivateKey = "privbyQCRp7DLqKtRFCqKQJr81TurTqG6UKXMMtGAmPG3abcM9XHjWvq";

            // Sign transaction
        $signature = OfflineSignatureDemo::signTransaction($transactionBlob, $senderPrivateKey);
        if ($signature !== false) {
            echo $signature . "\n";
        }
    }

    /**
     * @Description 4. Broadcast transactions in a network environment
     * @Author riven
     * @Method Online_SubmitTransaction
     * @Params []
     * @Return void
     * @Date 2018/7/12 16:13
     */
    /** @test */
    public function Online_SubmitTransaction() {
        // Get the signature in 3 (no net environment)
        $signature = "{\"signatures\":[{\"public_key\":\"b0011765082a9352e04678ef38d38046dc01306edef676547456c0c23e270aaed7ffe9e31477\",\"sign_data\":\"D2B5E3045F2C1B7D363D4F58C1858C30ABBBB0F41E4B2E18AF680553CA9C3689078E215C097086E47A4393BCA715C7A5D2C180D8750F35C6798944F79CC5000A\"}]}";
        $transactionBlob = "0A246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370102118C0843D20E8073A56080712246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370522C0A24627551426A4A443142534A376E7A41627A6454656E416870466A6D7852564545746D78481080A9E08704";

            // Submit a transaction and return the transaction hash
        $hash = OfflineSignatureDemo::submitTransaction($transactionBlob, $signature);
        if ($hash !== false) {
           echo "transaction hash: " . $hash . "\m";
        }
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

    private function buildTransactionBlob($senderAddresss, $nonce, $destAddress, $amount, $feeLimit, $gasPrice, $metadata) {
        $this->setSDKConfigure(0);
        $transaction =  $GLOBALS['sdk']->getTransactionService();

        // 1. Build sendBU
        $sendBU = new \src\model\request\operation\BUSendOperation();
        $sendBU->setSourceAddress($senderAddresss);
        $sendBU->setDestAddress($destAddress);
        $sendBU->setAmount($amount);
        $sendBU->setMetadata($metadata);

        $operations = array();
        array_push($operations, $sendBU);

        // Build blob
        $buildBlobRequest = new \src\model\request\TransactionBuildBlobRequest();
        $buildBlobRequest->setSourceAddress($senderAddresss);
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
        return $buildBlobResponse->result->transaction_blob;
    }

    private function parseBlob($transactionBlobResult) {
        $this->setSDKConfigure(0);
        $transaction =  $GLOBALS['sdk']->getTransactionService();

        $buildBlobResult = \src\common\Tools::jsonToClass($transactionBlobResult, new \src\model\response\result\TransactionBuildBlobResult());
        $hash = bin2hex(hash('sha256', hex2bin($buildBlobResult->transaction_blob),true));
        if ($buildBlobResult->hash !== $hash) {
            echo "transactionBlob (" . $buildBlobResult->transaction_blob . ") is invalid\n";
            return false;
        }

        $request = new \src\model\request\TransactionParseBlobRequest();
        $request->setBlob($buildBlobResult->transaction_blob);
        $response = $transaction->parseBlob($request);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        var_dump($json_response);
        return $json_response;
    }

    private function signTransaction($transactionblob, $senderPrivateKey) {
        $this->setSDKConfigure(0);
        $transaction =  $GLOBALS['sdk']->getTransactionService();

        // Sign blob
        $signRequest = new \src\model\request\TransactionSignRequest();
        $signRequest->setBlob($transactionblob);
        $signRequest->addPrivateKey($senderPrivateKey);
        $signResponse = $transaction->sign($signRequest);
        if ($signResponse->error_code != 0) {
            echo $signResponse->error_desc . "\n";
            return false;
        }
        return json_encode($signResponse->result);
    }

    private function submitTransaction($transactionBlob, $signatures) {
        $this->setSDKConfigure(0);
        $transaction =  $GLOBALS['sdk']->getTransactionService();
        // Submit
        $submitRequest = new \src\model\request\TransactionSubmitRequest();
        $submitRequest->setTransactionBlob($transactionBlob);
        $result = \src\common\Tools::jsonToClass($signatures, new \src\model\response\result\TransactionSignResult());
        $submitRequest->setSignatures($result->signatures);
        $submitResponse = $transaction->submit($submitRequest);
        if ($submitResponse->error_code != 0) {
            echo json_encode($submitResponse). "\n";
            return false;
        }
        return $submitResponse->hash;
    }
}
