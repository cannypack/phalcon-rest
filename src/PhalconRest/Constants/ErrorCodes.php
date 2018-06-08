<?php

namespace PhalconRest\Constants;

class ErrorCodes
{
    // General
    const GENERAL_SYSTEM = 1010;
    const GENERAL_NOT_IMPLEMENTED = 1020;
    const GENERAL_NOT_FOUND = 1030;

    // Authentication
    const AUTH_INVALID_ACCOUNT_TYPE = 2010;
    const AUTH_LOGIN_FAILED = 2020;
    const AUTH_TOKEN_INVALID = 2030;
    const AUTH_SESSION_EXPIRED = 2040;
    const AUTH_SESSION_INVALID = 2050;
    const AUTH_LOGIN_BLOCKED = 2060;
    const AUTH_LOGIN_INACTIVE = 2070;

    // Access Control
    const ACCESS_DENIED = 3010;

    // Data
    const DATA_FAILED = 4010;
    const DATA_NOT_FOUND = 4020;

    const POST_DATA_NOT_PROVIDED = 5010;
    const POST_DATA_INVALID = 5020;
}
