<?php

namespace block_learnerscript\Spout\Writer\Exception\Border;

use block_learnerscript\Spout\Writer\Exception\WriterException;
use block_learnerscript\Spout\Writer\Style\BorderPart;

class InvalidNameException extends WriterException
{
    public function __construct($name)
    {
        $msg = '%s is not a valid name identifier for a border. Valid identifiers are: %s.';

        parent::__construct(sprintf($msg, $name, implode(',', BorderPart::getAllowedNames())));
    }
}
