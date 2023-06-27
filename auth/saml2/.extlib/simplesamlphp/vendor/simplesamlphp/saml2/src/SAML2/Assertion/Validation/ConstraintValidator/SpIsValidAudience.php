<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation\ConstraintValidator;

use SAML2\Assertion;
use SAML2\Assertion\Validation\AssertionConstraintValidator;
use SAML2\Assertion\Validation\Result;
use SAML2\Configuration\ServiceProvider;
use SAML2\Configuration\ServiceProviderAware;

class SpIsValidAudience implements
    AssertionConstraintValidator,
    ServiceProviderAware
{
    /**
     * @var \SAML2\Configuration\ServiceProvider
     */
    private $serviceProvider;


    /**
     * @param ServiceProvider $serviceProvider
     * @return void
     */
    public function setServiceProvider(ServiceProvider $serviceProvider) : void
    {
        $this->serviceProvider = $serviceProvider;
    }


    /**
     * @param Assertion $assertion
     * @param Result $result
     * @return void
     */
    public function validate(Assertion $assertion, Result $result) : void
    {
        $intendedAudiences = $assertion->getValidAudiences();
        if ($intendedAudiences === null) {
            return;
        }

        $entityId = $this->serviceProvider->getEntityId();
        if (!in_array($entityId, $intendedAudiences, true)) {
            $result->addError(sprintf(
                'The configured Service Provider [%s] is not a valid audience for the assertion. Audiences: [%s]',
                strval($entityId),
                implode('], [', $intendedAudiences)
            ));
        }
    }
}
