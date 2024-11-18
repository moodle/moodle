<?php

declare(strict_types=1);

namespace SAML2\Assertion\Validation\ConstraintValidator;

use Webmozart\Assert\Assert;

use SAML2\Assertion\Validation\Result;
use SAML2\Assertion\Validation\SubjectConfirmationConstraintValidator;
use SAML2\Response;
use SAML2\XML\saml\SubjectConfirmation;

class SubjectConfirmationResponseToMatches implements
    SubjectConfirmationConstraintValidator
{
    /** @var Response */
    private $response;


    /**
     * Constructor for SubjectConfirmationResponseToMatches
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }


    /**
     * @param \SAML2\XML\saml\SubjectConfirmation $subjectConfirmation
     * @param Result $result
     * @return void
     */
    public function validate(SubjectConfirmation $subjectConfirmation, Result $result) : void
    {
        $data = $subjectConfirmation->getSubjectConfirmationData();
        Assert::notNull($data);

        /** @psalm-suppress PossiblyNullReference */
        $inResponseTo = $data->getInResponseTo();
        if ($inResponseTo && ($this->getInResponseTo() !== false) && ($this->getInResponseTo() !== $inResponseTo)) {
            $result->addError(sprintf(
                'InResponseTo in SubjectConfirmationData ("%s") does not match the Response InResponseTo ("%s")',
                $inResponseTo,
                strval($this->getInResponseTo())
            ));
        }
    }


    /**
     * @return string|bool
     */
    private function getInResponseTo()
    {
        $inResponseTo = $this->response->getInResponseTo();
        if ($inResponseTo === null) {
            return false;
        }

        return $inResponseTo;
    }
}
