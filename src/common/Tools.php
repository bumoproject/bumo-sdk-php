<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\common;

use \src\crypto\JsonMapper\JsonMapper;
use \src\exception\SDKException;

class Tools{ 
    /**
     * [isEmpty description]
     * @param  [type]  $info [description]
     * @return boolean       [description]
     */
   static public function isEmpty($info){
        if(empty($info)) {
            return true;
        }
        else {
            return false;
        }   
   }

   /**
    * [isNULL description]
    * @param  [type]  $info [description]
    * @return boolean       [description]
    */
   static public function isNULL($info){
        if(!isset($info)  || is_null($info)) {
            return true;
        }
        else {
            return false;
        }   
   }

    /**
     * unitWithoutDecimals Change unitWithDecimals to unitWithoutDecimals
     * @param  [string]  $unitWithDecimals
     * @param  [int]  $decimals
     * @return int
     */
   static public function unitWithoutDecimals($unitWithDecimals, $decimals){
       if (!is_string($unitWithDecimals) || !is_numeric($unitWithDecimals) || bccomp($unitWithDecimals, '0') < 0 ||
           is_string($decimals) || !is_int($decimals) || $decimals > 18 || $decimals < 0) {
           return false;
       }
       $unitWithoutDecimals = (int)bcmul($unitWithDecimals, pow(10, $decimals));
       return $unitWithoutDecimals;
   }

    /**
     * unitWithDecimals Change $unitWithoutDecimals to unitWithDecimals
     * @param  [string]  $unitWithoutDecimals
     * @param  [int]  $decimals
     * @return string
     */
   static public function unitWithDecimals($unitWithoutDecimals, $decimals){
       if (is_string($unitWithoutDecimals) || !is_int($unitWithoutDecimals) || $unitWithoutDecimals < 0 ||
           is_string($decimals) || !is_int($decimals) || $decimals > 18 || $decimals < 0) {
           return false;
       }
       $bu = bcdiv($unitWithoutDecimals, pow(10, $decimals), $decimals);
       return $bu;
   }

    /**
     * BU2MO Change bu to mo
     * @param  [string]  $bu
     * @return int
     */
   static public function BU2MO($bu) {
        return Tools::unitWithoutDecimals($bu, 8);
   }

    /**
     * MO2BU Change mo to bu
     * @param  [int]  $mo
     * @return string
     */
   static public function MO2BU($mo) {
       return Tools::unitWithDecimals($mo, 8);
   }

   static public function jsonToClass($json, $class) {
       try {
           $mapper = new JsonMapper();
           $mapper->bStrictNullTypes = false;
           $resultObject = json_decode($json);
           $classContent = $mapper->map($resultObject, $class);
       }
       catch (\Exception $exception) {
           throw new SDKException("SYSTEM", $exception->getMessage());
       }
       return $classContent;
   }

   static public function jsonArrayToClassArray($jsonArray, $className) {
       try {
           $mapper = new JsonMapper();
           $mapper->bStrictNullTypes = false;
           $resultObject = json_decode($jsonArray);
           $classContent = $mapper->mapArray($resultObject, array(), $className);
       }
       catch (\Exception $exception) {
           throw new SDKException("SYSTEM", $exception->getMessage());
       }
       return $classContent;
   }
}