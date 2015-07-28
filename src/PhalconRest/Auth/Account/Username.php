<?php

namespace PhalconRest\Auth\Account;

use PhalconRest\Constants\Services as PhalconRestServices;
use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Username extends \Phalcon\Mvc\User\Plugin implements \PhalconRest\Auth\Account
{
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setUserModel(\Phalcon\Mvc\Model $userModel)
    {
        $this->userModel = get_class($userModel);
    }

    public function setUsernameAccountModel(\Phalcon\Mvc\Model $usernameAccountModel)
    {
        $this->usernameAccountModel = get_class($usernameAccountModel);
    }

    public function setMailService($mailService)
    {
        $this->mailService = $this->di->get($mailService);
    }

    public function register($data)
    {
        $userModel = $this->userModel;
        $usernameAccountModel = $this->usernameAccountModel;
        $authManager = $this->di->get(PhalconRestServices::AUTH_MANAGER);

        $db = $this->di->get('db');

        if (!isset($data->name) && !isset($data->email) || !isset($data->username) || !isset($data->password)) {

            throw new UserException(ErrorCodes::DATA_INVALID);
        }

        $user = $userModel::findFirstByEmail($data->email);

        // When user already exists with username account
        if ($user && $user->getAccount($this->name)) {

            throw new UserException(ErrorCodes::DATA_DUPLICATE, 'User already exists.');
        }

        // Check if perhaps username already exists
        $usernameAccount = $usernameAccountModel::findFirstByUsername($data->username);

        if ($usernameAccount) {

            throw new UserException(ErrorCodes::DATA_DUPLICATE, 'Username already exists');
        }

        // Let's create username account
        $usernameAccount = new $usernameAccountModel();
        $usernameAccount->username = $data->username;
        $usernameAccount->password = $this->security->hash($data->password);

        // If user already exists, this stays false in the
        // next check so there will not be sent an activation mail.
        $sendActivationMail = false;

        // Here we start a transaction, because we are possibly executing
        // multiple queries. If one fails, we simply rollback all the queries
        // so there won't be any data inconsistency
        $db->begin();

        try {

            // If there's no user yet, first create one.
            if (!$user) {

                $sendActivationMail = true;

                $mailToken = $authManager->createMailToken();

                $user = new $this->userModel();
                $user->name = $data->name;
                $user->email = $data->email;

                // By default, user is not active.
                // They need to click the mailToken they get sent by mail
                $user->active = 0;
                $user->mailToken = $mailToken;
            }

            $user->usernameAccount = $usernameAccount;

            if (!$user->save()) {

                throw new \Exception('User could not be registered.');
            }

            if ($sendActivationMail) {

                // Send a mail where they can activate their account
                $sent = $this->mailService->sendActivationMail($user, $usernameAccount);

                if (!$sent) {

                    throw new \Exception('User #' . $user->id . ' was created, but Activation mail could not be sent. So changes have been rolled back.');
                }
            }

            // Everything has gone to plan, let's commit those changes!
            $db->commit();

        } catch (\Exception $e) {

            $db->rollback();
            throw new UserException(ErrorCodes::USER_CREATEFAIL, $e->getMessage());
        }

        return $user;
    }

    public function login($username = null, $password = null)
    {
        $usernameAccountModel = $this->usernameAccountModel;

        $usernameAccount = $usernameAccountModel::findFirstByUsername($username);

        // Check if password is valid
        if (!$usernameAccount || !$usernameAccount->validatePassword($password)) {
            return false;
        }

        // Something is terribly wrong, can't find the real user
        if (!$user = $usernameAccount->user) {
            return false;
        }

        if ($usernameAccount->user->active != 1) {

            throw new UserException(ErrorCodes::USER_NOTACTIVE, 'User should be activated first');
        }

        return $user;
    }
}
