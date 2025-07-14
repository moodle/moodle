<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Interfaces\IMessageValidator;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

abstract class AbstractMessageValidator implements IMessageValidator
{
    abstract public static function getMessageType(): string;

    public static function canValidate(array $jwtBody): bool
    {
        return $jwtBody[LtiConstants::MESSAGE_TYPE] === static::getMessageType();
    }

    abstract public static function validate(array $jwtBody): void;

    /**
     * @throws LtiException
     */
    public static function validateGenericMessage(array $jwtBody): void
    {
        if (empty($jwtBody['sub'])) {
            throw new LtiException('Must have a user (sub)');
        }
        if (!isset($jwtBody[LtiConstants::VERSION])) {
            throw new LtiException('Missing LTI Version');
        }
        if ($jwtBody[LtiConstants::VERSION] !== LtiConstants::V1_3) {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwtBody[LtiConstants::ROLES])) {
            throw new LtiException('Missing Roles Claim');
        }
    }
}
