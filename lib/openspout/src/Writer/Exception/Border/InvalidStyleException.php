<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Exception\Border;

use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Writer\Exception\WriterException;

final class InvalidStyleException extends WriterException
{
    public function __construct(string $name)
    {
        $msg = '%s is not a valid style identifier for a border. Valid identifiers are: %s.';

        parent::__construct(\sprintf($msg, $name, implode(',', BorderPart::allowedStyles)));
    }
}
