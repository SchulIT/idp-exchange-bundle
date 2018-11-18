<?php

namespace SchoolIT\IdpExchangeBundle\Service;

use SchoolIT\IdpExchange\Response\UserResponse;

interface UserUpdaterInterface {
    public function startTransaction();

    public function updateUser(UserResponse $response);

    public function commit();
}