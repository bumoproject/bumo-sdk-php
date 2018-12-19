[English](SDK.md) | 中文

# Bumo PHP SDK

## 概述
本文档详细说明Bumo PHP SDK常用接口文档, 使开发者更方便地操作和查询BU区块链。

- [名词解析](#名词解析)
- [请求参数与响应数据格式](#请求参数与响应数据格式)
	- [请求参数](#请求参数)
	- [响应数据](#响应数据)
- [使用方法](#使用方法)
    - [生成SDK实例](#生成sdk实例)
    - [生成公私钥地址](#生成公私钥地址)
    - [有效性校验](#有效性校验)
    - [查询](#查询)
	- [提交交易](#提交交易)
		- [获取交易发起的账户nonce值](#获取交易发起的账户nonce值)
		- [构建操作](#构建操作)
		- [序列化交易](#序列化交易)
		- [签名交易](#签名交易)
		- [广播交易](#广播交易)
- [账户服务](#账户服务)
	- [checkValid](#checkvalid-账户)
	- [getInfo](#getinfo-账户)
	- [getNonce](#getnonce)
	- [getBalance](#getbalance-账户)
	- [getAssets](#getassets)
	- [getMetadata](#getmetadata)
- [资产服务](#资产服务)
    - [getInfo](#getinfo-资产)
- [合约服务](#合约服务)
    - [checkValid](#checkvalid-合约)
	- [getInfo](#getinfo-合约)
    - [getAddress](#getaddress)
	- [call](#call)
- [交易服务](#交易服务)
    - [操作说明](#操作说明)
	- [buildBlob](#buildblob)
	- [evaluateFee](#evaluatefee)
	- [sign](#sign)
	- [submit](#submit)
	- [getInfo](#getinfo-交易)
- [区块服务](#区块服务)
    - [getNumber](#getnumber)
	- [checkStatus](#checkstatus)
	- [getTransactions](#gettransactions)
	- [getInfo](#getinfo-区块)
	- [getLatestInfo](#getlatestinfo)
	- [getValidators](#getvalidators)
	- [getLatestValidators](#getlatestvalidators)
	- [getReward](#getreward)
	- [getLatestReward](#getlatestreward)
	- [getFees](#getfees)
	- [getLatestFees](#getlatestfees)
- [错误码](#错误码)

## 名词解析

操作BU区块链： 向BU区块链写入或修改数据

提交交易： 向BU区块链发送写入或修改数据的请求

查询BU区块链： 查询BU区块链中的数据

账户服务： 提供账户相关的有效性校验与查询接口

资产服务： 提供资产相关的查询接口，该资产遵循ATP1.0协议

合约服务： 提供合约相关的有效性校验与查询接口

交易服务： 提供构建交易Blob接口，签名接口，查询与提交交易接口

区块服务： 提供区块的查询接口

账户nonce值： 每个账户都维护一个序列号，用于用户提交交易时标识交易执行顺序的

## 请求参数与响应数据格式

### 请求参数

接口的请求参数的类名，是[服务名][方法名]Request，比如: 账户服务下的getInfo接口的请求参数格式是AccountGetInfoRequest。

请求参数的成员，是各个接口的入参的成员。例如：账户服务下的getInfo接口的入参成员是address，那么该接口的请求参数的完整结构如下：
```php
class AccountGetInfoRequest {
	$address; // string
}
```

### 响应数据

接口的响应数据的类名，是[服务名][方法名]Response，比如：账户服务下的getNonce接口的响应数据格式是AccountGetNonceResponse。

响应数据的成员，包括错误码、错误描述和返回结果，比如资产服务下的getInfo接口的响应数据的成员如下：
```php
class AccountGetNonceResponse {
	$errorCode; // int
	$errorDesc; // string
	$result; // AccountGetNonceResult
}
```

说明：
1. errorCode: 错误码。0表示无错误，大于0表示有错误
2. errorDesc: 错误描述。
3. result: 返回结果。一个结构体，其类名是[服务名][方法名]Result，其成员是各个接口返回值的成员，例如：账户服务下的getNonce接口的结果类名是AccountGetNonceResult，成员有nonce, 完整结构如下：
```php
class AccountGetNonceResult {
	$nonce; // long
}
```

## 使用方法

这里介绍SDK的使用流程，首先需要生成SDK实现，然后调用相应服务的接口，其中服务包括账户服务、资产服务、合约服务、交易服务、区块服务，接口按使用分类分为生成公私钥地址接口、有效性校验接口、查询接口、广播交易相关接口

### 生成SDK实例

调用SDK的接口getInstance来实现，调用如下：
```php
$url = "http://seed1.bumotest.io";
$sdk = \src\SDK::getInstance($url);
```

### 生成公私钥地址

此接口生成BU区块链账户的公钥、私钥和地址，直接调用账户服务下的create接口即可，调用如下：
```php
$account = $sdk->getAccountService();
$response = $account->create();
if (0 == $response->error_code) {
    echo $response->result->private_key . "\n";
    echo $response->result->public_key . "\n";
    echo $response->result->address . "\n";
}
```

### 有效性校验
此接口用于校验信息的有效性的，直接调用相应的接口即可，比如，校验账户地址有效性，调用如下：
```php
// 初始化请求参数
$address = "buQemmMwmRQY1JkcU7w3nhruoX5N3j6C29uo";
$request = new \src\model\request\AccountCheckValidRequest();
$request->setAddress($address);

// 调用checkValid接口
$response = $sdk->getAccountService()->checkValid($request);
if(0 == $response->error_code) {
	echo $response->result->is_valid . "\n";
} else {
	echo "error: " . $response->error_desc . "\n";
}
```

### 查询
此接口用于查询BU区块链上的数据，直接调用相应的接口即可，比如，查询账户信息，调用如下：
```php
// 初始化请求参数
$accountAddress = "buQemmMwmRQY1JkcU7w3nhruo%X5N3j6C29uo";
$request = new \src\model\request\AccountGetInfoRequest();
$request->setAddress(accountAddress);

// 调用getInfo接口
$response =  $sdk->getAccountService()->getInfo($request);
if ($response->error_code == 0) {
	$result = $response->result;
	echo json_encode($result) . "\n";
}
else {
	echo "error: " . $response->error_desc . "\n";
}
```

### 广播交易
广播交易的过程包括以下几步：获取交易发起的账户nonce值 -> 构建操作 -> 序列化交易 -> 签名交易 -> 提交交易。

#### 获取交易发起的账户nonce值

开发者可自己维护各个账户nonce，在提交完一个交易后，自动递增1，这样可以在短时间内发送多笔交易，否则，必须等上一个交易执行完成后，账户的nonce值才会加1。接口调用如下：
```php
// 初始化请求参数
$senderAddress = "buQnnUEBREw2hB6pWHGPzwanX7d28xk6KVcp";
$getNonceRequest = new \src\model\request\AccountGetNonceRequest();
$getNonceRequest->setAddress($senderAddress);

// 调用getNonce接口
$getNonceResponse =  $sdk->getAccountService()->getNonce($getNonceRequest);

// 赋值nonce
if ($getNonceResponse->error_code == 0) {
   $result = $getNonceResponse->result;
   echo "nonce: " . $result->nonce . "\n";
}
else {
    echo "error" . $getNonceresponse->error_desc . "\n";
}
```

#### 构建操作

这里的操作是指在交易中做的一些动作，便于序列化交易和评估费用 例如：构建发送BU操作BUSendOperation，接口调用如下：
```php 
$senderAddress = "buQnnUEBREw2hB6pWHGPzwanX7d28xk6KVcp";
$destAddress = "buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw";
$buAmount = \src\common\Tools::BU2MO("10.9");

$operation = new \src\model\request\operation\BUSendOperation();
$operation->setSourceAddress($senderAddress);
$operation->setDestAddress($destAddress);
$operation->setAmount($buAmount);
```

#### 序列化交易



该接口用于序列化交易，并生成交易Blob串，便于网络传输。其中nonce和operation是上面接口得到的，接口调用如下：
```php 
// 初始化变量
$senderAddress = "buQnnUEBREw2hB6pWHGPzwanX7d28xk6KVcp";
$gasPrice = 1000;
$feeLimit = \src\common\Tools::BU2MO("0.01");

// 初始化请求参数
$buildBlobRequest = new \src\model\request\TransactionBuildBlobRequest();
$buildBlobRequest->setSourceAddress($senderAddress);
$buildBlobRequest->setNonce($nonce + 1);
$buildBlobRequest->setFeeLimit($feeLimit);
$buildBlobRequest->setGasPrice($gasPrice);
$buildBlobRequest->addOperation($operation);

// 调用buildBlob接口
$buildBlobResponse = $sdk->getTransactionService()->buildBlob($buildBlobRequest);
if ($buildBlobResponse->error_code == 0) {
    $result = $buildBlobResponse->result;
    echo "txHash: " . $result->hash . ", blob: " . $result->transaction_blob . "\n";
} else {
    echo "error: " . $buildBlobResponse->error_desc . "\n";
}
```

#### 签名交易

该接口用于交易发起者使用其账户私钥对交易进行签名。其中transactionBlob是上面接口得到的，接口调用如下：
```php 
// 初始化请求参数
$senderPrivateKey = "privbyQCRp7DLqKtRFCqKQJr81TurTqG6UKXMMtGAmPG3abcM9XHjWvq";
$signRequest = new \src\model\request\TransactionSignRequest();
$signRequest->addPrivateKey($senderPrivateKey);
$signRequest->setBlob($transactionBlob);

// 调用sign接口
$signResponse = $sdk->getTransactionService()->sign($signRequest);
if ($signResponse->error_code == 0) {
    $result = $signResponse->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "error: " . $signResponse->error_desc . "\n";
}
```

#### 提交交易

该接口用于向BU区块链发送交易请求，触发交易的执行。其中transactionBlob和signResult是上面接口得到的，接口调用如下：
```php 
// 初始化请求参数
$submitRequest = new \src\model\request\TransactionSubmitRequest();
$submitRequest->setTransactionBlob($transactionBlob);
$submitRequest->setSignatures($signResult->signatures);

// 调用submit接口
$response = $sdk->getTransactionService()->submit($submitRequest);
if (0 == $response->error_code) {
    echo "交易广播成功，hash=" . $response->result->hash . "\n";
} else {
    echo "error: " . $response->error_desc . "\n";
}
```

## 账户服务

账户服务主要是账户相关的接口，包括6个接口：checkValid, getInfo, getNonce, getBalance, getAssets, getMetadata。

### checkValid-账户
> 接口说明

   该接口用于检查区块链账户地址的有效性

> 调用方法

```php
/**
 * Check the availability of address
 * @param AccountCheckValidRequest $accountCheckValidRequest
 * @return AccountCheckValidResponse
 */
public function checkValid($accountCheckValidRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String     |  必填，待检查的区块链账户地址   

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
is_valid     | boolean |  是否有效   

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR |   20000     |  System error 
INVALID_REQUEST_ERROR | 17004 | Request is invalid 

> 示例

```php
// 初始化请求参数
$address = "buQemmMwmRQY1JkcU7w3nhruoX5N3j6C29uo";
$request = new \src\model\request\AccountCheckValidRequest();
$request->setAddress(address);

// 调用checkValid
$response = $sdk->getAccountService()->checkValid($request);
if(0 == $response->error_code) {
	echo $response->result->is_valid . "\n";
} else {
	echo "error: " . $response->error_desc . "\n";
}
```

### getInfo-账户

> 接口说明

   该接口用于获取指定的账户信息

> 调用方法

```php
/**
 * Get account info
 * @param AccountGetInfoRequest $accountGetInfoRequest
 * @return AccountGetInfoResponse, including address，balance，nonce and privilege
 */
public function getInfo($accountGetInfoRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String     |  必填，待查询的区块链账户地址   

> 响应数据

   参数    |     类型      |        描述       
--------- | ------------- | ---------------- 
address	  |    String     |    账户地址       
balance	  |    Long       |    账户余额，单位MO，1 BU = 10^8 MO, 必须大于0
nonce	  |    Long       |    账户交易序列号，必须大于0
priv	  | [Priv](#priv) |    账户权限

#### Priv
   成员       |     类型     |        描述       
-----------  | ------------ | ---------------- 
masterWeight |	 Long	    | 账户自身权重，大小限制[0, max(uint32)] 
signers	     |[Signer](#signer)[]|   签名者权重列表
threshold	 |[Threshold](#Threshold)|	门限

#### Signer
   成员       |     类型     |        描述       
-----------  | ------------ | ---------------- 
address	     |   String	    |   签名者区块链账户地址
weight	     |   Long	    | 签名者权重，大小限制[0, max(uint32)] 

#### Threshold
   成员       |     类型     |        描述       
-----------  | ------------ | ---------------- 
txThreshold	 |    Long	    | 交易默认门限，大小限制[0, max(int64)] 
typeThresholds|[TypeThreshold](#typethreshold)[]|不同类型交易的门限

#### TypeThreshold
   成员       |     类型     |        描述       
-----------  | ------------ | ---------------- 
type         |    Long	    |    操作类型，必须大于0
threshold    |    Long      | 门限值，大小限制[0, max(int64)] 

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR| 11006 | Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR| 11007| Failed to connect to the network
SYSTEM_ERROR |   20000     |  System error 
INVALID_REQUEST_ERROR | 17004 | Request is invalid 

> 示例

```php
// 初始化请求参数
$accountAddress = "buQemmMwmRQY1JkcU7w3nhruoX5N3j6C29uo";
$request = new \src\model\request\AccountGetInfoRequest();
$request->setAddress($accountAddress);

// 调用getInfo接口
$response =  $sdk->getAccountService()->getInfo($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo "账户信息: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "error: " . $response->error_desc . "\n";
}
```

### getNonce

> 接口说明

   该接口用于获取指定账户的nonce值

> 调用方法

```php
/**
 * Get account nonce
 * @param AccountGetNonceRequest $accountGetNonceRequest
 * @return AccountGetNonceResponse
 */
public function getNonce($accountGetNonceRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String     |  必填，待查询的区块链账户地址   

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
nonce       |   Long       |  账户交易序列号   

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR| 11006 | Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR| 11007| Failed to connect to the network
SYSTEM_ERROR |   20000     |  System error 
INVALID_REQUEST_ERROR | 17004 | Request is invalid 

> 示例

```php
// 初始化请求参数
$accountAddress = "buQswSaKDACkrFsnP1wcVsLAUzXQsemauEjf";
$request = new \src\model\request\AccountGetNonceRequest();
$request->setAddress($accountAddress);

// 调用getNonce接口
$response = $sdk->getAccountService()->getNonce($request);
if(0 == $response->error_code){
    echo "账户nonce:" . $response->result->nonce;
} else {
    echo "error: " . $response->error_desc;
}
```

### getBalance-账户

> 接口说明

   该接口用于获取指定账户的BU的余额

> 调用方法

```php
/**
 * Get account balance of BU
 * @param AccountGetBalanceRequest $accountGetBalanceRequest
 * @return AccountGetBalanceResponse
 */
public function getBalance($accountGetBalanceRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String     |  必填，待查询的区块链账户地址   

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
balance     |   Long       |  BU的余额, 单位MO，1 BU = 10^8 MO 

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR| 11006 | Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR| 11007| Failed to connect to the network
SYSTEM_ERROR |   20000     |  System error 
INVALID_REQUEST_ERROR | 17004 | Request is invalid 

> 示例

```php
// 初始化请求参数
$accountAddress = "buQswSaKDACkrFsnP1wcVsLAUzXQsemauEjf";
$request = new \src\model\request\AccountGetBalanceRequest();
$request->setAddress($accountAddress);

// 调用getBalance接口
$response = $sdk->getAccountService()->getBalance($request);
if(0 == $response->error_code){
    $result = $response->result;
    echo "BU余额：" . \src\common\Tools::BU2MO($result->balance) . " BU";
} else {
    echo "error: " . $response->error_desc;
}
```

### getAssets

> 接口说明

   该接口用于获取指定账户的所有资产信息

> 调用方法

```php
/**
 * Get all assets of an account
 * @param AccountGetAssetsRequest $accountGetAssetsRequest
 * @return AccountGetAssetsResponse, include code, issuer, amount
 */
public function getAssets;
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String     |  必填，待查询的账户地址   

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
asset	    | [AssetInfo](#AssetInfo)[] |账户资产

#### AssetInfo

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
  key       | [Key](#Key)  | 资产惟一标识
  assetAmount    | Long        | 资产数量

 #### Key

   成员   |     类型    |     描述       
-------- | ----------- | -----------
code     |   String    |   资产编码
issuer   |   String    |   资产发行账户地址

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR| 11006 | Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR| 11007| Failed to connect to the network
NO_ASSET_ERROR|11009|The account does not have the asset
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\AccountGetAssetsRequest();
$request->setAddress("buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw");

// 调用getAssets接口
$response = $sdk->getAccountService()->getAssets($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getMetadata

> 接口说明

   该接口用于获取指定账户的metadata信息

> 调用方法

```php
/**
 * Get the metadata of an account
 * @param AccountGetMetadataRequest $accountGetMetadataRequest
 * @return AccountGetMetadataResponse, include key and value
 */
public function getMetadata($accountGetMetadataRequest);
```

> 请求参数

   参数   |   类型   |        描述       
-------- | -------- | ---------------- 
address  |  String  |  必填，待查询的账户地址  
key      |  String  |  选填，metadata关键字，长度限制[1, 1024]

> 响应数据

   参数      |     类型    |        描述       
----------- | ----------- | ---------------- 
metadata    |[MetadataInfo](#MetadataInfo)   |  账户

#### MetadataInfo
   成员      |     类型    |        描述       
----------- | ----------- | ---------------- 
key         |  String     |  metadata的关键词
value       |  String     |  metadata的内容
version     |  Long      |  metadata的版本


> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR | 11006 | Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR | 11007 | Failed to connect to the network
NO_METADATA_ERROR|11010|The account does not have the metadata
INVALID_DATAKEY_ERROR | 11011 | The length of key must be between 1 and 1024
SYSTEM_ERROR | 20000| System error
INVALID_REQUEST_ERROR | 17004 | Request is invalid 


> 示例

```php
// 初始化请求参数
$accountAddress = "buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw";
$request = new \src\model\request\AccountGetMetadataRequest();
$request->setAddress($accountAddress);
$request->setKey("20180704");

// 调用getMetadata接口
$response =  $sdk->getAccountService()->getMetadata($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

## 资产服务

遵循ATP1.0协议，账户服务主要是资产相关的接口，目前有1个接口：getInfo

### getInfo-资产

> 接口说明

   该接口用于获取指定账户的指定资产信息

> 调用方法

```php
/**
 * Get details of the specified asset
 * @param  AssetGetInfoRequest $assetGetInfoRequest
 * @return AssetGetInfoResponse
 */
function getInfo($assetGetInfoRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
address     |   String    |  必填，待查询的账户地址
code        |   String    |  必填，资产编码，长度限制[1, 64]
issuer      |   String    |  必填，资产发行账户地址

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
asset	    | [AssetInfo](#AssetInfo)[] |账户资产   

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_ADDRESS_ERROR|11006|Invalid address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
INVALID_ASSET_CODE_ERROR|11023|The length of asset code must be between 1 and 64
INVALID_ISSUER_ADDRESS_ERROR|11027|Invalid issuer address
 NO_ASSET_ERROR               | 11009  | The account does not have this token              
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\AssetGetInfoRequest();
$request->setAddress("buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw");
$request->setIssuer("buQBjJD1BSJ7nzAbzdTenAhpFjmxRVEEtmxH");
$request->setCode("HNC");

// 调用getInfo消息
$response = $sdk->getAssetService()->getInfo($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

## 合约服务

合约服务主要是合约相关的接口，目前有4个接口：checkValid, getInfo, getAddress, call

### checkValid-合约

> 接口说明

   该接口用于检测合约账户的有效性

> 调用方法

```php
/**
 * Check the availability of a contract
 * @param  ContractCheckValidRequest $contractCheckValidRequest
 * @return ContractCheckValidResponse
 */
function checkValid($contractCheckValidRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
contractAddress     |   String     |  待检测的合约账户地址   

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
isValid     |   Boolean     |  是否有效   

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_CONTRACTADDRESS_ERROR|11037|Invalid contract address
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR |   20000     |  System error 
INVALID_REQUEST_ERROR | 17004 | Request is invalid 

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\ContractCheckValidRequest();
$request->setContractAddress("buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea");

// 调用checkValid接口
$response = $sdk->getContractService()->checkValid($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo result->is_valid;
} else {
    echo "error: " . $response->error_desc;
}
```

### getInfo-合约

> 接口说明

   该接口用于查询合约代码

> 调用方法

```php
/**
 * Get the details of contract, include type and payload
 * @param ContractGetInfoRequest $contractGetInfoRequest
 * @return ContractGetInfoResponse
 */
function getInfo($contractGetInfoRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
contractAddress     |   String     |  待查询的合约账户地址 

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
contract|[ContractInfo](#contractinfo)|合约信息

#### ContractInfo

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
type|Integer|合约类型，默认0
payload|String|合约代码

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_CONTRACTADDRESS_ERROR|11037|Invalid contract address
CONTRACTADDRESS_NOT_CONTRACTACCOUNT_ERROR|11038|contractAddress is not a contract account
NO_SUCH_TOKEN_ERROR|11030|No such token
GET_TOKEN_INFO_ERROR|11066|Failed to get token info
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\ContractGetInfoRequest();
$request->setContractAddress("buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea");

// 调用getInfo接口
$response = $sdk->getContractService()->getInfo($request);
if ($response->error_code == 0) {
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getAddress

> 接口说明

该接口用于查询合约地址

> 调用方法

```php
/**
 * Get the address of a contract account
 * @param  ContractGetAddressRequest $contractGetAddressRequest
 * @return ContractGetAddressResponse
 */
function getAddress($contractGetAddressRequest)
```

> 请求参数

参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
hash     |   String     |  创建合约交易的hash   

> 响应数据

参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
contractAddressList|List<[ContractAddressInfo](#contractaddressinfo)>|合约地址列表

#### ContractAddressInfo

成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
contractAddress|String|合约地址
operationIndex|Integer|所在操作的下标

> 错误码

异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_HASH_ERROR|11055|Invalid transaction hash
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\ContractGetAddressRequest();
$request->setHash("44246c5ba1b8b835a5cbc29bdc9454cdb9a9d049870e41227f2dcfbcf7a07689");

// 调用getAddress接口
$response = $sdk->getContractService()->getAddress($request);
if ($response->error_code == 0) {
   echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else {
   echo "error: " . $response->error_desc;
}
```

### call 

> 接口说明

   该接口用于调试合约代码

> 调用方法

```php
/**
 * Call contract for free
 * @param  ContractCallRequest $contractCallRequest
 * @return ContractCallResponse
 */
function call($contractCallRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
sourceAddress|String|选填，合约触发账户地址
contractAddress|String|选填，合约账户地址，与code不能同时为空
code|String|选填，合约代码，与contractAddress不能同时为空，长度限制[1, 64]
input|String|选填，合约入参
contractBalance|String|选填，赋予合约的初始 BU 余额, 单位MO，1 BU = 10^8 MO, 大小限制[1, max(int64)]
optType|Integer|必填，0: 调用合约的读写接口 init, 1: 调用合约的读写接口 main, 2 :调用只读接口 query
feeLimit|Long|交易要求的最低手续费， 大小限制[1, max(int64)]
gasPrice|Long|交易燃料单价，大小限制[1000, max(int64)]


> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
logs|JSONObject|日志信息
queryRets|JSONArray|查询结果集
stat|[ContractStat](#ContractStat)|合约资源占用信息
txs|[TransactionEnvs](#TransactionEnvs)[]|交易集

#### ContractStat

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- |
  applyTime|Long|接收时间
  memoryUsage|Long|内存占用量
  stackUsage|Long|堆栈占用量
  step|Long|几步完成

#### TransactionEnvs

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
  transactionEnv|[TransactionEnv](#transactionenv)|交易

#### TransactionEnv

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
transaction|[TransactionInfo](#transactioninfo)|交易内容
trigger|[ContractTrigger](#contracttrigger)|合约触发者

#### TransactionInfo

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
sourceAddress|String|交易发起的源账户地址
feeLimit|Long|交易要求的最低费用
gasPrice|Long|交易燃料单价
nonce|Long|交易序列号
operations|[Operation](#operation)[]|操作列表

#### ContractTrigger
   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
transaction|[TriggerTransaction](#triggertransaction)|触发交易

#### Operation

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
type|Integer|操作类型
sourceAddress|String|操作发起源账户地址
metadata|String|备注
createAccount|[OperationCreateAccount](#operationcreateaccount)|创建账户操作
issueAsset|[OperationIssueAsset](#operationissueasset)|发行资产操作
payAsset|[OperationPayAsset](#operationpayasset)|转移资产操作
payCoin|[OperationPayCoin](#operationpaycoin)|发送BU操作
setMetadata|[OperationSetMetadata](#operationsetmetadata)|设置metadata操作
setPrivilege|[OperationSetPrivilege](#operationsetprivilege)|设置账户权限操作
log|[OperationLog](#operationlog)|记录日志

#### TriggerTransaction

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
hash|String|交易hash

#### OperationCreateAccount

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
destAddress|String|目标账户地址
contract|[Contract](#contract)|合约信息
priv|[Priv](#priv)|账户权限
metadata|[MetadataInfo](#metadatainfo)[]|账户
initBalance|Long|账户资产, 单位MO，1 BU = 10^8 MO 
initInput|String|合约init函数的入参

#### Contract

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
type|Integer| 合约的语种，默认不赋值
payload|String|对应语种的合约代码

#### MetadataInfo

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
key|String|metadata的关键词
value|String|metadata的内容
version|Long|metadata的版本

#### OperationIssueAsset

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
code|String|资产编码
assetAmount|Long|资产数量

#### OperationPayAsset

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
destAddress|String|待转移的目标账户地址
asset|[AssetInfo](#assetinfo)|账户资产
input|String|合约main函数入参

#### OperationPayCoin

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
destAddress|String|待转移的目标账户地址
buAmount|Long|待转移的BU数量
input|String|合约main函数入参

#### OperationSetMetadata

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
key|String|metadata的关键词
value|String|metadata的内容
version|Long|metadata的版本
deleteFlag|boolean|是否删除metadata

#### OperationSetPrivilege

   成员      |     类型     |        描述       
----------- | ------------ | ----------------  
masterWeight|String|账户自身权重，大小限制[0, max(uint32)]|
signers|[Signer](#signer)[]|签名者权重列表|
txThreshold|String|交易门限，大小限制[0, max(int64)]|
typeThreshold|[TypeThreshold](#typethreshold)|指定类型交易门限|

#### OperationLog

   成员      |     类型     |        描述       
----------- | ------------ | ---------------- 
topic|String|日志主题
data|String[]|日志内容

> 错误码

   异常       |     错误码   |   描述 
-----------  | ----------- | -------- 
INVALID_SOURCEADDRESS_ERROR|11002|Invalid sourceAddress
INVALID_CONTRACTADDRESS_ERROR|11037|Invalid contract address
CONTRACTADDRESS_CODE_BOTH_NULL_ERROR|11063|ContractAddress and code cannot be empty at the same time
INVALID_OPTTYPE_ERROR|11064|OptType must be between 0 and 2
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_CONTRACT_BALANCE_ERROR|11044|The contractBalance must be between 1 and max(int64)
INVALID_GASPRICE_ERROR|11049|GasPrice must be between 1000 and max(int64)
INVALID_REQUEST_ERROR|17004|Request is invalid
INPUT_NOT_STRING_ERROR|17002|Input must be a string

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\ContractCallRequest();
$request->setCode("\"use strict\";log(undefined);function query() { getBalance(thisAddress); }");
$request->setFeeLimit(1000000000);
$request->setOptType(2);

// 调用call接口
$response = $sdk->getContractService()->call($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

## 交易服务

交易服务主要是交易相关的接口，目前有5个接口：buildBlob, evaluateFee, sign, submit, getInfo。

其中调用buildBlob之前需要构建一些操作，目前操作有10种，分别是AccountActivateOperation，AccountSetMetadataOperation, AccountSetPrivilegeOperation, AssetIssueOperation, AssetSendOperation, BUSendOperation, ContractCreateOperation, ContractInvokeByAssetOperation, ContractInvokeByBUOperation, LogCreateOperation

### 操作说明

> BaseOperation

   成员变量    |     类型  |        描述                           
------------- | -------- | ----------------------------------   
sourceAddress |   String |  选填，操作源账户地址
metadata      |   String |  选填，备注

>  AccountActivateOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU

   成员变量    |     类型  |        描述                           
------------- | -------- | ---------------------------------- 
sourceAddress |   String |  选填，操作源账户地址 
destAddress   |   String |  必填，目标账户地址                     
initBalance   |   Long   |  必填，初始化资产，单位MO，1 BU = 10^8 MO, 大小(0, max(int64)] 
metadata|String|选填，备注

> AccountSetMetadataOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU

   成员变量    |     类型   |        描述                         
------------- | --------- | ------------------------------- 
sourceAddress |   String |  选填，操作源账户地址
key           |   String  |  必填，metadata的关键词，长度限制[1, 1024]
value         |   String  |  必填，metadata的内容，长度限制[0, 256000]
version       |   Long    |  选填，metadata的版本
deleteFlag    |   Boolean |  选填，是否删除metadata
metadata|String|选填，备注           

> AccountSetPrivilegeOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU

   成员变量    |     类型   |        描述               
------------- | --------- | --------------------------
sourceAddress |   String |  选填，操作源账户地址
masterWeight|String|选填，账户自身权重，大小限制[0, max(uint32)]
signers|[Signer](#signer)[]|选填，签名者权重列表
txThreshold|String|选填，交易门限，大小限制[0, max(int64)]
typeThreshold|[TypeThreshold](#typethreshold)[]|选填，指定类型交易门限
metadata|String|选填，备注

> AssetIssueOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是50.01 BU

   成员变量    |     类型   |        描述             
------------- | --------- | ------------------------
sourceAddress|String|选填，操作源账户地址
code|String|必填，资产编码，长度限制[1, 64]
assetAmount|Long|必填，资产发行数量，大小限制[0, max(int64)]
metadata|String|选填，备注

> AssetSendOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU
若目标账户未激活，必须先调用激活账户操作

   成员变量    |     类型   |        描述            
------------- | --------- | ----------------------
sourceAddress|String|选填，操作源账户地址
destAddress|String|必填，目标账户地址
code|String|必填，资产编码，长度限制[1, 64]
issuer|String|必填，资产发行账户地址
assetAmount|Long|必填，资产数量，大小限制[0, max(int64)]
metadata|String|选填，备注

> BUSendOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU
若目标账户未激活，该操作也可使目标账户激活

   成员变量    |     类型   |        描述          
------------- | --------- | ---------------------
sourceAddress|String|选填，操作源账户地址
destAddress|String|必填，目标账户地址
buAmount|Long|必填，资产发行数量，大小限制[0, max(int64)]
metadata|String|选填，备注

> ContractCreateOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是10.01 BU

   成员变量    |     类型   |        描述          
------------- | --------- | ---------------------
sourceAddress|String|选填，操作源账户地址
initBalance|Long|必填，给合约账户的初始化资产，单位MO，1 BU = 10^8 MO, 大小限制[1, max(int64)]
type|Integer|选填，合约的语种，默认是0
payload|String|必填，对应语种的合约代码
initInput|String|选填，合约代码中init方法的入参
metadata|String|选填，备注

> ContractInvokeByAssetOperation

继承于BaseOperation，feeLimit要根据合约中执行交易来做添加手续费，首先发起交易手续费目前(2018.07.26)是0.01BU，然后合约中的交易也需要交易发起者添加相应交易的手续费
若合约账户不存在，必须先创建合约账户

   成员变量    |     类型   |        描述          
------------- | --------- | ---------------------
sourceAddress|String|选填，操作源账户地址
contractAddress|String|必填，合约账户地址
code|String|选填，资产编码，长度限制[0, 64];当为空时，仅触发合约;
issuer|String|选填，资产发行账户地址，当null时，仅触发合约
assetAmount|Long|选填，资产数量，大小限制[0, max(int64)]，当是0时，仅触发合约
input|String|选填，待触发的合约的main()入参
metadata|String|选填，备注

> ContractInvokeByBUOperation

继承于BaseOperation，feeLimit要根据合约中执行交易来做添加手续费，首先发起交易手续费目前(2018.07.26)是0.01BU，然后合约中的交易也需要交易发起者添加相应交易的手续费

   成员变量    |     类型   |        描述          
------------- | --------- | ---------------------
sourceAddress|String|选填，操作源账户地址
contractAddress|String|必填，合约账户地址
buAmount|Long|选填，资产发行数量，大小限制[0, max(int64)]，当0时仅触发合约
input|String|选填，待触发的合约的main()入参
metadata|String|选填，备注

> LogCreateOperation

继承于BaseOperation，feeLimit目前(2018.07.26)固定是0.01 BU

   成员变量    |     类型   |        描述          
------------- | --------- | ---------------------
sourceAddress|String|选填，操作源账户地址
topic|String|必填，日志主题，长度限制[1, 128]
datas|List<String>|必填，日志内容，每个字符串长度限制[1, 1024]
metadata|String|选填，备注

### buildBlob

> 接口说明

   该接口用于序列化交易，生成交易Blob串，便于网络传输

> 调用方法

```php
/**
 * Serialize the transaction
 * @param TransactionBuildBlobRequest $transactionBuildBlobRequest
 * @return TransactionBuildBlobResponse
 */
public function buildBlob($transactionBuildBlobRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
sourceAddress|String|必填，发起该操作的源账户地址
nonce|Long|必填，待发起的交易序列号，函数里+1，大小限制[1, max(int64)]
gasPrice|Long|必填，交易燃料单价，单位MO，1 BU = 10^8 MO，大小限制[1000, max(int64)]
feeLimit|Long|必填，交易要求的最低的手续费，单位MO，1 BU = 10^8 MO，大小限制[1, max(int64)]
operation|BaseOperation[]|必填，待提交的操作列表，不能为空
ceilLedgerSeq|long|选填，距离当前区块高度指定差值的区块内执行的限制，当区块超出当时区块高度与所设差值的和后，交易执行失败。必须大于等于0，是0时不限制
metadata|String|选填，备注

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
transactionBlob|String|Transaction序列化后的16进制字符串
hash|String|交易hash

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_SOURCEADDRESS_ERROR|11002|Invalid sourceAddress
INVALID_NONCE_ERROR|11048|Nonce must be between 1 and max(int64)
INVALID_DESTADDRESS_ERROR|11003|Invalid destAddress
INVALID_INITBALANCE_ERROR|11004|InitBalance must be between 1 and max(int64) 
SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR|11005|SourceAddress cannot be equal to destAddress
INVALID_ISSUE_AMMOUNT_ERROR|11008|AssetAmount this will be issued must be between 1 and max(int64)
INVALID_DATAKEY_ERROR|11011|The length of key must be between 1 and 1024
INVALID_DATAVALUE_ERROR|11012|The length of value must be between 0 and 256000
INVALID_DATAVERSION_ERROR|11013|The version must be equal to or bigger than 0 
INVALID_MASTERWEIGHT _ERROR|11015|MasterWeight must be between 0 and max(uint32)
INVALID_SIGNER_ADDRESS_ERROR|11016|Invalid signer address
INVALID_SIGNER_WEIGHT _ERROR|11017|Signer weight must be between 0 and max(uint32)
INVALID_TX_THRESHOLD_ERROR|11018|TxThreshold must be between 0 and max(int64)
INVALID_OPERATION_TYPE_ERROR|11019|Operation type must be between 1 and 100
INVALID_TYPE_THRESHOLD_ERROR|11020|TypeThreshold must be between 0 and max(int64)
INVALID_ASSET_CODE _ERROR|11023|The length of key must be between 1 and 64
INVALID_ASSET_AMOUNT_ERROR|11024|AssetAmount must be between 0 and max(int64)
INVALID_BU_AMOUNT_ERROR|11026|BuAmount must be between 0 and max(int64)
INVALID_ISSUER_ADDRESS_ERROR|11027|Invalid issuer address
NO_SUCH_TOKEN_ERROR|11030|No such token
INVALID_TOKEN_NAME_ERROR|11031|The length of token name must be between 1 and 1024
INVALID_TOKEN_SYMBOL_ERROR|11032|The length of symbol must be between 1 and 1024
INVALID_TOKEN_DECIMALS_ERROR|11033|Decimals must be between 0 and 8
INVALID_TOKEN_TOTALSUPPLY_ERROR|11034|TotalSupply must be between 1 and max(int64)
INVALID_TOKENOWNER_ERRPR|11035|Invalid token owner
INVALID_CONTRACTADDRESS_ERROR|11037|Invalid contract address
CONTRACTADDRESS_NOT_CONTRACTACCOUNT_ERROR|11038|ContractAddress is not a contract account
INVALID_TOKEN_AMOUNT_ERROR|11039|Token amount must be between 1 and max(int64)
SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR|11040|SourceAddress cannot be equal to contractAddress
INVALID_FROMADDRESS_ERROR|11041|Invalid fromAddress
FROMADDRESS_EQUAL_DESTADDRESS_ERROR|11042|FromAddress cannot be equal to destAddress
INVALID_SPENDER_ERROR|11043|Invalid spender
PAYLOAD_EMPTY_ERROR|11044|Payload cannot be empty
INVALID_LOG_TOPIC_ERROR|11045|The length of a log topic must be between 1 and 128
INVALID_LOG_DATA_ERROR|11046|The length of one piece of log data must be between 1 and1024
INVALID_CONTRACT_TYPE_ERROR|11047|Type must be equal or bigger than 0 
INVALID_NONCE_ERROR|11048|Nonce must be between 1 and max(int64)
INVALID_ GASPRICE_ERROR|11049|GasPrice must be between 1000 and max(int64)
INVALID_FEELIMIT_ERROR|11050|FeeLimit must be between 1 and max(int64)
OPERATIONS_EMPTY_ERROR|11051|Operations cannot be empty
INVALID_CEILLEDGERSEQ_ERROR|11052|CeilLedgerSeq must be equal to or bigger than 0
OPERATIONS_ONE_ERROR|11053|One of the operations cannot be resolved
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
METADATA_NOT_STRING_ERROR|17001|Metadata must be a string
INPUT_NOT_STRING_ERROR|17002|Input must be a string
INIT_INPUT_NOT_STRING_ERROR|17003|InitInput must be a string
INVALID_REQUEST_ERROR|17004|Request is invalid
INVALID_DELETE_FLAG_ERROR|17005|The deleteFlag is invalid
SIGNERS_NOT_ARRAY_ERROR|17006 |The signers should be an array
INVALID_SIGNER_ERROR|17007|The signer is invalid
TYPE_THRESHOLDS_NOT_ARRAY_ERROR|17008|The typeThresholds should be an array

> 示例

```php
// 初始化变量
$senderAddresss = "buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea";
$destAddress = "buQsurH1M4rjLkfjzkxR9KXJ6jSu2r9xBNEw";
$buAmount = \src\common\Tools::BU2MO(10.9);
$gasPrice = 1000;
$feeLimit = \src\common\Tools::BU2MO(0.01);
$nonce = 1;

// 构建sendBU操作
$operation = new \src\model\request\operation\BUSendOperation();
$operation->setSourceAddress($senderAddresss);
$operation->setDestAddress($destAddress);
$operation->setAmount($buAmount);

// 初始化请求参数
$request = new \src\model\request\TransactionBuildBlobRequest();
$request->setSourceAddress($senderAddresss);
$request->setNonce($nonce);
$request->setFeeLimit($feeLimit);
$request->setGasPrice($gasPrice);
$request->addOperation($operation);

// 调用buildBlob接口
$transactionBlob = null;
$response = $sdk->getTransactionService()->buildBlob($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### evaluateFee

> 接口说明

   该接口实现交易的费用评估

> 调用方法

```php
/**
 * Evaluate the fee of a transaction
 * @param TransactionEvaluateFeeRequest $transactionEvaluateFeeRequest
 * @return TransactionEvaluateFeeResponse
 */
public function evaluateFee($transactionEvaluateFeeRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ----------------  
sourceAddress|String|必填，发起该操作的源账户地址|
nonce|Long|必填，待发起的交易序列号，大小限制[1, max(int64)]|
operation|BaseOperation[]|必填，待提交的操作列表，不能为空|
signtureNumber|Integer|选填，待签名者的数量，默认是1，大小限制[1, max(uint32)]|
ceilLedgerSeq|Long|选填，距离当前区块高度指定差值的区块内执行的限制，当区块超出当时区块高度与所设差值的和后，交易执行失败。必须大于等于0，是0时不限制|
metadata|String|选填，备注|

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
txs     |   [TestTx](#testtx)[]     |  评估交易集   

#### TestTx

   成员变量      |     类型     |        描述       
----------- | ------------ | ---------------- 
transactionEnv| [TestTransactionFees](#testtransactionfees)| 评估交易费用

#### TestTransactionFees

   成员变量      |     类型     |      描述      
----------- | ------------ | ---------------- 
transactionFees|[TransactionFees](#transactionfees)|交易费用

#### TransactionFees
   成员变量      |     类型     |        描述    
----------- | ------------ | ---------------- 
feeLimit|Long|交易要求的最低费用
gasPrice|Long|交易燃料单价

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_SOURCEADDRESS_ERROR|11002|Invalid sourceAddress
INVALID_NONCE_ERROR|11045|Nonce must be between 1 and max(int64)
OPERATIONS_EMPTY_ERROR|11051|Operations cannot be empty
OPERATIONS_ONE_ERROR|11053|One of the operations cannot be resolved
INVALID_SIGNATURENUMBER_ERROR|11054|SignagureNumber must be between 1 and max(uint32)
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid
METADATA_NOT_STRING_ERROR|17001|Metadata must be a string

> 示例

```php
// 初始化变量
$senderAddresss = "buQnnUEBREw2hB6pWHGPzwanX7d28xk6KVcp";
$destAddress = "buQfnVYgXuMo3rvCEpKA6SfRrDpaz8D8A9Ea";
$buAmount = \src\common\Tools::BU2MO(10.9);
$gasPrice = 1000;
$feeLimit = \src\common\Tools::BU2MO(0.01);
$nonce = 51;

// 构建sendBU操作
$buSendOperation = new \src\model\request\operation\BUSendOperation();
$buSendOperation->setSourceAddress($senderAddresss);
$buSendOperation->setDestAddress($destAddress);
$buSendOperation->setAmount($buAmount);

// 初始化评估交易请求参数
$request = new \src\model\request\TransactionEvaluateFeeRequest();
$request->addOperation($buSendOperation);
$request->setSourceAddress($senderAddresss);
$request->setNonce($nonce);
$request->setSignatureNumber(1);
$request->setMetadata(bin2hex("evaluate fees"));

// 调用evaluateFee接口
$response = $sdk->getTransactionService().evaluateFee($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### sign

> 接口说明

   该接口用于实现交易的签名

> 调用方法

```php 
/**
 * Sign a transaction
 * @param TransactionSignRequest $transactionSignRequest
 * @return TransactionSignResponse
 */
public function sign($transactionSignRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blob|String|必填，待签名的交易Blob
privateKeys|String[]|必填，私钥列表


> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
signatures|[Signature](#signature)|签名后的数据列表

#### Signature

   成员变量      |     类型     |        描述       
----------- | ------------ | ---------------- 
  signData|Long|签名后数据
  publicKey|Long|公钥

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOB_ERROR|11056|Invalid blob
PRIVATEKEY_NULL_ERROR|11057|PrivateKeys cannot be empty
PRIVATEKEY_ONE_ERROR|11058|One of privateKeys is invalid
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$issuePrivateKey = "privbyQCRp7DLqKtRFCqKQJr81TurTqG6UKXMMtGAmPG3abcM9XHjWvq";
$transactionBlob = "0A246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370102118C0843D20E8073A56080712246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370522C0A24627551426A4A443142534A376E7A41627A6454656E416870466A6D7852564545746D78481080A9E08704";
$request = new \src\model\request\TransactionSignRequest();
$request->setBlob($transactionBlob);
$request->addPrivateKey($issuePrivateKey);
$response = $sdk->getTransactionService()->sign($request);
if(0 == $response->error_code){
	echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
}else{
	echo "error: " . $response->error_desc;
}
```

### submit

> 接口说明

   该接口用于实现交易的提交。

> 调用方法

```php
/**
 * Submit a transaction to bu chain
 * @param TransactionSubmitRequest $transactionSubmitRequest
 * @return TransactionSubmitResponse
 */
public function submit($transactionSubmitRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
  blob|String|必填，交易blob
  signature|[Signature](#signature)[]|必填，签名列表

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
hash|String|交易hash

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOB_ERROR|11056|Invalid blob
SIGNATURE_EMPTY_ERROR|11067|The signatures cannot be empty
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$transactionBlob = "0A246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370102118C0843D20E8073A56080712246275516E6E5545425245773268423670574847507A77616E5837643238786B364B566370522C0A24627551426A4A443142534A376E7A41627A6454656E416870466A6D7852564545746D78481080A9E08704";
$signature = new Signature();
$signature->setSignData(
  "D2B5E3045F2C1B7D363D4F58C1858C30ABBBB0F41E4B2E18AF680553CA9C3689078E215C097086E47A4393BCA715C7A5D2C180D8750F35C6798944F79CC5000A");
$signature->setPublicKey(
  "b0011765082a9352e04678ef38d38046dc01306edef676547456c0c23e270aaed7ffe9e31477");
$request = new \src\model\request\\src\model\request\TransactionSubmitRequest();
$request->setTransactionBlob($transactionBlob);
$request->addSignature($signature);

// 调用submit接口
$response = $sdk->getTransactionService()->submit($request);
if (0 == $response->error_code) { // 交易提交成功
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else{
    echo "error: " . $response->error_desc;
}
```

### getInfo-交易

> 接口说明

   该接口用于实现根据交易hash查询交易

> 调用方法

```php
/**
 * Get the information of specific block
 * @param TransactionGetInfoRequest $transactionGetInfoRequest
 * @return TransactionGetInfoResponse
 */
function getInfo($transactionGetInfoRequest);
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
hash|String|交易hash

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
totalCount|Long|返回的总交易数
transactions|[TransactionHistory](#transactionhistory)[]|交易内容

#### TransactionHistory

   成员变量  |     类型     |        描述       
----------- | ------------ | ---------------- 
actualFee|String|交易实际费用
closeTime|Long|交易关闭时间
errorCode|Long|交易错误码
errorDesc|String|交易描述
hash|String|交易hash
ledgerSeq|Long|区块序列号
transaction|[TransactionInfo](#transactioninfo)|交易内容列表
signatures|[Signature](#signature)[]|签名列表
txSize|Long|交易大小

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_HASH_ERROR|11055|Invalid transaction hash
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$txHash = "1653f54fbba1134f7e35acee49592a7c29384da10f2f629c9a214f6e54747705";
$request = new \src\model\request\TransactionGetInfoRequest();
$request->setHash(txHash);

// 调用getInfo接口
$response = $sdk->getTransactionService()->getInfo($request);
if ($response->error_code == 0) {
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

## 区块服务

区块服务主要是区块相关的接口，目前有11个接口：getNumber, checkStatus, getTransactions , getInfo, getLatestInfo, getValidators, getLatestValidators, getReward, getLatestReward, getFees, getLatestFees。

### getNumber

> 接口说明

   该接口用于查询最新的区块高度

> 调用方法

```php
/**
 * Get the latest block number
 * @return BlockGetNumberResponse
 */
function getNumber()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
header|BlockHeader|区块头
blockNumber|Long|最新的区块高度，对应底层字段seq

> 错误码

   异常       |     错误码   |   描述  
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用getNumber接口
$response = $sdk->getBlockService()->getNumber();
if(0 == $response->error_code){
	echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
}else{
	echo "error: " . $response->error_desc;
}
```

### checkStatus

> 接口说明

   该接口用于检查本地节点区块是否同步完成

> 调用方法

```php
/**
 * Check the status of block synchronization
 * @return BlockCheckStatusResponse
 */
public function checkStatus()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
isSynchronous    |   Boolean     |  区块是否同步   

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用checkStatus
$response = $sdk->getBlockService()->checkStatus();
if(0 == $response->error_code){
	echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
}else{
	echo "error: " . $response->error_desc;
}
```

### getTransactions

> 接口说明

   该接口用于查询指定区块高度下的所有交易

> 调用方法

```php
/**
 * Get the transactions of specific block
 * @param BlockGetTransactionsRequest $blockGetTransactionsRequest
 * @return BlockGetTransactionsResponse
 */
function getTransactions($blockGetTransactionsRequest)
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockNumber|Long|必填，待查询的区块高度，必须大于0

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
totalCount|Long|返回的总交易数
transactions|[TransactionHistory](#transactionhistory)[]|交易内容

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must bigger than 0
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$blockNumber = 617247;// 第617247区块
$request = new \src\model\request\BlockGetTransactionsRequest();
$request->setBlockNumber($blockNumber);

// 调用getTransactions接口
$response = $sdk->getBlockService()->getTransactions($request);
if(0 == $response->error_code){
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
}else{
    echo "error: " . $response->error_desc;
}
```

### getInfo-区块

> 接口说明

   该接口用于获取区块信息

> 调用方法

```php
/**
 * Get the information of specific block
 * @param BlockGetInfoRequest $blockGetInfoRequest
 * @return BlockGetInfoResponse
 */
function getInfo($blockGetInfoRequest)
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockNumber|Long|必填，待查询的区块高度

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
closeTime|Long|区块关闭时间
number|Long|区块高度
txCount|Long|交易总量
version|String|区块版本

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must bigger than 0
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\BlockGetInfoRequest();
$request->setBlockNumber(629743);

// 调用getInfo接口
$response = $sdk->getBlockService()->getInfo($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getLatestInfo

> 接口说明

   该接口用于获取最新区块信息

> 调用方法

```php
/**
 * Get the latest information of block
 * @return BlockGetLatestInfoResponse
 */
function getLatestInfo()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
closeTime|Long|区块关闭时间
number|Long|区块高度，对应底层字段seq
txCount|Long|交易总量
version|String|区块版本


> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用getLatestInfo接口
$response = $sdk->getBlockService()->getLatestInfo();
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getValidators

> 接口说明

   该接口用于获取指定区块中所有验证节点数

> 调用方法

```php
/**
 * Get the validators of specific block
 * @param  BlockGetValidatorsRequest $blockGetValidatorsRequest
 * @return BlockGetValidatorsResponse
 */
function getValidators($blockGetValidatorsRequest)
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockNumber|Long|必填，待查询的区块高度，必须大于0

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
validators|[ValidatorInfo](#validatorinfo)[]|验证节点列表

#### ValidatorInfo

   成员变量  |     类型     |        描述       
----------- | ------------ | ---------------- 
address|String|共识节点地址
plegeCoinAmount|Long|验证节点押金

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must bigger than 0
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\BlockGetValidatorsRequest();
$request->setBlockNumber(629743);

// 调用getValidators接口
$response = $sdk->getBlockService()->getValidators($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getLatestValidators

> 接口说明

   该接口用于获取最新区块中所有验证节点数

> 调用方法

```php
/**
 * Get the latest validators of block
 * @return BlockGetLatestValidatorsResponse
 */
function getLatestValidators()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
validators|[ValidatorInfo](#validatorinfo)[]|验证节点列表

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用getLatestValidators接口
$response = $sdk->getBlockService()->getLatestValidators();
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getReward

> 接口说明

   该接口用于获取指定区块中的区块奖励和验证节点奖励

> 调用方法

```php
/**
 * Get the reward of specific block
 * @param  BlockGetRewardRequest $blockGetRewardRequest
 * @return BlockGetRewardResponse
 */
function GetReward($blockGetRewardRequest)
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockNumber|Long|必填，待查询的区块高度，必须大于0

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockReward|Long|区块奖励数
validatorsReward|[ValidatorReward](#validatorreward)[]|验证节点奖励情况

#### ValidatorReward

   成员变量  |     类型     |        描述       
----------- | ------------ | ---------------- 
  validator|String|验证节点地址
  reward|Long|验证节点奖励


> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must bigger than 0
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\BlockGetRewardRequest();
$request->setBlockNumber(629743);

// 调用getReward接口
$response = $sdk->getBlockService()->getReward($request);
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getLatestReward

> 接口说明

   获取最新区块中的区块奖励和验证节点奖励

> 调用方法

```php
/**
 * Get the latest reward of block
 * @return BlockGetLatestRewardResponse
 */
function GetLatestReward()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockReward|Long|区块奖励数
validatorsReward|[ValidatorReward](#validatorreward)[]|验证节点奖励情况

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用getLatestReward接口
$response = $sdk->getBlockService()->getLatestReward();
if ($response->error_code == 0) {
    $result = $response->result;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getFees

> 接口说明

   获取指定区块中的账户最低资产限制和燃料单价

> 调用方法

```php
/**
 * Get the fees of specific block
 * @param  BlockGetFeesRequest $blockGetFeesRequest
 * @return BlockGetFeesResponse
 */
function getFees($blockGetFeesRequest)
```

> 请求参数

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
blockNumber|Long|必填，待查询的区块高度，必须大于0

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
fees|[Fees](#fees)|费用

#### Fees

   成员变量  |     类型     |        描述       
----------- | ------------ | ---------------- 
baseReserve|Long|账户最低资产限制
gasPrice|Long|交易燃料单价，单位MO，1 BU = 10^8 MO

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must bigger than 0
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error
INVALID_REQUEST_ERROR|17004|Request is invalid

> 示例

```php
// 初始化请求参数
$request = new \src\model\request\BlockGetFeesRequest();
$request->setBlockNumber(629743L);

// 调用getFees接口
$response = $sdk->getBlockService()->getFees($request);
if ($response->error_code == 0) {
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

### getLatestFees

> 接口说明

   该接口用于获取最新区块中的账户最低资产限制和燃料单价

> 调用方法

```php
/**
 * Get the latest fees of block
 * @return BlockGetLatestFeesResponse
 */
function getLatestFees()
```

> 响应数据

   参数      |     类型     |        描述       
----------- | ------------ | ---------------- 
fees|[Fees](#fees)|费用

> 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
SYSTEM_ERROR|20000|System error

> 示例

```php
// 调用getLatestFees接口
$response = $sdk->getBlockService()->getLatestFees();
if ($response->error_code == 0) {
    echo json_encode($response->result, JSON_UNESCAPED_UNICODE);
} else {
    echo "error: " . $response->error_desc;
}
```

## 错误码

   异常       |     错误码   |   描述   
-----------  | ----------- | -------- 
ACCOUNT_CREATE_ERROR|11001|Failed to create the account 
INVALID_SOURCEADDRESS_ERROR|11002|Invalid sourceAddress
INVALID_DESTADDRESS_ERROR|11003|Invalid destAddress
INVALID_INITBALANCE_ERROR|11004|InitBalance must be between 1 and max(int64) 
SOURCEADDRESS_EQUAL_DESTADDRESS_ERROR|11005|SourceAddress cannot be equal to destAddress
INVALID_ADDRESS_ERROR|11006|Invalid address
CONNECTNETWORK_ERROR|11007|Failed to connect to the network
INVALID_ISSUE_AMOUNT_ERROR|11008|Amount of the token to be issued must be between 1 and max(int64)
NO_ASSET_ERROR|11009|The account does not have the asset
NO_METADATA_ERROR|11010|The account does not have the metadata
INVALID_DATAKEY_ERROR|11011|The length of key must be between 1 and 1024
INVALID_DATAVALUE_ERROR|11012|The length of value must be between 0 and 256000
INVALID_DATAVERSION_ERROR|11013|The version must be equal to or bigger than 0 
INVALID_MASTERWEIGHT_ERROR|11015|MasterWeight must be between 0 and max(uint32)
INVALID_SIGNER_ADDRESS_ERROR|11016|Invalid signer address
INVALID_SIGNER_WEIGHT_ERROR|11017|Signer weight must be between 0 and max(uint32)
INVALID_TX_THRESHOLD_ERROR|11018|TxThreshold must be between 0 and max(int64)
INVALID_OPERATION_TYPE_ERROR|11019|Operation type must be between 1 and 100
INVALID_TYPE_THRESHOLD_ERROR|11020|TypeThreshold must be between 0 and max(int64)
INVALID_ASSET_CODE_ERROR|11023|The length of key must be between 1 and 64
INVALID_ASSET_AMOUNT_ERROR|11024|AssetAmount must be between 0 and max(int64)
INVALID_BU_AMOUNT_ERROR|11026|BuAmount must be between 0 and max(int64)
INVALID_ISSUER_ADDRESS_ERROR|11027|Invalid issuer address
NO_SUCH_TOKEN_ERROR|11030|No such token
INVALID_TOKEN_NAME_ERROR|11031|The length of token name must be between 1 and 1024
INVALID_TOKEN_SIMBOL_ERROR|11032|The length of symbol must be between 1 and 1024
INVALID_TOKEN_DECIMALS_ERROR|11033|Decimals must be between 0 and 8
INVALID_TOKEN_TOTALSUPPLY_ERROR|11034|TotalSupply must be between 1 and max(int64)
INVALID_TOKENOWNER_ERRPR|11035|Invalid token owner
INVALID_CONTRACTADDRESS_ERROR|11037|Invalid contract address
CONTRACTADDRESS_NOT_CONTRACTACCOUNT_ERROR|11038|contractAddress is not a contract account
INVALID_TOKEN_AMOUNT_ERROR|11039|TokenAmount must be between 1 and max(int64)
SOURCEADDRESS_EQUAL_CONTRACTADDRESS_ERROR|11040|SourceAddress cannot be equal to contractAddress
INVALID_FROMADDRESS_ERROR|11041|Invalid fromAddress
FROMADDRESS_EQUAL_DESTADDRESS_ERROR|11042|FromAddress cannot be equal to destAddress
INVALID_SPENDER_ERROR|11043|Invalid spender
PAYLOAD_EMPTY_ERROR|11044|Payload cannot be empty
INVALID_LOG_TOPIC_ERROR|11045|The length of a log topic must be between 1 and 128
INVALID_LOG_DATA_ERROR|11046|The length of one piece of log data must be between 1 and1024
INVALID_CONTRACT_TYPE_ERROR|11047|Invalid contract type
INVALID_NONCE_ERROR|11048|Nonce must be between 1 and max(int64)
INVALID_GASPRICE_ERROR|11049|GasPrice must be between 1000 and max(int64)
INVALID_FEELIMIT_ERROR|11050|FeeLimit must be between 1 and max(int64)
OPERATIONS_EMPTY_ERROR|11051|Operations cannot be empty
INVALID_CEILLEDGERSEQ_ERROR|11052|CeilLedgerSeq must be equal to or bigger than 0
OPERATIONS_ONE_ERROR|11053|One of the operations cannot be resolved
INVALID_SIGNATURENUMBER_ERROR|11054|SignagureNumber must be between 1 and max(uint32)
INVALID_HASH_ERROR|11055|Invalid transaction hash
INVALID_BLOB_ERROR|11056|Invalid blob
PRIVATEKEY_NULL_ERROR|11057|PrivateKeys cannot be empty
PRIVATEKEY_ONE_ERROR|11058|One of privateKeys is invalid
SIGNDATA_NULL_ERROR|11059|SignData cannot be empty
INVALID_BLOCKNUMBER_ERROR|11060|BlockNumber must be bigger than 0
PUBLICKEY_NULL_ERROR|11061|PublicKey cannot be empty
URL_EMPTY_ERROR|11062|Url cannot be empty
CONTRACTADDRESS_CODE_BOTH_NULL_ERROR|11063|ContractAddress and code cannot be empty at the same time
INVALID_OPTTYPE_ERROR|11064|OptType must be between 0 and 2
GET_ALLOWANCE_ERROR|11065|Failed to get allowance
GET_TOKEN_INFO_ERROR|11066|Failed to get token info
SIGNATURE_EMPTY_ERROR|11067|The signatures cannot be empty
REQUEST_NULL_ERROR|12001|Request parameter cannot be null
CONNECTN_BLOCKCHAIN_ERROR|19999|Failed to connect to the blockchain 
SYSTEM_ERROR|20000|System error
METADATA_NOT_STRING_ERROR|17001|Metadata must be a string
INPUT_NOT_STRING_ERROR|17002|Input must be a string
INIT_INPUT_NOT_STRING_ERROR|17003|InitInput must be a string
INVALID_REQUEST_ERROR|17004|Request is invalid
INVALID_DELETE_FLAG_ERROR|17005|The deleteFlag is invalid
SIGNERS_NOT_ARRAY_ERROR|17006 |The signers should be an array
INVALID_SIGNER_ERROR|17007|The signer is invalid
TYPE_THRESHOLDS_NOT_ARRAY_ERROR|17008|The typeThresholds should be an array