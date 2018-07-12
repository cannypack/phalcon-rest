<?php

namespace PhalconRest;

class Exception extends \Exception
{
    protected $developerInfo;
    protected $userInfo;
    protected $report;

    public function __construct($code, $message = null, $developerInfo = null, $userInfo = null, $report = true)
    {
        parent::__construct($message, $code);

        $this->developerInfo = $developerInfo;
        $this->userInfo = $userInfo;
        $this->report = $report;
    }

    public function getDeveloperInfo()
    {
        return $this->developerInfo;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function shouldReport()
    {
        return $this->report;
    }
}
