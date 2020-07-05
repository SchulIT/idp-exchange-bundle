<?php

namespace SchulIT\IdpExchangeBundle\Service;

use SchulIT\IdpExchange\Response\UserResponse;

interface UserUpdaterInterface {
    public function startTransaction();

    public function updateUser(UserResponse $response);

    public function commit();
}