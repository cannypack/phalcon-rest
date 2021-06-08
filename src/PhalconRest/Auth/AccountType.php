<?php

namespace PhalconRest\Auth;

interface AccountType
{
    /**
     * @param array $data Login data
     *
     * @return string Identity
     */
    public function login($data);

    /**
     * @param string $identity       Identity
     * @param string $token          Token
     * @param int    $expirationTime Token expiration time
     *
     * @return void
     */
    public function saveToken($identity, $token, $expirationTime);

    /**
     * @param string $identity Identity
     *
     * @return bool Authentication successful
     */
    public function authenticate($identity);

    /**
     * @param string $identity Identity
     * @param string $token    Token
     *
     * @return bool Authentication successful
     */
    public function authenticateToken($identity, $token);

    /**
     * @return int Expiration time of sessions
     */
    public function getSessionDuration();

    /**
     * @param int $time Expiration time of sessions
     *
     * @return void
     */
    public function setSessionDuration($time)
}
