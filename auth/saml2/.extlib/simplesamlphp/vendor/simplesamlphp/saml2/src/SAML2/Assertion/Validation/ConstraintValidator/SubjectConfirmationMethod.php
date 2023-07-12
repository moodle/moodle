<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation\ConstraintValidator;

use SAML2\Assertion\Validation\Result;
use SAML2\Assertion\Validation\SubjectConfirmationConstraintValidator;
use SAML2\Constants;
use SAML2\XML\saml\SubjectConfirmation;

final class SubjectConfirmationMethod implements SubjectConfirmationConstraintValidator
{
    /**
     * @param SubjectConfirmation $subjectConfirmation
     * @param Result $result
     * @return void
     */
    public function validate(
        SubjectConfirmation $subjectConfirmation,
        Result $result
    ) : void {
        if ($subjectConfirmation->getMethod() !== Constants::CM_BEARER) {
            $result->addError(sprintf(
                'Invalid Method on SubjectConfirmation, current;y only Bearer (%s) is supported',
                Constants::CM_BEARER
            ));
        }
    }
}
