<?php

declare(strict_types=1);

namespace Phpml\Exception;

class NormalizerException extends \Exception
{
    /**
     * @return NormalizerException
     */
    public static function unknownNorm()
    {
        return new self('Unknown norm supplied.');
    }
}
