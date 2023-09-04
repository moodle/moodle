<?php

declare(strict_types=1);

namespace SAML2\Assertion\Transformer;

use SAML2\Assertion;
use SAML2\Configuration\IdentityProvider;
use SAML2\Configuration\IdentityProviderAware;
use SAML2\Configuration\ServiceProvider;
use SAML2\Configuration\ServiceProviderAware;

class TransformerChain implements Transformer
{
    /**
     * @var \SAML2\Assertion\Transformer\Transformer[]
     */
    private $transformers = [];

    /**
     * @var \SAML2\Configuration\IdentityProvider
     */
    private $identityProvider;

    /**
     * @var \SAML2\Configuration\ServiceProvider
     */
    private $serviceProvider;


    /**
     * Constructor for TransformerChain
     *
     * @param IdentityProvider $identityProvider
     * @param ServiceProvider $serviceProvider
     */
    public function __construct(
        IdentityProvider $identityProvider,
        ServiceProvider $serviceProvider
    ) {
        $this->identityProvider = $identityProvider;
        $this->serviceProvider  = $serviceProvider;
    }


    /**
     * @param Transformer $transformer
     * @return void
     */
    public function addTransformerStep(Transformer $transformer) : void
    {
        if ($transformer instanceof IdentityProviderAware) {
            $transformer->setIdentityProvider($this->identityProvider);
        }

        if ($transformer instanceof ServiceProviderAware) {
            $transformer->setServiceProvider($this->serviceProvider);
        }

        $this->transformers[] = $transformer;
    }


    /**
     * @param \SAML2\Assertion $assertion
     *
     * @return \SAML2\Assertion
     */
    public function transform(Assertion $assertion) : Assertion
    {
        foreach ($this->transformers as $transformer) {
            $assertion = $transformer->transform($assertion);
        }

        return $assertion;
    }
}
