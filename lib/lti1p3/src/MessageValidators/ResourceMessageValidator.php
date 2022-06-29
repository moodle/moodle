<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Interfaces\IMessageValidator;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class ResourceMessageValidator implements IMessageValidator
{
    public function canValidate(array $jwtBody)
    {
        return $jwtBody[LtiConstants::MESSAGE_TYPE] === 'LtiResourceLinkRequest';
    }

    public function validate(array $jwtBody)
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
        if (empty($jwtBody[LtiConstants::RESOURCE_LINK]['id'])) {
            throw new LtiException('Missing Resource Link Id');
        }

        return true;
    }
}
