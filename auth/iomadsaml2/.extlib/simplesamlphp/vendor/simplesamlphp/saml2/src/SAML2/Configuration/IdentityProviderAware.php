<?php

declare(strict_types=1);

namespace SAML2\Configuration;

/**
 * Interface for triggering setter injection
 */
interface IdentityProviderAware
{
    /**
     * @param IdentityProvider $identityProvider
     *
     * @return void
     */
    public function setIdentityProvider(IdentityProvider $identityProvider) : void;
}
