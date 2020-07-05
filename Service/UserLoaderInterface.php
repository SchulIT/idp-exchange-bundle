<?php

namespace SchulIT\IdpExchangeBundle\Service;

interface UserLoaderInterface {
    public function getUsers($limit = null, $offset = null);
}