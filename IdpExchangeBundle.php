<?php

namespace SchoolIT\IdpExchangeBundle;

use SchoolIT\IdpExchangeBundle\DependencyInjection\IdpExchangeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IdpExchangeBundle extends Bundle {
    public function getContainerExtension() {
        return new IdpExchangeExtension();
    }
}