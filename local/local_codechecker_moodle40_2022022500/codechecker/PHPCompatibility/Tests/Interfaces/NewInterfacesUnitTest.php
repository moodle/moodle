<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Interfaces;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewInterfaces sniff.
 *
 * @group newInterfaces
 * @group interfaces
 *
 * @covers \PHPCompatibility\Sniffs\Interfaces\NewInterfacesSniff
 * @covers \PHPCompatibility\Sniff::getReturnTypeHintName
 * @covers \PHPCompatibility\Sniff::getReturnTypeHintToken
 * @covers \PHPCompatibility\Sniff::getTypeHintsFromFunctionDeclaration
 *
 * @since 7.0.3
 */
class NewInterfacesUnitTest extends BaseSniffTest
{

    /**
     * testNewInterface
     *
     * @dataProvider dataNewInterface
     *
     * @param string $interfaceName     Interface name.
     * @param string $lastVersionBefore The PHP version just *before* the class was introduced.
     * @param array  $lines             The line numbers in the test file which apply to this class.
     * @param string $okVersion         A PHP version in which the class was ok to be used.
     * @param string $testVersion       Optional. A PHP version in which to test for the error if different
     *                                  from the $lastVersionBefore.
     *
     * @return void
     */
    public function testNewInterface($interfaceName, $lastVersionBefore, $lines, $okVersion, $testVersion = null)
    {
        $errorVersion = (isset($testVersion)) ? $testVersion : $lastVersionBefore;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The built-in interface {$interfaceName} is not present in PHP version {$lastVersionBefore} or earlier";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewInterface()
     *
     * @return array
     */
    public function dataNewInterface()
    {
        return array(
            array('Reflector', '4.4', array(75), '5.0'),
            array('Traversable', '4.4', array(35, 50, 60, 71, 79), '5.0'),
            array('Countable', '5.0', array(3, 17, 41), '5.1'),
            array('OuterIterator', '5.0', array(4, 42, 65), '5.1'),
            array('RecursiveIterator', '5.0', array(5, 43, 65), '5.1'),
            array('SeekableIterator', '5.0', array(6, 17, 28, 44), '5.1'),
            array('Serializable', '5.0', array(7, 29, 45, 55, 70), '5.1'),
            array('SplObserver', '5.0', array(11, 46, 65), '5.1'),
            array('SplSubject', '5.0', array(12, 17, 47, 69), '5.1'),
            array('JsonSerializable', '5.3', array(13, 48), '5.4'),
            array('SessionHandlerInterface', '5.3', array(14, 49), '5.4'),
            array('DateTimeInterface', '5.4', array(36, 51, 61, 80), '5.5'),
            array('SessionIdInterface', '5.5.0', array(89), '5.6', '5.5'),
            array('Throwable', '5.6', array(37, 52, 62), '7.0'),
            array('SessionUpdateTimestampHandlerInterface', '5.6', array(90), '7.0'),
        );
    }

    /**
     * Test unsupported methods
     *
     * @dataProvider dataUnsupportedMethods
     *
     * @param array  $line       The line number.
     * @param string $methodName The name of the unsupported method which should be detected.
     *
     * @return void
     */
    public function testUnsupportedMethods($line, $methodName)
    {
        $file = $this->sniffFile(__FILE__, '5.1'); // Version in which the Serializable interface was introduced.
        $this->assertError($file, $line, "Classes that implement interface Serializable do not support the method {$methodName}(). See https://www.php.net/serializable");
    }

    /**
     * Data provider.
     *
     * @see testUnsupportedMethods()
     *
     * @return array
     */
    public function dataUnsupportedMethods()
    {
        return array(
            array(8, '__sleep'),
            array(9, '__wakeup'),
            array(30, '__sleep'),
            array(31, '__wakeup'),
        );
    }


    /**
     * Test interfaces in different cases.
     *
     * @return void
     */
    public function testCaseInsensitive()
    {
        $file = $this->sniffFile(__FILE__, '5.0');
        $this->assertError($file, 20, 'The built-in interface COUNTABLE is not present in PHP version 5.0 or earlier');
        $this->assertError($file, 21, 'The built-in interface countable is not present in PHP version 5.0 or earlier');
        $this->assertError($file, 78, 'The built-in interface COUNTABLE is not present in PHP version 5.0 or earlier');
        $this->assertError($file, 81, 'The built-in interface throwable is not present in PHP version 5.6 or earlier');
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
        $file = $this->sniffFile(__FILE__, '4.4'); // Low version below the first addition.
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
            array(24),
            array(25),
            array(56),
            array(57),
            array(72),
            array(84),
            array(85),
            array(86),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw an error
     * on invalid use of some magic methods for the Serializable Interface.
     */
}
