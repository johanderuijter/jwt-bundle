<?php

namespace JDR\JWTBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JDR\JWTBundle\DependencyInjection\JDRJWTExtension;

class JDRJWTBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new JDRJWTExtension();
    }
}
