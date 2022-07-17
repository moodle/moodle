<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Variables;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedPredefinedGlobalVariables sniff.
 *
 * @group removedPredefinedGlobalVariables
 * @group variables
 *
 * @covers \PHPCompatibility\Sniffs\Variables\RemovedPredefinedGlobalVariablesSniff
 *
 * @since 5.5   Introduced as LongArraysSniffTest.
 * @since 7.0   RemovedVariablesSniffTest.
 * @since 7.1.3 Merged to one sniff & test.
 */
class RemovedPredefinedGlobalVariablesUnitTest extends BaseSniffTest
{

    /**
     * testRemovedGlobalVariables
     *
     * @dataProvider dataRemovedGlobalVariables
     *
     * @param string $varName      The name of the removed global variable.
     * @param string $deprecatedIn The PHP version in which the global variable was deprecated.
     * @param string $removedIn    The PHP version in which the global variable was removed.
     * @param array  $lines        The line numbers in the test file which apply to this variable.
     * @param string $alternative  What to use as an alternative.
     * @param string $okVersion    A PHP version in which the global variable was ok to be used.
     *
     * @return void
     */
    public function testRemovedGlobalVariables($varName, $deprecatedIn, $removedIn, $lines, $alternative, $okVersion)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $file  = $this->sniffFile(__FILE__, $deprecatedIn);
        $error = "Global variable '$" . $varName . "' is deprecated since PHP {$deprecatedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $file  = $this->sniffFile(__FILE__, $removedIn);
        $error = "Global variable '$" . $varName . "' is deprecated since PHP {$deprecatedIn} and removed since PHP {$removedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testRemovedGlobalVariables()
     *
     * @return array
     */
    public function dataRemovedGlobalVariables()
    {
        return array(
            array('HTTP_POST_VARS', '5.3', '5.4', array(9, 31, 71, 91), '$_POST', '5.2'),
            array('HTTP_GET_VARS', '5.3', '5.4', array(10, 32, 51, 72), '$_GET', '5.2'),
            array('HTTP_ENV_VARS', '5.3', '5.4', array(11, 33, 52, 73), '$_ENV', '5.2'),
            array('HTTP_SERVER_VARS', '5.3', '5.4', array(12, 34, 74, 92), '$_SERVER', '5.2'),
            array('HTTP_COOKIE_VARS', '5.3', '5.4', array(13, 35, 75), '$_COOKIE', '5.2'),
            array('HTTP_SESSION_VARS', '5.3', '5.4', array(14, 36, 76, 93), '$_SESSION', '5.2'),
            array('HTTP_POST_FILES', '5.3', '5.4', array(15, 37, 77), '$_FILES', '5.2'),

            array('HTTP_RAW_POST_DATA', '5.6', '7.0', array(3, 38, 53, 78), 'php://input', '5.5'),
        );
    }


    /**
     * testDeprecatedPHPErrorMsg
     *
     * @dataProvider dataDeprecatedPHPErrorMsg
     *
     * @param array $line The line number in the test file where a warning is expected.
     *
     * @return void
     */
    public function testDeprecatedPHPErrorMsg($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);

        $file  = $this->sniffFile(__FILE__, '7.2');
        $error = 'The variable \'$php_errormsg\' is deprecated since PHP 7.2; Use error_get_last() instead';
        $this->assertWarning($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedPHPErrorMsg()
     *
     * @return array
     */
    public function dataDeprecatedPHPErrorMsg()
    {
        return array(
            array(101),
            array(110),
            array(111),
            array(126),
            array(127),
            array(140),
            array(141),
            array(151), // False positive!
            array(156),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest deprecation.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            // Variable names are case-sensitive.
            array(5),
            array(6),

            // Issue #268 - class properties named after long array variables.
            array(20),
            array(21),
            array(22),
            array(23),
            array(24),
            array(25),
            array(26),
            array(27),

            array(41),
            array(42),
            array(43),
            array(44),
            array(45),
            array(46),
            array(47),
            array(48),

            // Issue #333 - class properties named after long array variables in anonymous classes.
            array(60),
            array(61),
            array(62),
            array(63),
            array(64),
            array(65),
            array(66),
            array(67),

            array(81),
            array(82),
            array(83),
            array(84),
            array(85),
            array(86),
            array(87),
            array(88),

            // PHP 7.2 deprecated $php_errormsg.
            array(106),
            array(114),
            array(116),
            array(118),
            array(121),
            array(123),
            array(130),
            array(132),
            array(133),
            array(134),
            array(143),
            array(145),
            array(146),
            array(147),
            array(150),
            array(165),
            array(169),

        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.2'); // Low version below the first deprecation.
        $this->assertNoViolation($file);
    }
}
