<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

/**
 * Class for creating exceptions from assertion failures.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class Assertion extends Exception
{
    /**
     * The assertion which failed, or null if only an expression was passed to the
     * assert-function.
     */
    private $assertion;


    /**
     * Constructor for the assertion exception.
     *
     * Should only be called from the onAssertion handler.
     *
     * @param string|null $assertion  The assertion which failed, or null if the assert-function was
     *                                given an expression.
     */
    public function __construct($assertion = null)
    {
        assert($assertion === null || is_string($assertion));

        $msg = 'Assertion failed: ' . var_export($assertion, true);
        parent::__construct($msg);

        $this->assertion = $assertion;
    }


    /**
     * Retrieve the assertion which failed.
     *
     * @return string|null  The assertion which failed, or null if the assert-function was called with an expression.
     */
    public function getAssertion()
    {
        return $this->assertion;
    }


    /**
     * Install this assertion handler.
     *
     * This function will register this assertion handler. If will not enable assertions if they are
     * disabled.
     * @return void
     */
    public static function installHandler()
    {

        assert_options(ASSERT_WARNING, 0);
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            assert_options(ASSERT_QUIET_EVAL, 0);
        } else {
            ini_set('assert.exception', '0');
            ini_set('assert.warning', '1');
        }
        assert_options(ASSERT_CALLBACK, [Assertion::class, 'onAssertion']);
    }


    /**
     * Handle assertion.
     *
     * This function handles an assertion.
     *
     * @param string $file  The file assert was called from.
     * @param int $line  The line assert was called from.
     * @param mixed $message  The expression which was passed to the assert-function.
     * @return void
     */
    public static function onAssertion($file, $line, $message)
    {

        if (!empty($message)) {
            $exception = new self($message);
        } else {
            $exception = new self();
        }

        $exception->logError();
    }
}
