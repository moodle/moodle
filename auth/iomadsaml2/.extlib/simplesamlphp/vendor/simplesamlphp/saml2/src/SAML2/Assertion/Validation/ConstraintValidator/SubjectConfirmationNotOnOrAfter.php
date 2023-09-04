<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation\ConstraintValidator;

use SAML2\Assertion\Validation\Result;
use SAML2\Assertion\Validation\SubjectConfirmationConstraintValidator;
use SAML2\Utilities\Temporal;
use SAML2\XML\saml\SubjectConfirmation;
use Webmozart\Assert\Assert;

class SubjectConfirmationNotOnOrAfter implements
    SubjectConfirmationConstraintValidator
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
        $data = $subjectConfirmation->getSubjectConfirmationData();
        Assert::notNull($data);

        /** @psalm-suppress PossiblyNullReference */
        $notOnOrAfter = $data->getNotOnOrAfter();
        if ($notOnOrAfter && $notOnOrAfter <= Temporal::getTime() - 60) {
            $result->addError('NotOnOrAfter in SubjectConfirmationData is in the past');
        }
    }
}
