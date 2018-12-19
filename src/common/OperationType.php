<?php
/**
 * @author zjl <[<email address>]>
 */
namespace src\common;

class OperationType {
    // Unknown operation
    const UNKNOWN = 0;

    // Activate an account
    const ACCOUNT_ACTIVATE = 1;

    // Set metadata
    const ACCOUNT_SET_METADATA = 2;

    // Set privilege
    const ACCOUNT_SET_PRIVILEGE = 3;

    // Issue token
    const ASSET_ISSUE = 4;

    // Send token
    const ASSET_SEND = 5;

    // Send bu
    const BU_SEND = 6;

    // Issue token
    const TOKEN_ISSUE = 7;

    // Transfer token
    const TOKEN_TRANSFER = 8;

    // Transfer token from an account
    const TOKEN_TRANSFER_FROM = 9;

    // Approve token
    const TOKEN_APPROVE = 10;

    // Assign token
    const TOKEN_ASSIGN = 11;

    // Change owner of token
    const TOKEN_CHANGE_OWNER = 12;

    // Create contract
    const CONTRACT_CREATE = 13;

    // Invoke contract by sending token
    const CONTRACT_INVOKE_BY_ASSET = 14;

    // Invoke contract by sending bu
    const CONTRACT_INVOKE_BY_BU = 15;

    // Create log
    const LOG_CREATE = 16;
}

?>