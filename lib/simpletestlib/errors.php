<?php
    /**
     *	base include file for SimpleTest
     *	@package	SimpleTest
     *	@subpackage	UnitTester
     *	@version	$Id$
     */

    /** @ignore - PHP5 compatibility fix. */
    if (! defined('E_STRICT')) {
        define('E_STRICT', 2048);
    }

    /**#@+
     * Includes SimpleTest files.
     */
    require_once(dirname(__FILE__) . '/invoker.php');
    require_once(dirname(__FILE__) . '/test_case.php');
    require_once(dirname(__FILE__) . '/expectation.php');

    /**
     *    Extension that traps errors into an error queue.
	 *	  @package SimpleTest
	 *	  @subpackage UnitTester
     */
    class SimpleErrorTrappingInvoker extends SimpleInvokerDecorator {

        /**
         *    Stores the invoker to wrap.
         *    @param SimpleInvoker $invoker  Test method runner.
         */
        function SimpleErrorTrappingInvoker(&$invoker) {
            $this->SimpleInvokerDecorator($invoker);
        }

        /**
         *    Invokes a test method and dispatches any
         *    untrapped errors. Called back from
         *    the visiting runner.
         *    @param string $method    Test method to call.
         *    @access public
         */
        function invoke($method) {
            $context = &SimpleTest::getContext();
            $queue = &$context->get('SimpleErrorQueue');
            $queue->setTestCase($this->GetTestCase());
            set_error_handler('SimpleTestErrorHandler');
            parent::invoke($method);
            while (list($severity, $message, $file, $line) = $queue->extract()) {
                $severity = SimpleErrorQueue::getSeverityAsString($severity);
                $test = &$this->getTestCase();
                $test->error($severity, $message, $file, $line);
            }
            restore_error_handler();
        }
    }

    /**
     *    Singleton error queue used to record trapped
     *    errors.
	 *	  @package	SimpleTest
	 *	  @subpackage	UnitTester
     */
    class SimpleErrorQueue {
        var $_queue;
        var $_expectation_queue;
        var $_test;

        /**
         *    Starts with an empty queue.
         */
        function SimpleErrorQueue() {
            $this->clear();
        }

        /**
         *    Sets the currently running test case.
         *    @param SimpleTestCase $test    Test case to send messages to.
         *    @access public
         */
        function setTestCase(&$test) {
            $this->_test = &$test;
        }

        /**
         *    Adds an error to the front of the queue.
         *    @param integer $severity       PHP error code.
         *    @param string $content         Text of error.
         *    @param string $filename        File error occoured in.
         *    @param integer $line           Line number of error.
         *    @access public
         */
        function add($severity, $content, $filename, $line) {
			$content = str_replace('%', '%%', $content);
            if (count($this->_expectation_queue)) {
                $this->_testLatestError($severity, $content, $filename, $line);
            } else {
                array_push(
                        $this->_queue,
                        array($severity, $content, $filename, $line));
            }
        }

        /**
         *    Tests the error against the most recent expected
         *    error.
         *    @param integer $severity       PHP error code.
         *    @param string $content         Text of error.
         *    @param string $filename        File error occoured in.
         *    @param integer $line           Line number of error.
         *    @access private
         */
        function _testLatestError($severity, $content, $filename, $line) {
            list($expected, $message) = array_shift($this->_expectation_queue);
            $severity = $this->getSeverityAsString($severity);
            $is_match = $this->_test->assert(
                    $expected,
                    $content,
                    sprintf($message, "%s -> PHP error [$content] severity [$severity] in [$filename] line [$line]"));
            if (! $is_match) {
                $this->_test->error($severity, $content, $filename, $line);
            }
        }

        /**
         *    Pulls the earliest error from the queue.
         *    @return     False if none, or a list of error
         *                information. Elements are: severity
         *                as the PHP error code, the error message,
         *                the file with the error, the line number
         *                and a list of PHP super global arrays.
         *    @access public
         */
        function extract() {
            if (count($this->_queue)) {
                return array_shift($this->_queue);
            }
            return false;
        }

        /**
         *    Discards the contents of the error queue.
         *    @access public
         */
        function clear() {
            $this->_queue = array();
            $this->_expectation_queue = array();
        }

        /**
         *    @deprecated
         */
        function assertNoErrors($message) {
            return $this->_test->assert(
					new TrueExpectation(),
                    count($this->_queue) == 0,
                    sprintf($message, 'Should be no errors'));
        }

        /**
         *    @deprecated
         */
        function assertError($expected, $message) {
            if (count($this->_queue) == 0) {
                $this->_test->fail(sprintf($message, 'Expected error not found'));
                return false;
            }
            list($severity, $content, $file, $line) = $this->extract();
            $severity = $this->getSeverityAsString($severity);
            return $this->_test->assert(
                    $expected,
                    $content,
                    sprintf($message, "Expected PHP error [$content] severity [$severity] in [$file] line [$line]"));
        }

        /**
         *    Sets up an expectation of an error. If this is
         *    not fulfilled at the end of the test, a failure
         *    will occour. If the error does happen, then this
         *    will cancel it out and send a pass message.
         *    @param SimpleExpectation $expected    Expected error match.
         *    @param string $message                Message to display.
         *    @access public
         */
        function expectError($expected, $message) {
            array_push(
                    $this->_expectation_queue,
                    array($expected, $message));
        }

        /**
         *    Converts an error code into it's string
         *    representation.
         *    @param $severity  PHP integer error code.
         *    @return           String version of error code.
         *    @access public
         *    @static
         */
        function getSeverityAsString($severity) {
            static $map = array(
                    E_STRICT => 'E_STRICT',
                    E_ERROR => 'E_ERROR',
                    E_WARNING => 'E_WARNING',
                    E_PARSE => 'E_PARSE',
                    E_NOTICE => 'E_NOTICE',
                    E_CORE_ERROR => 'E_CORE_ERROR',
                    E_CORE_WARNING => 'E_CORE_WARNING',
                    E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                    E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                    E_USER_ERROR => 'E_USER_ERROR',
                    E_USER_WARNING => 'E_USER_WARNING',
                    E_USER_NOTICE => 'E_USER_NOTICE');
            // Moodle additions, to prevent notices in PHP5.
            if (defined('E_RECOVERABLE_ERROR')) {
                $map[E_RECOVERABLE_ERROR] = 'E_RECOVERABLE_ERROR';
            }
            if (defined('E_DEPRECATED')) {
                $map[E_DEPRECATED] = 'E_DEPRECATED';
            }
            // End Moodle additions.
            return $map[$severity];
        }
    }

    /**
     *    Error handler that simply stashes any errors into the global
     *    error queue. Simulates the existing behaviour with respect to
     *    logging errors, but this feature may be removed in future.
     *    @param $severity        PHP error code.
     *    @param $message         Text of error.
     *    @param $filename        File error occoured in.
     *    @param $line            Line number of error.
     *    @param $super_globals   Hash of PHP super global arrays.
     *    @static
     *    @access public
     */
    function SimpleTestErrorHandler($severity, $message, $filename, $line, $super_globals) {
        if ($severity = $severity & error_reporting()) {
            restore_error_handler();
            if (ini_get('log_errors')) {
                $label = SimpleErrorQueue::getSeverityAsString($severity);
                error_log("$label: $message in $filename on line $line");
            }
            $context = &SimpleTest::getContext();
            $queue = &$context->get('SimpleErrorQueue');
            $queue->add($severity, $message, $filename, $line);
            set_error_handler('SimpleTestErrorHandler');
        }
    }
?>