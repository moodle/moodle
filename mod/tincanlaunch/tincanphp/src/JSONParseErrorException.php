<?php
/*
    Copyright 2016 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

class JSONParseErrorException extends \Exception
{
    private static $format = 'Invalid JSON "%s": %s (%d)';

    private $malformedValue;
    private $jsonErrorNumber;
    private $jsonErrorMessage;

    public function __construct($malformedValue, $jsonErrorNumber, $jsonErrorMessage, \Exception $previous = null) {
        $this->malformedValue   = $malformedValue;
        $this->jsonErrorNumber  = (int) $jsonErrorNumber;
        $this->jsonErrorMessage = $jsonErrorMessage;

        $message = sprintf(self::$format, $malformedValue, $jsonErrorMessage, $jsonErrorNumber);

        parent::__construct($message, $jsonErrorNumber, $previous);
    }

    public function malformedValue() {
        return $this->malformedValue;
    }

    public function jsonErrorNumber() {
        return $this->jsonErrorNumber;
    }

    public function jsonErrorMessage() {
        return $this->jsonErrorMessage;
    }
}
