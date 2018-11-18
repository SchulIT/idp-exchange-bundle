<?php

namespace SchoolIT\IdpExchangeBundle\Service;

class SynchronizationResult {

    /**
     * @var boolean
     */
    private $isSuccessful;

    /**
     * @var string[]
     */
    private $users;

    /**
     * @var \Exception|null
     */
    private $exception = null;

    public function __construct(bool $isSuccessful, array $users, \Exception $exception = null) {
        $this->isSuccessful = $isSuccessful;
        $this->users = $users;
        $this->exception = $exception;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool {
        return $this->isSuccessful;
    }

    /**
     * @return string[]
     */
    public function getUsers(): array {
        return $this->users;
    }

    /**
     * @return \Exception|null
     */
    public function getException(): ?\Exception {
        return $this->exception;
    }
}