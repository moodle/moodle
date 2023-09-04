<?php

declare(strict_types=1);

namespace SAML2\Certificate\Exception;

use SAML2\Exception\Throwable;

/**
 * Named Exception for what the name describes. This should not occur, as it has to be
 * caught on the configuration side.
 */
class InvalidCertificateStructureException extends \DomainException implements
    Throwable
{
}
