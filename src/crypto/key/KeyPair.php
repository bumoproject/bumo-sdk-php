<?php
/**
 * Created by PhpStorm.
 * User: dw
 * Date: 2018/8/9
 * Time: 18:02
 */
namespace src\crypto\key;
use src\crypto\base58\Base58;

class KeyPair
{
    /**
     * construct: Create a new key pair
     * @param null
     * @return null
     */
    public function __construct()
    {
        $this->createKeyPair();
    }

    /**
     * getEncPrivateKey: Create an encode private key
     * @param null
     * @return string: encode private key
     */
    public function getEncPrivateKey() {
        $firstString = $this->rawPrivateKey;
        return KeyPair::encodePrivateKey($firstString);
    }

    /**
     * getEncPublicKey: Create an encode public key
     * @param null
     * @return string: encode public key
     */
    public function getEncPublicKey() {
        $firstString = $this->rawPublicKey;
        return KeyPair::encodePublicKey($firstString);
    }

    /**
     * getEncPublicKeyByPrivateKey (static): Create an encode public key by an encode private key
     * @param string: encode private key
     * @return string: encode public key
     */
    public static function getEncPublicKeyByPrivateKey($privateKey) {
        $rawPrivateKey = KeyPair::decodePrivateKey($privateKey);
        if (!$rawPrivateKey) {
            return false;
        }
        $firstString = ed25519_publickey($rawPrivateKey);
        return KeyPair::encodePublicKey($firstString);
    }

    /**
     * getEncAddress: Create an encode address
     * @param null
     * @return string: encode address
     */
    public function getEncAddress() {
        $firstString = $this->rawPublicKey;
        return KeyPair::encodeAddress($firstString);
    }

    /**
     * getEncAddressByPublicKey (static): Create an encode address by an encode public key
     * @param string: encode public key
     * @return string: encode address
     */
    public static function getEncAddressByPublicKey($publicKey) {
        $rawPublicKey = KeyPair::decodePublicKey($publicKey);
        if (!$rawPublicKey) {
            return false;
        }
        return KeyPair::encodeAddress($rawPublicKey);
    }

    /**
     * getEncAddressByPrivateKey (static): Create an encode address by an encode private key
     * @param string: encode private key
     * @return string: encode address
     */
    public static function getEncAddressByPrivateKey($privateKey) {
        $rawPrivateKey = KeyPair::decodePrivateKey($privateKey);
        if (!$rawPrivateKey) {
            return false;
        }

        $firstString = ed25519_publickey($rawPrivateKey);
        return KeyPair::encodeAddress($firstString);
    }

    /**
     * isAddressValid (static): Check the validity of an encode address
     * @param string: encode address
     * @return boolean: true or false
     */
    public static function isAddressValid($address) {
        if(!$address){
            return false;
        }
        $addressRet = Base58::decode($address);
        if (!$addressRet) {
            return false;
        }
        $addressByteArr = KeyPair::getBytes($addressRet);
        if (strlen($addressRet) != 27 || $addressByteArr[0] != 0x01 || $addressByteArr[1] != 0x56
            || $addressByteArr[2] != 1) {
            return false;
        }
        $len = strlen($addressRet);
        $checkSum = substr($addressRet,$len-4);
        $newBuff = substr($addressRet,0,$len-4);
        $firstHash = hash('sha256', $newBuff,true);
        $secondHash = hash('sha256', $firstHash,true);
        $hashData = substr($secondHash, 0,4);
        if($checkSum==$hashData){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * isPublicKeyValid (static): Check the validity of an encode public key
     * @param string: encode public key
     * @return boolean: true or false
     */
    public static function isPublicKeyValid($publicKey){
        $rawPublicKey = KeyPair::decodePublicKey($publicKey);
        if (!$rawPublicKey) {
            return false;
        }
        return true;
    }

    /**
     * isPrivateKeyValid (static): Check the validity of an encode private key
     * @param string: encode private key
     * @return boolean: true or false
     */
    public static function isPrivateKeyValid($privateKey) {
        $rawPrivateKey = KeyPair::decodePrivateKey($privateKey);
        if (!$rawPrivateKey) {
            return false;
        }
        return true;
    }

    /**
     * sign: sign a message
     * @param string or bytes: a message to be signed
     * @return array: the signed data
     */
    public function sign($message) {
        $signature = ed25519_sign($message, $this->rawPrivateKey, $this->rawPublicKey);
        return $signature;
    }

    /**
     * signByPrivateKey (static): sign a message by an encode private key
     * @param string or bytes: a message to be signed
     * @param string: encode private key
     * @return boolean or bytes: the signed data
     */
    public static function signByPrivateKey($message, $privateKey) {
        $rawPrivateKey = KeyPair::decodePrivateKey($privateKey);
        if (!$rawPrivateKey) {
            return false;
        }
        $rawPublicKey = ed25519_publickey($rawPrivateKey);
        $signature = ed25519_sign($message, $rawPrivateKey, $rawPublicKey);
        return $signature;
    }

    /**
     * verify: verify a signature
     * @param string or bytes: the message that has been signed
     * @param array: the signed data
     * @return boolean: true or false
     */
    public function verify($message, $signature) {
        $status = ed25519_sign_open($message,  $this->rawPublicKey, $signature);
        if($status){
            return true;
        }
        return false;
    }

    /**
     * verifyByPublicKey (static): verify a signature by an encode public key
     * @param string or bytes: the message that has been signed
     * @param string: encode public key
     * @param array: the signed data
     * @return boolean: true or false
     */
    public static function verifyByPublicKey($message, $publicKey, $signature) {
        $rawPublicKey = KeyPair::decodePublicKey($publicKey);
        if (!$rawPublicKey) {
            return false;
        }
        $status = ed25519_sign_open($message,  $rawPublicKey, $signature);
        if($status){
            return true;
        }
        return false;
    }

    private function createKeyPair() {
        $strong = true;
        $this->rawPrivateKey = openssl_random_pseudo_bytes(32, $strong);
        $this->rawPublicKey  = ed25519_publickey($this->rawPrivateKey);
    }

    private static function encodePrivateKey($rawPrivateKey) {
        $secondString = chr(1) . $rawPrivateKey;
        $thirdString = chr(218) . chr(55) .chr(159) .$secondString;
        $fourthString = $thirdString . chr(0);
        $fifthString_1 = hash('sha256', $fourthString,true);
        $fifthString_2 = hash('sha256', $fifthString_1,true);
        $fifthString = $fourthString.substr($fifthString_2, 0,4);
        $lastString = Base58::encode($fifthString);
        return $lastString;
    }

    private static function encodePublicKey($rawPublicKey) {
        $secondString = chr(1) . $rawPublicKey;
        $thirdString = chr(176) .$secondString;
        $thirdString_1 = hash('sha256', $thirdString,true);
        $thirdString_2 = hash('sha256', $thirdString_1,true);
        $fourthString = $thirdString . substr($thirdString_2, 0,4);
        $lastString = bin2hex($fourthString);
        return $lastString;
    }

    private static function encodeAddress($rawPublicKey) {
        $second_1 = hash('sha256', $rawPublicKey,true);
        $secondString = substr($second_1, 12);
        $thirdString = chr(1) . $secondString;
        $fourthString = chr(1) . chr(86) .$thirdString;
        $fifthString_1 = hash('sha256', $fourthString,true);
        $fifthString_2 = hash('sha256', $fifthString_1,true);
        $fifthString = $fourthString . substr($fifthString_2, 0,4);
        $lastString = Base58::encode($fifthString);
        return $lastString;
    }

    private static function decodePrivateKey($encPrivateKey) {
        if(!$encPrivateKey){
            return false;
        }
        $buffString = Base58::decode($encPrivateKey);
        if (!$buffString) {
            return false;
        }
        $buffStringArray = KeyPair::getBytes($buffString);
        if (strlen($buffString) != 41 || $buffStringArray[0] != 0xda || $buffStringArray[1] != 0x37 ||
            $buffStringArray[2] != 0x9f || $buffStringArray[3] != 1 || $buffStringArray[36] != 0) {
            return false;
        }
        // checksum
        $len = strlen($buffString);
        $checkSum  = substr($buffString, $len-4);
        $buff = substr($buffString, 0,$len - 4);
        $firstHash = hash('sha256', $buff,true);
        $secondHash = hash('sha256', $firstHash,true);
        $hash2 = substr($secondHash,0,4);
        if($checkSum== $hash2){
            $rawPriKey = substr($buffString,4,32);
            return $rawPriKey;
        }
        else{
            return false;
        }
    }

    private static function decodePublicKey($encPublicKey) {
        // not null
        if(!$encPublicKey || !is_string($encPublicKey))
            return false;
        // prefix
        $buffString = hex2bin($encPublicKey);
        if (!$buffString) {
            return false;
        }
        $buffStringArray = KeyPair::getBytes($buffString);
        if (strlen($buffString) != 38 || $buffStringArray[0] != 0xb0 || $buffStringArray[1] != 1) {
            return false;
        }
        // checksum
        $len = strlen($buffString);
        $checkSum  = substr($buffString, $len-4);
        $buff = substr($buffString, 0,$len - 4);
        $firstHash = hash('sha256', $buff,true);
        $secondHash = hash('sha256', $firstHash,true);
        $hash2 = substr($secondHash,0,4);
        if($checkSum == $hash2){
            $rawPubKey = substr($buffString,2,32);
            return $rawPubKey;
        }
        else{
            return false;
        }
    }

    private static function getBytes($string) {
        $bytes = array();
        for($i = 0; $i < strlen($string); $i++){
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }
}
?>