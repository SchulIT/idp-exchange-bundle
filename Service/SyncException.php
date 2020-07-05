<?php

namespace SchulIT\IdpExchangeBundle\Service;

use Throwable;

class SyncException extends \Exception {
    public function __construct(Throwable $previous = null) {
        parent::__construct('', 0, $previous);
    }
}