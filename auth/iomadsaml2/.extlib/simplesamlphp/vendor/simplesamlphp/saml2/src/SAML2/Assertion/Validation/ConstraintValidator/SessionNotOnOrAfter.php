<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation\ConstraintValidator;

use SAML2\Assertion;
use SAML2\Assertion\Validation\AssertionConstraintValidator;
use SAML2\Assertion\Validation\Result;
use SAML2\Utilities\Temporal;

class SessionNotOnOrAfter implements
    AssertionConstraintValidator
{
    /**
     * @param Assertion $assertion
     * @param Result $result
     * @return void
     */
    public function validate(Assertion $assertion, Result $result) : void
    {
        $sessionNotOnOrAfterTimestamp = $assertion->getSessionNotOnOrAfter();
        $currentTime = Temporal::getTime();
        if (($sessionNotOnOrAfterTimestamp !== null) && ($sessionNotOnOrAfterTimestamp <= ($currentTime - 60))) {
            $result->addError(
                'Received an assertion with a session that has expired. Check clock synchronization on IdP and SP.'
            );
        }
    }
}
