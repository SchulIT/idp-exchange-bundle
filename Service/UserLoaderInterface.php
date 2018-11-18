<?php

namespace SchoolIT\IdpExchangeBundle\Service;

interface UserLoaderInterface {
    public function getUsers($limit = null, $offset = null);
}