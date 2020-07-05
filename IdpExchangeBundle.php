<?php

namespace SchulIT\IdpExchangeBundle;

use SchulIT\IdpExchangeBundle\DependencyInjection\IdpExchangeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IdpExchangeBundle extends Bundle {
    public function getContainerExtension() {
        return new IdpExchangeExtension();
    }
}