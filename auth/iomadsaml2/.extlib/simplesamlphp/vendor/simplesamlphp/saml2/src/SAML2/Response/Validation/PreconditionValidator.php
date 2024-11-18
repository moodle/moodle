<?php

declare(strict_types=1);

namespace SAML2\Response\Validation;

use SAML2\Configuration\Destination;
use SAML2\Response\Validation\ConstraintValidator\DestinationMatches;
use SAML2\Response\Validation\ConstraintValidator\IsSuccessful;

/**
 * Validates the preconditions that have to be met prior to processing of the response.
 */
class PreconditionValidator extends Validator
{
    /**
     * Constructor for PreconditionValidator
     *
     * @param Destination $destination
     */
    public function __construct(Destination $destination)
    {
        // move to DI
        $this->addConstraintValidator(new IsSuccessful());
        $this->addConstraintValidator(
            new DestinationMatches($destination)
        );
    }
}
