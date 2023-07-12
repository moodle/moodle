<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

/**
 * Custom Exception to throw to terminate a TestCase.
 */
class ExitTestException extends \Exception
{
    /** @var array */
    private $testResult;


    /**
     * @param array $testResult
     * @return void
     */
    public function __construct(array $testResult)
    {
        parent::__construct("ExitTestException", 0, null);
        $this->testResult = $testResult;
    }


    /**
     * @return array
     */
    public function getTestResult(): array
    {
        return $this->testResult;
    }
}
