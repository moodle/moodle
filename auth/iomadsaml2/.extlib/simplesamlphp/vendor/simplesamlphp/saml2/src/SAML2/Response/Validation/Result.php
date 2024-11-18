<?php

declare(strict_types=1);

namespace SAML2\Response\Validation;

use SAML2\Exception\InvalidArgumentException;

/**
 * Simple Result object
 */
class Result
{
    /**
     * @var array
     */
    private $errors = [];


    /**
     * @param string $message
     * @throws InvalidArgumentException
     * @return void
     */
    public function addError(string $message) : void
    {
        $this->errors[] = $message;
    }


    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return empty($this->errors);
    }


    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
