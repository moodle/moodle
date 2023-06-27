<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation;

use SAML2\Assertion;
use SAML2\Configuration\IdentityProvider;
use SAML2\Configuration\IdentityProviderAware;
use SAML2\Configuration\ServiceProvider;
use SAML2\Configuration\ServiceProviderAware;

class AssertionValidator
{
    /**
     * @var \SAML2\Assertion\Validation\AssertionConstraintValidator[]
     */
    protected $constraints;

    /**
     * @var \SAML2\Configuration\IdentityProvider
     */
    private $identityProvider;

    /**
     * @var \SAML2\Configuration\ServiceProvider
     */
    private $serviceProvider;


    /**
     * @param \SAML2\Configuration\IdentityProvider $identityProvider
     * @param \SAML2\Configuration\ServiceProvider  $serviceProvider
     */
    public function __construct(
        IdentityProvider $identityProvider,
        ServiceProvider $serviceProvider
    ) {
        $this->identityProvider = $identityProvider;
        $this->serviceProvider = $serviceProvider;
    }


    /**
     * @param AssertionConstraintValidator $constraint
     * @return void
     */
    public function addConstraintValidator(AssertionConstraintValidator $constraint) : void
    {
        if ($constraint instanceof IdentityProviderAware) {
            $constraint->setIdentityProvider($this->identityProvider);
        }

        if ($constraint instanceof ServiceProviderAware) {
            $constraint->setServiceProvider($this->serviceProvider);
        }

        $this->constraints[] = $constraint;
    }


    /**
     * @param Assertion $assertion
     * @return Result
     */
    public function validate(Assertion $assertion) : Result
    {
        $result = new Result();
        foreach ($this->constraints as $validator) {
            $validator->validate($assertion, $result);
        }

        return $result;
    }
}
