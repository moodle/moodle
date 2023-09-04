<?php

declare(strict_types=1);

namespace SAML2\Response\Validation\ConstraintValidator;

use SAML2\Constants;
use SAML2\Response;
use SAML2\Response\Validation\ConstraintValidator;
use SAML2\Response\Validation\Result;

class IsSuccessful implements ConstraintValidator
{
    /**
     * @param \SAML2\Response $response
     * @param \SAML2\Response\Validation\Result $result
     * @return void
     */
    public function validate(
        Response $response,
        Result $result
    ) : void {
        if (!$response->isSuccess()) {
            $result->addError($this->buildMessage($response->getStatus()));
        }
    }


    /**
     * @param array $responseStatus
     *
     * @return string
     */
    private function buildMessage(array $responseStatus) : string
    {
        return sprintf(
            '%s%s%s',
            $this->truncateStatus($responseStatus['Code']),
            $responseStatus['SubCode'] ? '/'.$this->truncateStatus($responseStatus['SubCode']) : '',
            $responseStatus['Message'] ? ' '.$responseStatus['Message'] : ''
        );
    }


    /**
     * Truncate the status if it is prefixed by its urn.
     * @param string $status
     *
     * @return string
     */
    private function truncateStatus(string $status) : string
    {
        $prefixLength = strlen(Constants::STATUS_PREFIX);
        if (strpos($status, Constants::STATUS_PREFIX) !== 0) {
            return $status;
        }

        return substr($status, $prefixLength);
    }
}
